<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountStatementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $newBalance = $this->properties->get('attributes')['balance'];
        $oldBalance = $this->properties->get('old')['balance'];

        return [
            'operation_type' => ($newBalance > $oldBalance) ? 'deposit' : 'withdrawal',
            'amount' => abs($newBalance - $oldBalance),
            'balance' => $newBalance,
            'created_at' => $this->created_at
        ];
    }
}
