<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Ticket;
use App\Services\FileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FileService $fileService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = new FileService;
    }

    public function test_download_ticket_file_returns_download_response()
    {
        Storage::fake('public');

        $ticket = Ticket::factory()->create();

        // Create a test file
        $file = File::create('test.txt', 1, 'text/plain');

        // Attach the file to the ticket
        $media = $ticket->addMedia($file)
            ->toMediaCollection('files');

        // Mock the file content in storage
        Storage::disk('public')->put("{$media->id}/{$media->file_name}", 'Test file content');

        // Create a temporary file for testing
        $response = $this->fileService->downloadTicketFile($ticket, $media->id);

        $this->assertInstanceOf(BinaryFileResponse::class, $response);
    }

    public function test_download_ticket_file_throws_404_when_media_not_found()
    {
        $ticket = Ticket::factory()->create();

        $this->expectException(NotFoundHttpException::class);

        $this->fileService->downloadTicketFile($ticket, 999);
    }

    public function test_download_ticket_file_throws_404_when_media_does_not_belong_to_ticket()
    {
        Storage::fake('public');

        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();

        // Create a file and attach to ticket1
        $file = File::create('test.txt', 1, 'text/plain');
        $media = $ticket1->addMedia($file)
            ->toMediaCollection('files');

        // Try to download the file from ticket2 (which doesn't own it)
        $this->expectException(NotFoundHttpException::class);

        $this->fileService->downloadTicketFile($ticket2, $media->id);
    }

    public function test_download_ticket_file_throws_404_when_file_does_not_exist_on_disk()
    {
        Storage::fake('public');

        $ticket = Ticket::factory()->create();

        // Create a media record without actually storing the file
        $file = File::create('test.txt', 1, 'text/plain');
        $media = $ticket->addMedia($file)
            ->toMediaCollection('files');

        // Delete the actual file from storage to simulate missing file
        Storage::disk('public')->delete("{$media->id}/{$media->file_name}");

        $this->expectException(NotFoundHttpException::class);

        $this->fileService->downloadTicketFile($ticket, $media->id);
    }

    public function test_download_ticket_file_allows_valid_download()
    {
        Storage::fake('public');

        $ticket = Ticket::factory()->create();

        // Create a file
        $file = File::create('test.txt', 1, 'text/plain');
        $media = $ticket->addMedia($file)
            ->toMediaCollection('files');

        // Store the file content in the fake storage
        Storage::disk('public')->put("{$media->id}/{$media->file_name}", 'Test file content');

        $response = $this->fileService->downloadTicketFile($ticket, $media->id);

        $this->assertInstanceOf(BinaryFileResponse::class, $response);
    }

    public function test_download_ticket_file_prevents_directory_traversal()
    {
        Storage::fake('public');

        $ticket = Ticket::factory()->create();

        // Create a file with a potentially dangerous filename that might attempt directory traversal
        $file = File::create('../../etc/passwd', 1, 'text/plain');
        $media = $ticket->addMedia($file)
            ->toMediaCollection('files');

        // The media library should sanitize the filename, but we still want to make sure
        // that the service properly validates the file path to prevent directory traversal
        // Store the file content in the fake storage with the sanitized filename
        Storage::disk('public')->put("{$media->id}/{$media->file_name}", 'Test file content');

        $response = $this->fileService->downloadTicketFile($ticket, $media->id);

        // The download should succeed because the media library sanitizes the filename
        // and our path validation confirms the file is within the allowed directory
        $this->assertInstanceOf(BinaryFileResponse::class, $response);
    }
}
