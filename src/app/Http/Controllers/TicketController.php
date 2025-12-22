<?php

namespace App\Http\Controllers;

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
        $tickets = $this->ticketManager->getFilteredTickets(15, $request);

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

    public function updateStatus(Ticket $ticket, \Illuminate\Http\Request $request)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,done',
        ]);

        $updateData = [
            'status' => $request->status,
        ];

        // Set date_answer if status is 'done' and it's not already set
        if ($request->status === 'done' && ! $ticket->date_answer) {
            $updateData['date_answer'] = now();
        }

        $ticket->update($updateData);

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
