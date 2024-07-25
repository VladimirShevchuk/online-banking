<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_ERROR = 'error';

    /**
     * @var array
     */
    protected $fillable = [
        'batch_id',
        'reference_id',
        'amount',
        'description',
        'sender_user_id',
        'recipient_user_id'
    ];

    protected $attributes = [
        'status' => self::STATUS_ACCEPTED,
    ];

    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /**
     * @return $this
     */
    public function pend(): static
    {
        $this->status = self::STATUS_PENDING;
        return $this;
    }

    /**
     * @return $this
     */
    public function approve(): static
    {
        $this->status = self::STATUS_APPROVED;
        return $this;
    }

    /**
     * @return $this
     */
    public function decline(string $reason = null): static
    {
        $this->status = self::STATUS_DECLINED;
        $this->status_message = $reason;
        return $this;
    }

    /**
     * @return $this
     */
    public function error(string $message = null): static
    {
        $this->status = self::STATUS_ERROR;
        $this->status_message = $message;
        return $this;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        /**
         * Error status is not included to allow retries
         */
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_DECLINED]);
    }
}
