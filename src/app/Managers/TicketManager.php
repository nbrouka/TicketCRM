<?php

namespace App\Managers;

use App\Filters\TicketFilter;
use App\Models\Ticket;

class TicketManager
{
    public function getFilteredTickets($perPage = 15, $request = null)
    {
        $filter = new TicketFilter($request);

        // Apply filters
        $query = Ticket::with(['customer:id,name,email,phone', 'media'])
            ->filter($filter);

        // Sort by created_at desc and id desc by default (important for cursor pagination)
        $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');

        return $query->cursorPaginate($perPage)->withQueryString();
    }
}
