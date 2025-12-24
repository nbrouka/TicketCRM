<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_ticket_creation_process()
    {
        // Test the complete ticket creation process via API
        $requestData = [
            'theme' => 'Test Ticket Creation',
            'text' => 'This is a test ticket for creation process',
            'name' => 'Test Customer',
            'phone' => '+1234567890',
            'email' => 'test.customer@example.com',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/tickets', $requestData);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Ticket created successfully',
        ]);

        $responseData = $response->json();

        $this->assertDatabaseHas('tickets', [
            'id' => $responseData['ticket']['id'],
            'theme' => 'Test Ticket Creation',
            'text' => 'This is a test ticket for creation process',
            'status' => TicketStatus::NEW->value,
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'Test Customer',
            'email' => 'test.customer@example.com',
        ]);
    }

    public function test_ticket_search_functionality()
    {
        $customer = Customer::factory()->create();

        $ticket1 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Urgent Server Issue',
            'text' => 'The main server is down',
        ]);

        $ticket2 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Normal Feature Request',
            'text' => 'Request for new feature',
        ]);

        $ticket3 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Another Issue',
            'text' => 'Different problem description',
        ]);

        // Search by theme
        $response = $this->actingAs($this->user)
            ->get('/tickets?search=server');

        $response->assertStatus(200);
        $tickets = $response->viewData('tickets');
        $this->assertCount(1, $tickets);
        $this->assertEquals($ticket1->id, $tickets->first()->id);

        // Search by text content
        $response = $this->actingAs($this->user)
            ->get('/tickets?search=feature');

        $response->assertStatus(200);
        $tickets = $response->viewData('tickets');
        $this->assertCount(1, $tickets);
        $this->assertEquals($ticket2->id, $tickets->first()->id);
    }

    public function test_ticket_filter_by_status()
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

        $ticket3 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::DONE,
        ]);

        // Filter by NEW status
        $response = $this->actingAs($this->user)
            ->get('/tickets?status='.TicketStatus::NEW->value);

        $response->assertStatus(200);
        $tickets = $response->viewData('tickets');
        $this->assertCount(1, $tickets);
        $this->assertEquals($ticket1->id, $tickets->first()->id);

        // Filter by IN_PROGRESS status
        $response = $this->actingAs($this->user)
            ->get('/tickets?status='.TicketStatus::IN_PROGRESS->value);

        $response->assertStatus(200);
        $tickets = $response->viewData('tickets');
        $this->assertCount(1, $tickets);
        $this->assertEquals($ticket2->id, $tickets->first()->id);
    }

    public function test_ticket_pagination()
    {
        $customer = Customer::factory()->create();
        Ticket::factory()->count(20)->create(['customer_id' => $customer->id]);

        // First page should have 15 items (default per page)
        $response = $this->actingAs($this->user)
            ->get('/tickets');

        $response->assertStatus(200);
        $tickets = $response->viewData('tickets');
        $this->assertCount(15, $tickets);
        $this->assertTrue($tickets->hasMorePages());
    }

    public function test_ticket_with_files_creation_and_download()
    {
        Storage::fake('public');

        $file = File::image('screenshot.jpg');

        $requestData = [
            'theme' => 'Ticket with Attachment',
            'text' => 'This ticket has an attachment',
            'name' => 'Test Customer',
            'phone' => '+1234567890',
            'email' => 'test@example.com',
            'files' => [$file],
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/tickets', $requestData);

        $response->assertStatus(201);
        $responseData = $response->json();

        $ticket = Ticket::find($responseData['ticket']['id']);

        // Verify file was attached
        $this->assertCount(1, $ticket->getMedia('files'));

        // Now test downloading the file
        $media = $ticket->getMedia('files')->first();

        $downloadResponse = $this->actingAs($this->user)
            ->get("/tickets/{$ticket->id}/files/{$media->id}");

        $downloadResponse->assertStatus(200);
    }

    public function test_customer_ticket_relationship()
    {
        $customer = Customer::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $ticket1 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'First Ticket',
        ]);

        $ticket2 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Second Ticket',
        ]);

        $response = $this->actingAs($this->user)
            ->get("/tickets/{$ticket1->id}");

        $response->assertStatus(200);
        $ticket = $response->viewData('ticket');

        $this->assertEquals($customer->id, $ticket->customer->id);
        $this->assertEquals('John Doe', $ticket->customer->name);
        $this->assertEquals('john@example.com', $ticket->customer->email);
    }

    public function test_ticket_sorting()
    {
        $customer = Customer::factory()->create();

        $ticket1 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'First Ticket',
            'created_at' => now()->subDays(2),
        ]);

        $ticket2 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Second Ticket',
            'created_at' => now()->subDay(),
        ]);

        $ticket3 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Third Ticket',
            'created_at' => now(),
        ]);

        // Tickets should be sorted by created_at desc (most recent first)
        $response = $this->actingAs($this->user)
            ->get('/tickets');

        $response->assertStatus(200);
        $tickets = $response->viewData('tickets');

        // The first ticket in the collection should be the most recently created
        $this->assertEquals($ticket3->id, $tickets->first()->id);
    }
}
