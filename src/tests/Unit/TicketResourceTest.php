<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\TicketStatus;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Models\Customer;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Testing\File;
use Tests\TestCase;

class TicketResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_resource_transforms_data_correctly()
    {
        $customer = Customer::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
        ]);

        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Test Ticket Resource',
            'text' => 'Test ticket for resource transformation',
            'status' => TicketStatus::NEW,
        ]);

        $ticket->load('customer'); // Load the customer relation for the resource
        $resource = new TicketResource($ticket);
        $data = $resource->toArray(new Request);

        $this->assertEquals($ticket->id, $data['id']);
        $this->assertEquals($ticket->theme, $data['theme']);
        $this->assertEquals($ticket->text, $data['text']);
        $this->assertEquals($ticket->status->value, $data['status']);
        $this->assertEquals($ticket->created_at->format('Y-m-d H:i:s'), Carbon::parse($data['created_at'])->format('Y-m-d H:i:s'));
        $this->assertEquals($ticket->updated_at->format('Y-m-d H:i:s'), Carbon::parse($data['updated_at'])->format('Y-m-d H:i:s'));

        // Check customer data
        $this->assertEquals($customer->id, $data['customer']['id']);
        $this->assertEquals($customer->name, $data['customer']['name']);
        $this->assertEquals($customer->email, $data['customer']['email']);
        $this->assertEquals($customer->phone, $data['customer']['phone']);
    }

    public function test_ticket_resource_includes_media_when_available()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);

        // Add some media to the ticket
        $file = File::create('test.txt', 1, 'text/plain');
        $media = $ticket->addMedia($file)
            ->preservingOriginal()
            ->toMediaCollection('files');

        // Refresh and load the media relation properly
        $ticket->load('media');

        // Reload the ticket from the database with the media relation to ensure it's properly marked as loaded
        $ticketWithMedia = Ticket::with('media')->find($ticket->id);

        $resource = new TicketResource($ticketWithMedia);
        $data = $resource->toArray(new Request);

        $this->assertArrayHasKey('files', $data);
        $this->assertCount(1, $data['files']);
        $this->assertEquals($media->file_name, $data['files'][0]['file_name']);
    }

    public function test_ticket_resource_handles_null_date_answer()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'date_answer' => null,
        ]);

        $resource = new TicketResource($ticket);
        $data = $resource->toArray(new Request);

        $this->assertNull($data['date_answer']);
    }

    public function test_ticket_resource_handles_set_date_answer()
    {
        $customer = Customer::factory()->create();
        $date = now();

        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'date_answer' => $date,
        ]);

        $resource = new TicketResource($ticket);
        $data = $resource->toArray(new Request);

        $this->assertEquals($date->format('Y-m-d H:i:s'), Carbon::parse($data['date_answer'])->format('Y-m-d H:i:s'));
    }

    public function test_ticket_collection_transforms_multiple_tickets()
    {
        $customer = Customer::factory()->create();

        $ticket1 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'First Ticket',
            'status' => TicketStatus::NEW,
        ]);

        $ticket2 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Second Ticket',
            'status' => TicketStatus::IN_PROGRESS,
        ]);

        $collection = collect([$ticket1, $ticket2]);
        $ticketCollection = new TicketCollection($collection);

        $data = $ticketCollection->toArray(new Request);

        $this->assertCount(2, $data['data']);
        $this->assertEquals($ticket1->theme, $data['data'][0]['theme']);
        $this->assertEquals($ticket2->theme, $data['data'][1]['theme']);
    }

    public function test_ticket_collection_with_pagination()
    {
        $customer = Customer::factory()->create();
        $tickets = Ticket::factory()->count(5)->create(['customer_id' => $customer->id]);

        // Create a paginated collection
        $paginatedTickets = Ticket::paginate(3);
        $ticketCollection = new TicketCollection($paginatedTickets);

        $data = $ticketCollection->toArray(new Request);

        // Check that it's a proper collection with pagination metadata
        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);
        $this->assertLessThanOrEqual(3, count($data['data'])); // Since we paginated by 3
    }

    public function test_ticket_resource_serializes_to_array_properly()
    {
        $customer = Customer::factory()->create([
            'name' => 'Test Customer',
        ]);

        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Serialization Test',
            'status' => TicketStatus::DONE,
        ]);

        $resource = new TicketResource($ticket->load('customer'));
        $array = $resource->toArray(new Request);

        // Verify the structure of the serialized data
        $expectedKeys = ['id', 'theme', 'text', 'status', 'date_answer', 'created_at', 'updated_at', 'customer'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }

    public function test_ticket_resource_status_serialization()
    {
        $customer = Customer::factory()->create();

        foreach (TicketStatus::cases() as $status) {
            $ticket = Ticket::factory()->create([
                'customer_id' => $customer->id,
                'status' => $status,
            ]);

            $resource = new TicketResource($ticket);
            $data = $resource->toArray(new Request);

            $this->assertEquals($status->value, $data['status']);
        }
    }
}
