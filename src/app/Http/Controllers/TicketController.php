<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTicketStatusRequest;
use App\Managers\TicketManager;
use App\Models\Ticket;
use App\Services\FileService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected $ticketManager;

    protected $fileService;

    public function __construct(TicketManager $ticketManager, FileService $fileService)
    {
        $this->ticketManager = $ticketManager;
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        $tickets = $this->ticketManager->getFilteredTickets($request);

        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['customer', 'media']);

        return view('tickets.show', compact('ticket'));
    }

    public function downloadFile(Ticket $ticket, $mediaId)
    {
        return $this->fileService->downloadTicketFile($ticket, $mediaId);
    }

    public function updateStatus(Ticket $ticket, UpdateTicketStatusRequest $request)
    {
        $ticket->update([
            'status' => $request->status,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket status updated successfully!',
                'ticket' => $ticket->refresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Ticket status updated successfully!');
    }
}
