<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketFormRequest;
use App\Http\Resources\TicketResource;
use App\Managers\TicketManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Tickets')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    in: 'header',
    name: 'Authorization'
)]
class TicketController extends Controller
{
    public function __construct(
        protected TicketManager $ticketManager,
    ) {}

    /**
     * Store a newly created ticket in storage.
     */
    #[OA\Post(
        path: '/api/tickets',
        summary: 'Create a new ticket',
        description: 'Creates a new ticket with customer information',
        tags: ['Tickets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['theme', 'text', 'email'],
                    properties: [
                        new OA\Property(property: 'customer_id', type: 'integer', example: 1, description: 'ID of existing customer (optional if providing email/phone)'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com', description: 'Customer email (required if no customer_id provided)'),
                        new OA\Property(property: 'phone', type: 'string', example: '+1234567890', description: 'Customer phone (optional)'),
                        new OA\Property(property: 'name', type: 'string', example: 'John Doe', description: 'Customer name (optional)'),
                        new OA\Property(property: 'theme', type: 'string', example: 'Support Request', description: 'Ticket theme/subject'),
                        new OA\Property(property: 'text', type: 'string', example: 'Detailed description of the issue', description: 'Ticket content'),
                        new OA\Property(property: 'status', type: 'string', enum: ['new', 'in_progress', 'done'], example: 'new', description: 'Ticket status'),
                        new OA\Property(property: 'date_answer', type: 'string', format: 'date-time', example: '2023-12-25T10:00Z', description: 'Date of answer'),
                        new OA\Property(property: 'files', type: 'array', items: new OA\Items(type: 'string', format: 'binary'), description: 'Files to attach to the ticket'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Ticket created successfully',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'message', type: 'string', example: 'Ticket created successfully'),
                            new OA\Property(property: 'ticket', ref: '#/components/schemas/Ticket'),
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(TicketFormRequest $request): JsonResponse
    {
        $ticket = $this->ticketManager->createTicketWithCustomer(
            $request->only(['theme', 'text']),
            $request->only(['name', 'phone', 'email']),
            $request->input('customer_id'),
            $request->hasFile('files') ? $request->file('files') : null
        );

        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => new TicketResource($ticket->load('customer')),
        ], Response::HTTP_CREATED);
    }
}
