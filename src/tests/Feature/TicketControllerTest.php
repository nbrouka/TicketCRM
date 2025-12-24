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

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_tickets_index()
    {
        $response = $this->get('/tickets');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_tickets_index()
    {
        $response = $this->actingAs($this->user)->get('/tickets');

        $response->assertStatus(200);
        $response->assertViewIs('tickets.index');
    }

    public function test_tickets_index_shows_tickets()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($this->user)->get('/tickets');

        $response->assertStatus(200);
        $response->assertViewHas('tickets');
        $response->assertSee($ticket->theme);
    }

    public function test_authenticated_user_can_view_ticket()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($this->user)->get("/tickets/{$ticket->id}");

        $response->assertStatus(200);
        $response->assertViewIs('tickets.show');
        $response->assertViewHas('ticket');
        $response->assertSee($ticket->theme);
        $response->assertSee($ticket->text);
    }

    public function test_guest_cannot_view_ticket()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);

        $response = $this->get("/tickets/{$ticket->id}");

        $response->assertRedirect('/login');
    }

    public function test_update_ticket_status_updates_status()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::NEW,
        ]);

        $response = $this->actingAs($this->user)
            ->put("/tickets/{$ticket->id}/status", [
                'status' => TicketStatus::IN_PROGRESS->value,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => TicketStatus::IN_PROGRESS->value,
        ]);
    }

    public function test_update_ticket_status_returns_redirect_response()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::NEW,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/tickets/{$ticket->id}/status", [
                'status' => TicketStatus::IN_PROGRESS->value,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => TicketStatus::IN_PROGRESS->value,
        ]);
    }

    public function test_update_ticket_status_fails_with_invalid_status()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::NEW,
        ]);

        $response = $this->actingAs($this->user)
            ->put("/tickets/{$ticket->id}/status", [
                'status' => 'invalid_status',
            ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_download_ticket_file()
    {
        Storage::fake('public');

        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);

        // Create a test file and attach to ticket
        $file = File::create('test.txt', 1, 'text/plain');
        $media = $ticket->addMedia($file)
            ->toMediaCollection('files');

        // Put content in storage
        Storage::disk('public')->put("{$media->id}/{$media->file_name}", 'Test file content');

        $response = $this->actingAs($this->user)
            ->get("/tickets/{$ticket->id}/files/{$media->id}");

        $response->assertStatus(200);
    }

    public function test_download_ticket_file_fails_for_nonexistent_file()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($this->user)
            ->get("/tickets/{$ticket->id}/files/999");

        $response->assertStatus(404);
    }

    public function test_tickets_index_applies_filters()
    {
        $customer = Customer::factory()->create();

        $ticket1 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::NEW,
            'theme' => 'Urgent ticket',
        ]);

        $ticket2 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::IN_PROGRESS,
            'theme' => 'Normal ticket',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/tickets?status='.TicketStatus::NEW->value);

        $response->assertStatus(200);
        $tickets = $response->viewData('tickets');

        $this->assertCount(1, $tickets);
        $this->assertEquals($ticket1->id, $tickets->first()->id);
    }

    public function test_tickets_index_search_functionality()
    {
        $customer = Customer::factory()->create();

        $ticket1 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Urgent issue',
            'text' => 'This is an urgent ticket',
        ]);

        $ticket2 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Normal issue',
            'text' => 'This is a normal ticket',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/tickets?search=urgent');

        $response->assertStatus(200);
        $tickets = $response->viewData('tickets');

        $this->assertCount(1, $tickets);
        $this->assertEquals($ticket1->id, $tickets->first()->id);
    }
}
