<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class TicketFilter extends QueryFilter
{
    public function dateFrom(Builder $query, $dateFrom)
    {
        if ($dateFrom) {
            return $query->where('created_at', '>=', $dateFrom.' 00:00:00');
        }

        return $query;
    }

    public function dateTo(Builder $query, $dateTo)
    {
        if ($dateTo) {
            return $query->where('created_at', '<=', $dateTo.' 23:59');
        }

        return $query;
    }

    public function status(Builder $query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }

        return $query;
    }

    public function email(Builder $query, $email)
    {
        if ($email) {
            return $query->whereHas('customer', function ($q) use ($email) {
                $q->where('email', 'like', '%'.$email.'%');
            });
        }

        return $query;
    }

    public function phone(Builder $query, $phone)
    {
        if ($phone) {
            return $query->whereHas('customer', function ($q) use ($phone) {
                $q->where('phone', 'like', '%'.$phone.'%');
            });
        }

        return $query;
    }

    public function dateAnswerFrom(Builder $query, $dateAnswerFrom)
    {
        if ($dateAnswerFrom) {
            return $query->where('date_answer', '>=', $dateAnswerFrom.' 00:00:00');
        }

        return $query;
    }

    public function dateAnswerTo(Builder $query, $dateAnswerTo)
    {
        if ($dateAnswerTo) {
            return $query->where('date_answer', '<=', $dateAnswerTo.' 23:59:59');
        }

        return $query;
    }
}
