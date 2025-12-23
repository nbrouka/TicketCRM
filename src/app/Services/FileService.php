<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Http\Response;

class FileService
{
    /**
     * Download a file associated with a ticket
     */
    public function downloadTicketFile(Ticket $ticket, $mediaId)
    {
        $media = $ticket->media()->where('id', $mediaId)->firstOrFail();

        if ($media->model_id !== $ticket->id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $filePath = storage_path('app/public/'.$media->id.'/'.$media->file_name);
        $realPath = realpath($filePath);
        $allowedDir = realpath(storage_path('app/public/'));

        if (! $realPath || ! str_starts_with($realPath, $allowedDir)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if (! file_exists($filePath)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return response()->download($filePath, $media->file_name);
    }
}
