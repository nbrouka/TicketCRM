<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Customer',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'phone', type: 'string', example: '+1234567890'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'Ticket',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'customer', ref: '#/components/schemas/Customer'),
        new OA\Property(property: 'theme', type: 'string', example: 'Support Request'),
        new OA\Property(property: 'text', type: 'string', example: 'Detailed description of the issue'),
        new OA\Property(property: 'status', type: 'string', enum: ['new', 'in_progress', 'done'], example: 'new'),
        new OA\Property(property: 'date_answer', type: 'string', format: 'date-time', example: '2023-12-25T10:00Z'),
        new OA\Property(property: 'files', type: 'array', items: new OA\Items(ref: '#/components/schemas/File')),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'File',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'file_name', type: 'string', example: 'document.pdf'),
        new OA\Property(property: 'mime_type', type: 'string', example: 'application/pdf'),
        new OA\Property(property: 'size', type: 'integer', example: 102400),
        new OA\Property(property: 'url', type: 'string', format: 'uri', example: 'http://example.com/storage/files/document.pdf'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class OpenApiSchemas extends Model
{
    // This class is used only for OpenAPI schema definitions
}
