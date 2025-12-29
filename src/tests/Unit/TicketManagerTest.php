<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\TicketStatus;
use App\Managers\TicketManager;
use App\Models\Customer;
use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Tests\TestCase;

class TicketManagerTest extends TestCase
{
    use RefreshDatabase;

    protected TicketManager $ticketManager;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the repository interface with default empty returns for methods that are not used in these tests
        $ticketRepositoryMock = $this->createMock(TicketRepositoryInterface::class);

        // Set up default expectations for the repository methods that the TicketManager constructor might use
        $ticketRepositoryMock
            ->method('getTicketsWithStatusCount')
            ->willReturn(collect([]));

        $ticketRepositoryMock
            ->method('getTicketsGroupedByMonthAndStatus')
            ->willReturn(collect([]));

        $ticketRepositoryMock
            ->method('getTicketsGroupedByDayFromRange')
            ->willReturn(collect([]));

        $this->ticketManager = new TicketManager($ticketRepositoryMock);
    }

    public function test_get_filtered_tickets_returns_paginated_results()
    {
        $customer = Customer::factory()->create();
        Ticket::factory()->count(20)->create(['customer_id' => $customer->id]);

        $request = new Request;
        $tickets = $this->ticketManager->getFilteredTickets($request);

        $this->assertEquals(TicketManager::DEFAULT_PER_PAGE, $tickets->count());
        $this->assertTrue($tickets->hasMorePages());
    }

    public function test_get_filtered_tickets_applies_filters()
    {
        $customer = Customer::factory()->create();

        $ticket1 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::NEW,
        ]);

        $ticket2 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::IN_PROGRESS,
        ]);

        $request = new Request(['status' => TicketStatus::NEW->value]);
        $tickets = $this->ticketManager->getFilteredTickets($request);

        $this->assertCount(1, $tickets);
        $this->assertEquals($ticket1->id, $tickets->first()->id);
    }

    public function test_create_ticket_creates_new_ticket()
    {
        $customer = Customer::factory()->create();

        $ticketData = [
            'customer_id' => $customer->id,
            'theme' => 'Test Ticket',
            'text' => 'Test ticket description',
            'status' => TicketStatus::NEW,
        ];

        $ticket = $this->ticketManager->createTicket($ticketData);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'theme' => 'Test Ticket',
            'text' => 'Test ticket description',
            'status' => TicketStatus::NEW->value,
        ]);
    }

    public function test_update_ticket_status_updates_status()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::NEW,
        ]);

        $result = $this->ticketManager->updateTicketStatus($ticket, TicketStatus::IN_PROGRESS->value);

        $this->assertTrue($result);
        $this->assertEquals(TicketStatus::IN_PROGRESS, $ticket->fresh()->status);
    }

    public function test_create_ticket_with_customer_using_existing_customer_id()
    {
        $customer = Customer::factory()->create();

        $ticketData = [
            'theme' => 'Test Ticket',
            'text' => 'Test ticket description',
        ];

        $ticket = $this->ticketManager->createTicketWithCustomer($ticketData, null, $customer->id);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'customer_id' => $customer->id,
            'theme' => 'Test Ticket',
        ]);
    }

    public function test_create_ticket_with_customer_creates_new_customer()
    {
        $ticketData = [
            'theme' => 'Test Ticket',
            'text' => 'Test ticket description',
        ];

        $customerData = [
            'name' => 'New Customer',
            'phone' => '+1234567890',
            'email' => 'newcustomer@example.com',
        ];

        $ticket = $this->ticketManager->createTicketWithCustomer($ticketData, $customerData);

        $this->assertDatabaseHas('customers', [
            'name' => 'New Customer',
            'email' => 'newcustomer@example.com',
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'theme' => 'Test Ticket',
        ]);

        $this->assertEquals('New Customer', $ticket->customer->name);
    }

    public function test_create_ticket_with_customer_finds_existing_customer_by_email()
    {
        $existingCustomer = Customer::factory()->create([
            'name' => 'Existing Customer',
            'email' => 'existing@example.com',
        ]);

        $ticketData = [
            'theme' => 'Test Ticket',
            'text' => 'Test ticket description',
        ];

        $customerData = [
            'name' => 'Different Name',
            'phone' => '+1234567890',
            'email' => 'existing@example.com', // Same email as existing customer
        ];

        $ticket = $this->ticketManager->createTicketWithCustomer($ticketData, $customerData);

        $this->assertEquals($existingCustomer->id, $ticket->customer->id);
        $this->assertEquals('Existing Customer', $ticket->customer->name);
    }

    public function test_create_ticket_with_customer_finds_existing_customer_by_phone()
    {
        $existingCustomer = Customer::factory()->create([
            'name' => 'Existing Customer',
            'phone' => '+9876543210',
        ]);

        $ticketData = [
            'theme' => 'Test Ticket',
            'text' => 'Test ticket description',
        ];

        $customerData = [
            'name' => 'Different Name',
            'phone' => '+9876543210', // Same phone as existing customer
            'email' => 'different@example.com',
        ];

        $ticket = $this->ticketManager->createTicketWithCustomer($ticketData, $customerData);

        $this->assertEquals($existingCustomer->id, $ticket->customer->id);
        $this->assertEquals('Existing Customer', $ticket->customer->name);
    }

    public function test_create_ticket_with_customer_throws_exception_when_no_customer_data_or_id()
    {
        $ticketData = [
            'theme' => 'Test Ticket',
            'text' => 'Test ticket description',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Either customerData or customerId must be provided');

        $this->ticketManager->createTicketWithCustomer($ticketData);
    }

    public function test_get_filtered_tickets_sorts_by_created_at_desc()
    {
        $customer = Customer::factory()->create();

        $ticket1 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'created_at' => now()->subDays(2),
        ]);

        $ticket2 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'created_at' => now()->subDay(),
        ]);

        $ticket3 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'created_at' => now(),
        ]);

        $request = new Request;
        $tickets = $this->ticketManager->getFilteredTickets($request);

        // The most recent ticket should be first
        $this->assertEquals($ticket3->id, $tickets->first()->id);
    }
}
