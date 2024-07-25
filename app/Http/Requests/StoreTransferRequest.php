<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      title="StoreTransferRequest",
 *      description="Store Transfer request body data",
 *      type="object",
 *      required={"reference_id","amount","recipient_user_id"},
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
 * )
 */
class StoreTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'batch_id' => 'nullable|string',
            'reference_id' => 'required|string|max:255|unique:App\Models\Transfer,reference_id',
            'description' => 'nullable|string',
            'amount' => 'required|integer',
            'recipient_user_id' => 'required|integer|exists:users,id',
            'sender_user_id' => 'nullable|integer'
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'sender_user_id' => $this->user()->getKey()
        ]);
    }
}
