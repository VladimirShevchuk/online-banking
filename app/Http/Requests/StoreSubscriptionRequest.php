<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      title="StoreSubscriptionRequest",
 *      description="Store Subscription request body data",
 *      type="object",
 *      required={"name","price","frequency","start_date"},
 *      @OA\Property(
 *           property="name",
 *           title="name",
 *           type="string",
 *           description="Subscription name/description",
 *           example="Monthly Oreylis"
 *       ),
 *       @OA\Property(
 *           property="price",
 *           title="price",
 *           type="integer",
 *           description="Cost of subscription",
 *           example="100"
 *       ),
 *       @OA\Property(
 *            property="frequency",
 *            title="frequency",
 *            type="string",
 *            description="Frequency of subscription (monthly/yearly)",
 *            example="monthly"
 *        ),
 *        @OA\Property(
 *            property="start_date",
 *            title="start_date",
 *            type="date",
 *            description="Date from which subscription starts",
 *            example="2024-07-01"
 *        )
 * )
 */
class StoreSubscriptionRequest extends FormRequest
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
            'name' => 'required|string',
            'price' => 'required|integer',
            'frequency' => 'required|string',
            'start_date' => 'required|date'
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()->getKey(),
            'next_invoice_date' => $this->input('start_date')
        ]);
    }
}
