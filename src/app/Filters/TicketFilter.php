<?php

declare(strict_types=1);

namespace App\Filters;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;

class TicketFilter extends QueryFilter
{
    /**
     * @param  Builder<Ticket>  $query
     * @return Builder<Ticket>
     */
    public function dateFrom(Builder $query, string $dateFrom): Builder
    {
        if ($dateFrom) {
            return $query->where('created_at', '>=', $dateFrom.' 00:00:00');
        }

        return $query;
    }

    /**
     * @param  Builder<Ticket>  $query
     * @return Builder<Ticket>
     */
    public function dateTo(Builder $query, string $dateTo): Builder
    {
        if ($dateTo) {
            return $query->where('created_at', '<=', $dateTo.' 23:59');
        }

        return $query;
    }

    /**
     * @param  Builder<Ticket>  $query
     * @return Builder<Ticket>
     */
    public function status(Builder $query, string $status): Builder
    {
        if ($status) {
            return $query->where('status', $status);
        }

        return $query;
    }

    /**
     * @param  Builder<Ticket>  $query
     * @return Builder<Ticket>
     */
    public function email(Builder $query, string $email): Builder
    {
        if ($email) {
            return $query->whereHas('customer', function ($q) use ($email) {
                $q->where('email', 'like', '%'.$email.'%');
            });
        }

        return $query;
    }

    /**
     * @param  Builder<Ticket>  $query
     * @return Builder<Ticket>
     */
    public function phone(Builder $query, string $phone): Builder
    {
        if ($phone) {
            return $query->whereHas('customer', function ($q) use ($phone) {
                $q->where('phone', 'like', '%'.$phone.'%');
            });
        }

        return $query;
    }

    /**
     * @param  Builder<Ticket>  $query
     * @return Builder<Ticket>
     */
    public function dateAnswerFrom(Builder $query, string $dateAnswerFrom): Builder
    {
        if ($dateAnswerFrom) {
            return $query->where('date_answer', '>=', $dateAnswerFrom.' 00:00:00');
        }

        return $query;
    }

    /**
     * @param  Builder<Ticket>  $query
     * @return Builder<Ticket>
     */
    public function dateAnswerTo(Builder $query, string $dateAnswerTo): Builder
    {
        if ($dateAnswerTo) {
            return $query->where('date_answer', '<=', $dateAnswerTo.' 23:59:59');
        }

        return $query;
    }

    /**
     * @param  Builder<Ticket>  $query
     * @return Builder<Ticket>
     */
    public function search(Builder $query, string $search): Builder
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('theme', 'like', '%'.$search.'%')
                    ->orWhere('text', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }
}
