<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketFormRequest;
use App\Http\Resources\TicketResource;
use App\Managers\TicketManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    public function __construct(
        protected TicketManager $ticketManager,
    ) {}

    /**
     * Store a newly created ticket in storage.
     */
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
