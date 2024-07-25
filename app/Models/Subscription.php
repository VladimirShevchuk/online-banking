<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    public const FREQUENCY_MONTHLY = 'monthly';

    public const FREQUENCY_YEARLY = 'yearly';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_ON_HOLD = 'on_hold';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::STATUS_ACTIVE,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'price',
        'frequency',
        'start_date',
        'next_invoice_date',
        'status',
        'status_message',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'next_invoice_date'
    ];

    protected $casts = [
        'next_invoice_date' => 'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function activate(): static
    {
        $this->status = self::STATUS_ACTIVE;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isDueToInvoice(): bool
    {
        return $this->next_invoice_date->lte(Carbon::now());
    }

    public function actualizeNextInvoiceDate(): void
    {
        /**
         * This is the simplest approach. But depending on the business workflow and how payment date affects start and
         * duration of the subscription, more complex logic can be implemented.
         * Ex: if a user paid for a monthly sub a few days later than start date, should the next start date be the initial day,
         * or it shifts to the new payment day?
         */
        if ($this->frequency === self::FREQUENCY_YEARLY) {
            $this->next_invoice_date = Carbon::now()->addYear();
        } elseif ($this->frequency === self::FREQUENCY_MONTHLY) {
            $this->next_invoice_date = Carbon::now()->addMonth();
        }
    }
}
