<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
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

        // Skip Redis flush in testing environment to avoid connection issues
        if (app()->environment('testing')) {
            // In testing environment, clear cache to ensure clean state
            Cache::flush();
        } else {
            // Flush Redis to ensure clean state for tests
            Redis::flushall();
        }
    }

    public function test_api_store_creates_new_ticket_with_new_customer()
    {
        $requestData = [
            'theme' => 'Test API Ticket',
            'text' => 'This is a test ticket from API',
            'name' => 'API Customer',
            'phone' => '+1234567890',
            'email' => 'api.customer@example.com',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/tickets', $requestData);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Ticket created successfully',
        ]);

        $this->assertDatabaseHas('tickets', [
            'theme' => 'Test API Ticket',
            'text' => 'This is a test ticket from API',
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'API Customer',
            'email' => 'api.customer@example.com',
        ]);
    }

    public function test_api_store_creates_new_ticket_with_existing_customer()
    {
        $customer = Customer::factory()->create([
            'name' => 'Existing Customer',
            'email' => 'existing@example.com',
        ]);

        $requestData = [
            'theme' => 'Test API Ticket',
            'text' => 'This is a test ticket from API',
            'customer_id' => $customer->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/tickets', $requestData);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Ticket created successfully',
        ]);

        $this->assertDatabaseHas('tickets', [
            'theme' => 'Test API Ticket',
            'customer_id' => $customer->id,
        ]);
    }

    public function test_api_store_creates_ticket_and_finds_existing_customer_by_email()
    {
        $existingCustomer = Customer::factory()->create([
            'name' => 'Existing Customer',
            'email' => 'find-existing@example.com',
        ]);

        $requestData = [
            'theme' => 'Test API Ticket',
            'text' => 'This is a test ticket from API',
            'name' => 'Different Name',
            'phone' => '+1234567890',
            'email' => 'find-existing@example.com', // Same email as existing customer
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/tickets', $requestData);

        $response->assertStatus(201);

        $responseData = $response->json();
        $this->assertEquals($existingCustomer->id, $responseData['ticket']['customer']['id']);
    }

    public function test_api_store_creates_ticket_with_files()
    {
        Storage::fake('public');

        $file1 = File::image('ticket_image1.jpg');
        $file2 = File::image('ticket_image2.jpg');

        $requestData = [
            'theme' => 'Test API Ticket with Files',
            'text' => 'This is a test ticket with files',
            'name' => 'API Customer',
            'phone' => '+1234567890',
            'email' => 'api.customer@example.com',
            'attachments' => [$file1, $file2],
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/tickets', $requestData);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Ticket created successfully',
        ]);

        // Get the created ticket to check if files were attached
        $ticketId = $response->json('ticket.id');
        $ticket = Ticket::find($ticketId);

        $this->assertCount(2, $ticket->getMedia('files'));
    }

    public function test_api_store_fails_with_invalid_data()
    {
        $requestData = [
            'theme' => '', // Required field
            'text' => '',  // Required field
            'name' => 'API Customer',
            'phone' => '+1234567890',
            'email' => 'invalid-email', // Invalid email
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/tickets', $requestData);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'theme',
                'text',
                'email',
            ],
        ]);
    }

    public function test_api_store_fails_without_required_fields()
    {
        $requestData = [
            // Missing all required fields
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/tickets', $requestData);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'theme',
                'text',
            ],
        ]);
    }

    public function test_api_store_creates_ticket_with_customer_id_and_additional_data()
    {
        $customer = Customer::factory()->create();

        $requestData = [
            'theme' => 'Test API Ticket',
            'text' => 'This is a test ticket from API',
            'customer_id' => $customer->id,
            'name' => 'Should be ignored', // This should be ignored when customer_id is provided
            'phone' => '+1234567890',
            'email' => 'ignored@example.com',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/tickets', $requestData);

        $response->assertStatus(201);

        $responseData = $response->json();
        $ticket = Ticket::find($responseData['ticket']['id']);

        $this->assertEquals($customer->id, $ticket->customer_id);
    }

    public function test_api_store_sets_default_status_to_new()
    {
        $requestData = [
            'theme' => 'Test API Ticket',
            'text' => 'This is a test ticket from API',
            'name' => 'API Customer',
            'phone' => '+1234567890',
            'email' => 'api.customer@example.com',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/tickets', $requestData);

        $response->assertStatus(201);

        $responseData = $response->json();
        $ticket = Ticket::find($responseData['ticket']['id']);

        $this->assertEquals(TicketStatus::NEW, $ticket->status);
    }

    public function test_statistics_endpoint_returns_ticket_counts()
    {
        // Set a fixed date to ensure consistent behavior
        $now = now()->setYear(2023)->setMonth(5)->setDay(15); // Fixed date: May 15, 2023
        // Freeze time to ensure all date operations use the same fixed time
        Carbon::setTestNow($now);

        $today = $now->copy()->startOfDay();
        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();

        // Create tickets for today
        Ticket::factory()->count(3)->create(['created_at' => $today]);

        // Create tickets for this week (but not today)
        Ticket::factory()->count(4)->create(['created_at' => $weekStart->copy()->addDay()]);

        // Create tickets for this month (but not this week)
        Ticket::factory()->count(5)->create(['created_at' => $monthStart->copy()->addWeek()]);

        // Create tickets from previous month (should not be counted)
        Ticket::factory()->count(2)->create(['created_at' => $monthStart->copy()->subMonth()]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/tickets/statistics');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'day',
            'week',
            'month',
        ]);

        $data = $response->json();

        // Should have 3 tickets created today
        $this->assertEquals(3, $data['day']);

        // Should have 7 tickets created this week (3 today + 4 other days this week)
        $this->assertEquals(7, $data['week']);

        // Should have 12 tickets created this month (3 today + 4 this week + 5 other days this month)
        $this->assertEquals(12, $data['month']);

        // Reset the test time
        Carbon::setTestNow();
    }

    public function test_index_returns_tickets_with_cursor_pagination()
    {
        // Create multiple tickets
        $tickets = Ticket::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/tickets');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'path',
                'per_page',
                'next_cursor',
                'prev_cursor',
            ],
        ]);

        // Check that we get the default number of tickets (15)
        $responseData = $response->json();
        $this->assertCount(15, $responseData['data']);
        $this->assertEquals(15, $responseData['meta']['per_page']);

        // Check that next cursor exists (pagination)
        $this->assertNotNull($responseData['links']['next']);
    }

    public function test_index_returns_tickets_with_custom_limit()
    {
        // Create multiple tickets
        $tickets = Ticket::factory()->count(20)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/tickets?limit=5');

        $response->assertStatus(200);

        // Check that we get the custom number of tickets (5)
        $responseData = $response->json();
        $this->assertCount(5, $responseData['data']);
        $this->assertEquals(5, $responseData['meta']['per_page']);
    }

    public function test_index_returns_tickets_sorted_by_created_at_desc()
    {
        // Create tickets with specific created_at times
        $now = now()->setYear(2023)->setMonth(5)->setDay(15); // Fixed date: May 15, 2023
        // Freeze time to ensure consistent behavior during the test
        Carbon::setTestNow($now);

        $ticket1 = Ticket::factory()->create(['created_at' => $now->copy()->subDays(2)]);
        $ticket2 = Ticket::factory()->create(['created_at' => $now->copy()->subDays(1)]);
        $ticket3 = Ticket::factory()->create(['created_at' => $now]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/tickets?limit=10');

        $response->assertStatus(200);

        $responseData = $response->json();
        $data = $responseData['data'];

        // Check that tickets are sorted by created_at desc (newest first)
        $this->assertEquals($ticket3->id, $data[0]['id']);
        $this->assertEquals($ticket2->id, $data[1]['id']);
        $this->assertEquals($ticket1->id, $data[2]['id']);

        // Reset the test time
        Carbon::setTestNow();
    }
}
