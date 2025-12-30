<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;

class TicketCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Add pagination info if this is a cursor paginator
        if ($this->resource instanceof CursorPaginator) {
            return [
                'data' => $this->collection->map(function ($item) {
                    return new TicketResource($item);
                })->all(),
                'links' => [
                    'first' => null,
                    'last' => null,
                    'prev' => $this->resource->previousCursor() ? $this->resource->previousCursor()->encode() : null,
                    'next' => $this->resource->nextCursor() ? $this->resource->nextCursor()->encode() : null,
                ],
                'meta' => [
                    'path' => $this->resource->path(),
                    'per_page' => $this->resource->perPage(),
                    'next_cursor' => $this->resource->nextCursor()?->encode(),
                    'prev_cursor' => $this->resource->previousCursor()?->encode(),
                ],
            ];
        }

        return [
            'data' => $this->collection->map(function ($item) {
                return new TicketResource($item);
            })->all(),
        ];
    }
}
