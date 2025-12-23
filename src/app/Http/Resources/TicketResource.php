<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Ticket $resource
 */
class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'theme' => $this->resource->theme,
            'text' => $this->resource->text,
            'status' => $this->resource->status->value ?? $this->resource->status,
            'date_answer' => $this->resource->date_answer,
            'files' => $this->whenLoaded('media', $this->resource->media->map(function ($media) {
                return [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'url' => $media->getUrl(),
                    'created_at' => $media->created_at,
                ];
            }), collect()),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
