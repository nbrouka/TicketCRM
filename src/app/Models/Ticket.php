<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketStatus;
use App\Filters\TicketFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin Builder<Ticket>
 *
 * @method static Factory<Ticket> factory($count = null, $state = [])
 */
class Ticket extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    // Media conversion constants
    public const THUMBNAIL_WIDTH = 150;

    public const THUMBNAIL_HEIGHT = 150;

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

    /**
     * @return BelongsTo<Customer, Ticket>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->width(self::THUMBNAIL_WIDTH)
                    ->height(self::THUMBNAIL_HEIGHT);
            });
    }

    /**
     * Attach files to the ticket
     */
    /** @param array<mixed> $files */
    public function attachFiles(array $files): void
    {
        foreach ($files as $file) {
            $this->addMedia($file)
                ->toMediaCollection('files');
        }
    }

    /**
     * @param  Builder<Ticket>  $query
     * @param  TicketFilter  $filter
     * @return Builder<Ticket>
     */
    public function scopeFilter($query, $filter)
    {
        return $filter->apply($query);
    }
}
