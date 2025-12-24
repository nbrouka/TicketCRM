<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTicketStatusRequest;
use App\Managers\TicketManager;
use App\Models\Ticket;
use App\Services\FileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketController extends Controller
{
    protected TicketManager $ticketManager;

    protected FileService $fileService;

    public function __construct(TicketManager $ticketManager, FileService $fileService)
    {
        $this->ticketManager = $ticketManager;
        $this->fileService = $fileService;
    }

    public function index(Request $request): View
    {
        $tickets = $this->ticketManager->getFilteredTickets($request);

        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['customer', 'media']);

        return view('tickets.show', compact('ticket'));
    }

    public function downloadFile(Ticket $ticket, int $mediaId): BinaryFileResponse
    {
        return $this->fileService->downloadTicketFile($ticket, $mediaId);
    }

    public function updateStatus(Ticket $ticket, UpdateTicketStatusRequest $request): RedirectResponse
    {
        $this->ticketManager->updateTicketStatus($ticket, $request->status);

        return redirect()->back()->with('success', 'Ticket status updated successfully!');
    }
}
