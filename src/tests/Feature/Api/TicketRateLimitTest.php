<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class TicketRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_rate_limit_blocks_additional_tickets_from_same_email_in_24_hours(): void
    {
        // Clear any existing rate limit data
        Redis::flushall();

        $customerData = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'phone' => '+1234567890',
            'theme' => 'Test Theme',
            'text' => 'Test ticket content',
        ];

        // First submission should succeed
        $response = $this->post('/api/feedback', $customerData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('tickets', ['theme' => 'Test Theme']);

        // Second submission from same email should be blocked
        $response = $this->post('/api/feedback', $customerData);
        $response->assertStatus(429); // Too Many Requests
        $response->assertJson([
            'message' => 'Rate limit exceeded. Please try again later.',
        ]);

        // Verify only one ticket was created
        $this->assertEquals(1, Ticket::count());
    }

    public function test_rate_limit_blocks_additional_tickets_from_same_phone_in_24_hours(): void
    {
        // Clear any existing rate limit data
        Redis::flushall();

        $customerData = [
            'email' => 'test2@example.com',
            'name' => 'Test User 2',
            'phone' => '+1234567890', // Same phone number
            'theme' => 'Test Theme 2',
            'text' => 'Test ticket content 2',
        ];

        // First submission should succeed
        $response = $this->post('/api/feedback', $customerData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('tickets', ['theme' => 'Test Theme 2']);

        // Change email but keep same phone - should still be blocked
        $customerData['email'] = 'different@example.com';
        $customerData['theme'] = 'Test Theme 3';

        $response = $this->post('/api/feedback', $customerData);
        $response->assertStatus(429); // Too Many Requests
        $response->assertJson([
            'message' => 'Rate limit exceeded. Please try again later.',
        ]);

        // Verify only one ticket was created
        $this->assertEquals(1, Ticket::count());
    }

    public function test_rate_limit_allows_different_email_and_phone(): void
    {
        // Clear any existing rate limit data
        Redis::flushall();

        $customerData1 = [
            'email' => 'user1@example.com',
            'name' => 'User 1',
            'phone' => '+111111111',
            'theme' => 'Test Theme 1',
            'text' => 'Test ticket content 1',
        ];

        $customerData2 = [
            'email' => 'user2@example.com',
            'name' => 'User 2',
            'phone' => '+22222',
            'theme' => 'Test Theme 2',
            'text' => 'Test ticket content 2',
        ];

        // First submission should succeed
        $response = $this->post('/api/feedback', $customerData1);
        $response->assertStatus(201);
        $this->assertDatabaseHas('tickets', ['theme' => 'Test Theme 1']);

        // Second submission with different email and phone should also succeed
        $response = $this->post('/api/feedback', $customerData2);
        $response->assertStatus(201);
        $this->assertDatabaseHas('tickets', ['theme' => 'Test Theme 2']);

        // Verify both tickets were created
        $this->assertEquals(2, Ticket::count());
    }

    public function test_rate_limit_works_for_authenticated_tickets(): void
    {
        // Clear any existing rate limit data
        Redis::flushall();

        // Create a user for authentication
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $ticketData = [
            'email' => 'auth@example.com',
            'name' => 'Auth User',
            'phone' => '+333333',
            'theme' => 'Authenticated Ticket',
            'text' => 'Test ticket content from authenticated user',
        ];

        // First submission should succeed
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/tickets', $ticketData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('tickets', ['theme' => 'Authenticated Ticket']);

        // Second submission from same email should be blocked
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/api/tickets', $ticketData);
        $response->assertStatus(429); // Too Many Requests
        $response->assertJson([
            'message' => 'Rate limit exceeded. Please try again later.',
        ]);

        // Verify only one ticket was created
        $this->assertEquals(1, Ticket::count());
    }
}
