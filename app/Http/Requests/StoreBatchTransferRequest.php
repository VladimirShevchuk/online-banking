<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *      title="StoreBatchTransferRequest",
 *      description="Store BatchTransfer request body data",
 *      type="object",
 *      required={"batch_id","items"},
 *      @OA\Property(
 *           property="batch_id",
 *           title="batch_id",
 *           type="string",
 *           description="Client-generated id of transfers batch",
 *           example="550e8400-e29b-41d4-a716-446655440000 "
 *       ),
 *       @OA\Property(
 *           property="items",
 *           title="items",
 *           type="array",
 *           description="Transfers to create",
 *           @OA\Items(
 *              ref="#/components/schemas/StoreTransferRequest"
 *           ),
 *           example="[{'reference_id': '550e8400-e29b-41d4-a716-446655440000 ','amount': 100,'recipient_user_id': 2,'description': 'Payment for items'}]"
 *       )
 * )
 */
class StoreBatchTransferRequest extends StoreTransferRequest
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
        $batchRules = ['batch_id' => 'required|string'];

        /**
         * To prevent rules definition duplication, we use single Transfer rules and adjust them to the batch array structure.
         */
        foreach (parent::rules() as $parentRuleKey => $rule) {
            $batchRules['items.*.'.$parentRuleKey] = $rule;
        }

        return $batchRules;
    }

    /**
     * Enrich request data. Add current user as sender_user_id and add batch_id from body root to each transfer.
     */
    public function prepareForValidation(): void
    {
        $enrichedItems = [];
        foreach ($this->get('items') as $item) {
            $enrichedItems[] = array_merge(
                $item,
                [
                    'sender_user_id' => $this->user()->getKey(),
                    'batch_id' => $this->input('batch_id')
                ]
            );
        }

        $this->merge([
            'items' => $enrichedItems
        ]);
    }

    public function getValidatedItems(): array
    {
        return $this->validated()['items'];
    }
}
