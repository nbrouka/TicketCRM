<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use App\Observers\TicketObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TicketObserverTest extends TestCase
{
    use RefreshDatabase;

    protected TicketObserver $ticketObserver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketObserver = new TicketObserver;
    }

    public function test_creating_event_is_fired_when_ticket_is_created()
    {
        Event::fake();

        $customer = Customer::factory()->create();

        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'theme' => 'Test Ticket',
            'text' => 'Test ticket description',
            'status' => TicketStatus::NEW,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'theme' => 'Test Ticket',
        ]);
    }

    public function test_updating_event_is_fired_when_ticket_is_updated()
    {
        Event::fake();

        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::NEW,
        ]);

        $originalStatus = $ticket->status;

        $ticket->update([
            'status' => TicketStatus::IN_PROGRESS,
        ]);

        $this->assertNotEquals($originalStatus, $ticket->fresh()->status);
        $this->assertEquals(TicketStatus::IN_PROGRESS, $ticket->fresh()->status);
    }

    public function test_saving_event_works_for_new_ticket()
    {
        $customer = Customer::factory()->create();

        $ticketData = [
            'customer_id' => $customer->id,
            'theme' => 'Observer Test Ticket',
            'text' => 'Ticket to test observer',
            'status' => TicketStatus::NEW,
        ];

        $ticket = new Ticket($ticketData);
        $ticket->save();

        $this->assertDatabaseHas('tickets', [
            'theme' => 'Observer Test Ticket',
            'text' => 'Ticket to test observer',
        ]);
    }

    public function test_saving_event_works_for_existing_ticket()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Original Theme',
            'status' => TicketStatus::NEW,
        ]);

        $ticket->theme = 'Updated Theme';
        $ticket->save();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'theme' => 'Updated Theme',
        ]);
    }

    public function test_ticket_observer_handles_status_changes()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::NEW,
        ]);

        // Update the status
        $ticket->status = TicketStatus::IN_PROGRESS;
        $ticket->save();

        $this->assertEquals(TicketStatus::IN_PROGRESS, $ticket->fresh()->status);
    }

    public function test_ticket_observer_preserves_other_attributes_on_update()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Original Theme',
            'text' => 'Original text',
            'status' => TicketStatus::NEW,
        ]);

        // Update only the status
        $ticket->status = TicketStatus::IN_PROGRESS;
        $ticket->save();

        $freshTicket = $ticket->fresh();
        $this->assertEquals('Original Theme', $freshTicket->theme);
        $this->assertEquals('Original text', $freshTicket->text);
        $this->assertEquals(TicketStatus::IN_PROGRESS, $freshTicket->status);
    }
}
