<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\TicketStatus;
use App\Models\Ticket;

class TicketObserver
{
    /**
     * Handle the Ticket "updating" event.
     *
     * @return void
     */
    public function updating(Ticket $ticket)
    {
        // If status is being changed to 'done' and date_answer is not already set
        if ($ticket->isDirty('status') && $ticket->status === TicketStatus::DONE && ! $ticket->getOriginal('date_answer')) {
            $ticket->date_answer = now();
        }
    }
}
