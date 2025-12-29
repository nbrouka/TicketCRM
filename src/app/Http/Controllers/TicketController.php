<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTicketStatusRequest;
use App\Managers\TicketManager;
use App\Models\Ticket;
use App\Services\FileService;
use App\Services\TicketService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketController extends Controller
{
    public function __construct(
        protected TicketManager $ticketManager,
        protected FileService $fileService,
        protected TicketService $ticketService
    ) {}

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

    public function statistics(): JsonResponse
    {
        $statistics = $this->ticketService->getTicketStatistics();

        return response()->json($statistics);
    }

    public function statisticsByMonth(): View
    {
        return view('tickets.statistics');
    }

    public function statisticsByMonthData(): JsonResponse
    {
        $statistics = $this->ticketService->getTicketStatisticsByMonthAndStatus();

        return response()->json($statistics);
    }

    public function statisticsByDayData(Request $request): JsonResponse
    {
        $month = $request->query('month', 'last_month');
        $statistics = $this->ticketService->getTicketStatisticsByDayFromMonth($month);

        return response()->json($statistics);
    }

    public function statusDistribution(): JsonResponse
    {
        $statusDistribution = $this->ticketService->getTicketCountByStatus();

        return response()->json($statusDistribution);
    }
}
