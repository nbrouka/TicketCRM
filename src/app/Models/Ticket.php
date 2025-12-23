<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'customer_id',
        'theme',
        'text',
        'status',
        'date_answer',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
        'date_answer' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->width(150)
                    ->height(150);
            });
    }

    /**
     * Attach files to the ticket
     */
    public function attachFiles($files): void
    {
        foreach ($files as $file) {
            $this->addMedia($file)
                ->toMediaCollection('files');
        }
    }

    public function scopeFilter($query, $filter)
    {
        return $filter->apply($query);
    }
}
