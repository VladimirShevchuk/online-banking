<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      title="TransferResource",
 *      description="Transfer resource",
 *      type="object",
 *      @OA\Property(
 *            property="id",
 *            title="id",
 *            type="integer",
 *            description="Transfer ID",
 *            example="1"
 *      ),
 *      @OA\Property(
 *            property="batch_id",
 *            title="batch_id",
 *            type="string",
 *            description="Client-generated id of transfers batch",
 *            example="550e8400-e29b-41d4-a716-446655440000 "
 *      ),
 *      @OA\Property(
 *           property="reference_id",
 *           title="reference_id",
 *           type="string",
 *           description="Client-generated id of transfer",
 *           example="550e8400-e29b-41d4-a716-446655440000 "
 *       ),
 *       @OA\Property(
 *           property="amount",
 *           title="amount",
 *           type="integer",
 *           description="Amount of money to transfer",
 *           example="100"
 *       ),
 *       @OA\Property(
 *            property="recipient_user_id",
 *            title="recipient_user_id",
 *            type="integer",
 *            description="ID of the user who will receive money",
 *            example="2"
 *        ),
 *        @OA\Property(
 *            property="description",
 *            title="description",
 *            type="string",
 *            description="Short description of transaction puprose",
 *            example="Payment for items"
 *        ),
 *        @OA\Property(
 *             property="status",
 *             title="status",
 *             type="string",
 *             description="Status of the transfer",
 *             example="declined"
 *        ),
 *        @OA\Property(
 *              property="status_message",
 *              title="status_message",
 *              type="string",
 *              description="Status message of the transfer",
 *              example="Not enough money on the sender's balance."
 *         ),
 *         @OA\Property(
 *              property="created_at",
 *              title="created_at",
 *              type="date",
 *              description="SDate and time of the transfer creation",
 *              example="2024-07-01"
 *         )
 * )
 */
class TransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'batch_id' => $this->batch_id,
            'reference_id' => $this->reference_id,
            'description' => $this->description,
            'amount' => $this->amount,
            'recipient_id' => $this->recipient_user_id,
            'status' => $this->status,
            'status_message' => $this->status_message,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s')
        ];
    }
}
