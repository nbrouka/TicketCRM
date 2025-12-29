<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackFormRequest;
use App\Http\Requests\TicketFormRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\FileService;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Public Tickets')]
#[OA\Tag(name: 'Authenticated Tickets')]
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
        protected TicketService $ticketService,
        protected FileService $fileService,
    ) {}

    /**
     * Store a newly created ticket in storage (authenticated users).
     */
    #[OA\Post(
        path: '/api/tickets',
        summary: 'Create a new ticket',
        description: 'Creates a new ticket with customer information (authenticated users)',
        tags: ['Authenticated Tickets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
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
                            new OA\Property(property: 'attachments[]', type: 'array', items: new OA\Items(type: 'string', format: 'binary'), description: 'Attachments to attach to the ticket'),
                        ]
                    )
                ),
            ]
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
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
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Validation error'),
        ]
    )]
    public function storeForAuthenticated(TicketFormRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicketWithCustomer(
            $request->only(['theme', 'text']),
            $request->only(['name', 'phone', 'email']),
            $request->input('customer_id'),
            $this->fileService->extractFilesFromRequest($request)
        );

        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => new TicketResource($ticket->load('customer')),
        ], Response::HTTP_CREATED);
    }

    /**
     * Store a newly created ticket from feedback form (public).
     *
     * Note: When sending files, use multipart/form-data content type.
     * Files should be sent in the 'attachments[]' field.
     */
    #[OA\Post(
        path: '/api/feedback',
        summary: 'Create a ticket from feedback form',
        description: 'Creates a new ticket from the feedback widget form (public endpoint)',
        tags: ['Public Tickets'],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['theme', 'text', 'email'],
                        properties: [
                            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com', description: 'Customer email'),
                            new OA\Property(property: 'phone', type: 'string', example: '+1234567890', description: 'Customer phone (optional)'),
                            new OA\Property(property: 'name', type: 'string', example: 'John Doe', description: 'Customer name (optional)'),
                            new OA\Property(property: 'theme', type: 'string', example: 'Feedback', description: 'Ticket theme/subject'),
                            new OA\Property(property: 'text', type: 'string', example: 'Detailed feedback message', description: 'Ticket content'),
                            new OA\Property(property: 'attachments[]', type: 'array', items: new OA\Items(type: 'string', format: 'binary'), description: 'Attachments to attach to the ticket'),
                        ]
                    )
                ),
            ]
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Ticket created successfully from feedback',
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
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Validation error'),
        ]
    )]
    public function store(FeedbackFormRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicketWithCustomer(
            $request->only(['theme', 'text']),
            $request->only(['name', 'phone', 'email']),
            null,
            $this->fileService->extractFilesFromRequest($request)
        );

        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => new TicketResource($ticket->load('customer')),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display a listing of the tickets.
     */
    #[OA\Get(
        path: '/api/tickets',
        summary: 'Get all tickets',
        description: 'Returns a list of all tickets for authenticated user',
        tags: ['Authenticated Tickets'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'List of tickets',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Ticket')),
                        ]
                    )
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ]
    )]
    public function index(): JsonResponse
    {
        $tickets = $this->ticketService->getAllTickets();

        return response()->json(new TicketCollection($tickets));
    }

    /**
     * Display the specified ticket.
     */
    #[OA\Get(
        path: '/api/tickets/{ticket}',
        summary: 'Get a specific ticket',
        description: 'Returns details of a specific ticket',
        tags: ['Authenticated Tickets'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'ticket',
                in: 'path',
                required: true,
                description: 'Ticket ID',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Ticket details',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/Ticket'),
                        ]
                    )
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Ticket not found'),
        ]
    )]
    public function show(Ticket $ticket): JsonResponse
    {
        $ticket = $this->ticketService->findTicketById($ticket->id);

        if (! $ticket) {
            return response()->json(['message' => 'Ticket not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(new TicketResource($ticket));
    }

    /**
     * Update the specified ticket status.
     */
    #[OA\Patch(
        path: '/api/tickets/{ticket}/status',
        summary: 'Update ticket status',
        description: 'Updates the status of a specific ticket',
        tags: ['Authenticated Tickets'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'ticket',
                in: 'path',
                required: true,
                description: 'Ticket ID',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['status'],
                    properties: [
                        new OA\Property(property: 'status', type: 'string', enum: ['new', 'in_progress', 'done'], example: 'in_progress', description: 'New ticket status'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Ticket status updated successfully',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'message', type: 'string', example: 'Ticket status updated successfully'),
                            new OA\Property(property: 'ticket', ref: '#/components/schemas/Ticket'),
                        ]
                    )
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Ticket not found'),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Validation error'),
        ]
    )]
    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): JsonResponse
    {
        $updated = $this->ticketService->updateTicketStatus($ticket, $request->input('status'));

        if (! $updated) {
            return response()->json(['message' => 'Failed to update ticket status'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Ticket status updated successfully',
            'ticket' => new TicketResource($ticket->refresh()->load('customer')),
        ], Response::HTTP_OK);
    }

    /**
     * Get ticket statistics for different periods.
     */
    #[OA\Get(
        path: '/api/tickets/statistics',
        summary: 'Get ticket statistics',
        description: 'Returns ticket statistics for day, week, and month',
        tags: ['Authenticated Tickets'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Ticket statistics',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'day', type: 'integer', example: 5, description: 'Number of tickets created today'),
                            new OA\Property(property: 'week', type: 'integer', example: 25, description: 'Number of tickets created this week'),
                            new OA\Property(property: 'month', type: 'integer', example: 120, description: 'Number of tickets created this month'),
                        ]
                    )
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ]
    )]
    public function statistics(): JsonResponse
    {
        $statistics = $this->ticketService->getTicketStatistics();

        return response()->json($statistics, Response::HTTP_OK);
    }

    /**
     * Get ticket statistics grouped by month and status.
     */
    #[OA\Get(
        path: '/api/tickets/statistics-by-month',
        summary: 'Get ticket statistics grouped by month and status',
        description: 'Returns ticket statistics grouped by month and status',
        tags: ['Authenticated Tickets'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Ticket statistics grouped by month and status',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'month', type: 'string', example: '01', description: 'Month number (01-12)'),
                                new OA\Property(property: 'year', type: 'string', example: '2025', description: 'Year'),
                                new OA\Property(property: 'month_year', type: 'string', example: '2025-01', description: 'Combined month and year'),
                                new OA\Property(property: 'status_counts', type: 'object', description: 'Object with status counts'),
                            ]
                        )
                    )
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ]
    )]
    public function statisticsByMonth(): JsonResponse
    {
        $statistics = $this->ticketService->getTicketStatisticsByMonthAndStatus();

        return response()->json($statistics, Response::HTTP_OK);
    }
}
