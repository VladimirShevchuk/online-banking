<?php

namespace App\Models;

use App\Exceptions\NotEnoughMoneyException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Account extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'balance',
    ];

    public function depositMoney(int $amount): static
    {
        $this->balance += $amount;
        return $this;
    }

    public function withdrawMoney(int $amount): static
    {
        if ($this->hasLessMoneyThan($amount)) {
            throw new NotEnoughMoneyException('Not enough money on the balance.');
        }

        $this->balance -= $amount;
        return $this;
    }

    public function hasLessMoneyThan(int $amount): bool
    {
        return $this->balance < $amount;
    }

    /**
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['balance']);
    }
}
