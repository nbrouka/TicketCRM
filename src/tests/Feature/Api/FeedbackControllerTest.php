<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FeedbackControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_store_creates_new_ticket_with_customer()
    {
        $requestData = [
            'theme' => 'Feedback Widget Ticket',
            'text' => 'This is feedback from the widget',
            'name' => 'Widget Customer',
            'phone' => '+1234567890',
            'email' => 'widget.customer@example.com',
        ];

        $response = $this->postJson('/api/feedback', $requestData);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Ticket created successfully',
        ]);

        $this->assertDatabaseHas('tickets', [
            'theme' => 'Feedback Widget Ticket',
            'text' => 'This is feedback from the widget',
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'Widget Customer',
            'email' => 'widget.customer@example.com',
        ]);
    }

    public function test_feedback_store_creates_ticket_and_finds_existing_customer_by_email()
    {
        $existingCustomer = Customer::factory()->create([
            'name' => 'Existing Customer',
            'email' => 'existing@example.com',
        ]);

        $requestData = [
            'theme' => 'Feedback Widget Ticket',
            'text' => 'This is feedback from the widget',
            'name' => 'Different Name',
            'phone' => '+1234567890',
            'email' => 'existing@example.com', // Same email as existing customer
        ];

        $response = $this->postJson('/api/feedback', $requestData);

        $response->assertStatus(201);

        $responseData = $response->json();
        $this->assertEquals($existingCustomer->id, $responseData['ticket']['customer']['id']);
    }

    public function test_feedback_store_creates_ticket_with_files()
    {
        Storage::fake('public');

        $file1 = File::image('feedback_image1.jpg');
        $file2 = File::image('feedback_image2.jpg');

        $requestData = [
            'theme' => 'Feedback Widget Ticket with Files',
            'text' => 'This is feedback from the widget with files',
            'name' => 'Widget Customer',
            'phone' => '+1234567890',
            'email' => 'widget.customer@example.com',
            'attachments' => [$file1, $file2],
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

    public function test_feedback_store_creates_ticket_with_single_file()
    {
        Storage::fake('public');

        $file = File::image('feedback_single.jpg');

        $requestData = [
            'theme' => 'Feedback Widget Ticket with Single File',
            'text' => 'This is feedback from the widget with a single file',
            'name' => 'Widget Customer',
            'phone' => '+1234567890',
            'email' => 'widget.customer@example.com',
            'attachments' => [$file],
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

    public function test_feedback_store_creates_ticket_with_multiple_file_types()
    {
        Storage::fake('public');

        $imageFile = File::image('feedback_image.png');
        $pdfFile = File::create('document.pdf', 'PDF content', 'application/pdf');
        $textFile = File::create('document.txt', 'Text content', 'text/plain');

        $requestData = [
            'theme' => 'Feedback Widget Ticket with Multiple File Types',
            'text' => 'This is feedback from the widget with multiple file types',
            'name' => 'Widget Customer',
            'phone' => '+1234567890',
            'email' => 'widget.customer@example.com',
            'attachments' => [$imageFile, $pdfFile, $textFile],
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

        $this->assertCount(3, $ticket->getMedia('files'));
    }

    public function test_feedback_store_fails_with_invalid_data()
    {
        $requestData = [
            'theme' => '', // Required field
            'text' => '',  // Required field
            'name' => 'Widget Customer',
            'phone' => '+1234567890',
            'email' => 'invalid-email', // Invalid email
        ];

        $response = $this->postJson('/api/feedback', $requestData);

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

    public function test_feedback_store_fails_without_required_fields()
    {
        $requestData = [
            // Missing all required fields
        ];

        $response = $this->postJson('/api/feedback', $requestData);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'email',
                'name',
                'theme',
                'text',
            ],
        ]);
    }

    public function test_feedback_store_fails_with_invalid_file_types()
    {
        Storage::fake('public');

        $invalidFile = File::create('script.php', '<?php echo "malicious"; ?>', 'text/php');

        $requestData = [
            'theme' => 'Feedback Widget Ticket with Invalid File',
            'text' => 'This is feedback from the widget with invalid file',
            'name' => 'Widget Customer',
            'phone' => '+1234567890',
            'email' => 'widget.customer@example.com',
            'attachments' => [$invalidFile],
        ];

        $response = $this->post('/api/feedback', $requestData, [
            'Accept' => 'application/json',
            'Content-Type' => 'multipart/form-data',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'attachments.0',
            ],
        ]);
    }

    public function test_feedback_store_fails_with_too_large_files()
    {
        Storage::fake('public');

        // Create a file larger than the 10MB limit (10240 KB)
        $largeFile = File::create('large_file.pdf', str_repeat('A', 11 * 1024 * 1024)); // 11MB

        $requestData = [
            'theme' => 'Feedback Widget Ticket with Large File',
            'text' => 'This is feedback from the widget with large file',
            'name' => 'Widget Customer',
            'phone' => '+1234567890',
            'email' => 'widget.customer@example.com',
            'attachments' => [$largeFile],
        ];

        $response = $this->post('/api/feedback', $requestData, [
            'Accept' => 'application/json',
            'Content-Type' => 'multipart/form-data',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'attachments.0',
            ],
        ]);
    }

    public function test_feedback_store_with_too_many_files()
    {
        Storage::fake('public');

        // Create 6 files (more than the max allowed of 5)
        $files = [];
        for ($i = 1; $i <= 6; $i++) {
            $files[] = File::image("file{$i}.jpg");
        }

        $requestData = [
            'theme' => 'Feedback Widget Ticket with Too Many Files',
            'text' => 'This is feedback from the widget with too many files',
            'name' => 'Widget Customer',
            'phone' => '+1234567890',
            'email' => 'widget.customer@example.com',
            'attachments' => $files,
        ];

        $response = $this->post('/api/feedback', $requestData, [
            'Accept' => 'application/json',
            'Content-Type' => 'multipart/form-data',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'attachments',
            ],
        ]);
    }
}
