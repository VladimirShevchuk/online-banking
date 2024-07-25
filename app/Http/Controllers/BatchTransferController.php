<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBatchTransferRequest;
use App\Http\Resources\TransferResource;
use App\Jobs\ProcessTransfer;
use App\Models\Transfer;
use App\Repositories\TransferRepository;
use Exception;

class BatchTransferController extends Controller
{
    /**
     * @OA\Post(
     *      path="/transfers/batch",
     *      operationId="storeBatchTransfer",
     *      tags={"Transfers"},
     *      summary="Store new batch transfer",
     *      description="Returns transfers data from a batch",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreBatchTransferRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *           @OA\Response(
     *           response=422,
     *           description="Unprocessable Content",
     *       ),
     * )
     */
    public function store(StoreBatchTransferRequest $request)
    {
        $resultTransfers = [];
        foreach ($request->getValidatedItems() as $batchItemData) {
            $transfer = new Transfer($batchItemData);
            try {
                $transfer->save();
                $resultTransfers[] = $transfer;
            } catch (Exception $exc) {
                $transfer->error($exc->getMessage());
                $resultTransfers[] = $transfer;
                continue;
            }
            ProcessTransfer::dispatchAfterResponse($transfer);
        }

        return TransferResource::collection($resultTransfers);
    }

    public function show(string $id, TransferRepository $transferRepository)
    {
        $transfers = $transferRepository->findByBatchId($id);

        if (empty($transfers)) {
            return response([], 404);
        }

        return TransferResource::collection($transfers);
    }
}
