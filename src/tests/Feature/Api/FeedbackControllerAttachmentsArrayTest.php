<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FeedbackControllerAttachmentsArrayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Flush Redis to ensure clean state for tests
        Redis::flushall();
    }

    public function test_feedback_store_creates_ticket_with_attachments_array_field()
    {
        Storage::fake('public');

        $file1 = File::image('feedback_image1.jpg');
        $file2 = File::image('feedback_image2.jpg');

        // Test the exact format that the HTML form sends: attachments[]
        $requestData = [
            'theme' => 'Feedback Widget Ticket with Files',
            'text' => 'This is feedback from the widget with files',
            'name' => 'Widget Customer',
            'phone' => '+1234567890',
            'email' => 'widget.customer@example.com',
            'attachments[]' => [$file1, $file2],  // Using attachments[] format as sent by HTML form
        ];

        $response = $this->post('/api/feedback', $requestData, [
            'Accept' => 'application/json',
            'Content-Type' => 'multipart/form-data',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Ticket created successfully',
        ]);

        // Get the created ticket to check if files were attached
        $ticketId = $response->json('ticket.id');
        $ticket = Ticket::find($ticketId);

        $this->assertCount(2, $ticket->getMedia('files'));
    }

    public function test_feedback_store_creates_ticket_with_single_attachment_array_field()
    {
        Storage::fake('public');

        $file = File::image('feedback_single.jpg');

        // Test the exact format that the HTML form sends: attachments[]
        $requestData = [
            'theme' => 'Feedback Widget Ticket with Single File',
            'text' => 'This is feedback from the widget with a single file',
            'name' => 'Widget Customer',
            'phone' => '+1234567890',
            'email' => 'widget.customer@example.com',
            'attachments[]' => [$file],  // Using attachments[] format as sent by HTML form
        ];

        $response = $this->post('/api/feedback', $requestData, [
            'Accept' => 'application/json',
            'Content-Type' => 'multipart/form-data',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Ticket created successfully',
        ]);

        // Get the created ticket to check if file was attached
        $ticketId = $response->json('ticket.id');
        $ticket = Ticket::find($ticketId);

        $this->assertCount(1, $ticket->getMedia('files'));
    }
}
