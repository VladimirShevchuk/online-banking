<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Http\Resources\TransferResource;
use App\Jobs\ProcessTransfer;
use App\Models\Transfer;
use App\Repositories\TransferRepository;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * @OA\Post(
     *      path="/transfers",
     *      operationId="storeTransfer",
     *      tags={"Transfers"},
     *      summary="Store new transfer",
     *      description="Returns transfer data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreTransferRequest")
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
    public function store(StoreTransferRequest $request)
    {
        $transfer = Transfer::create($request->validated());

        /**
         * Unfortunately, Laravel jod::dispatch has no way to check delivery acknowledgement to change status
         * of the transfer to pending in a callback.
         * Need to improve the approach here to guaranty job delivery to the queue.
         * Ex: add a cronjob (compensating or instead dispatch, but it's slower than immediate dispatch), own dispatch with ack.
         */
        ProcessTransfer::dispatchAfterResponse($transfer);

        return new TransferResource($transfer);
    }

    /**
     * @OA\Get(
     *      path="/transfers",
     *      operationId="getTransfers",
     *      tags={"Transfers"},
     *      summary="Get list of transfers",
     *      description="Returns transfers data",
     *      @OA\Parameter(
     *          name="perPage",
     *          description="Amount of items per page",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          description="Current page number",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
     * )
     */
    public function index(Request $request, TransferRepository $transferRepository)
    {
        return TransferResource::collection(
            $transferRepository->getTransfersSentByUserPaginated((int)$request->user()->getKey(), (int)$request->input('perPage', 2))
        );
    }

    /**
     * @OA\Get(
     *      path="/transfers/{id}",
     *      operationId="getTransfer",
     *      tags={"Transfers"},
     *      summary="Get transfer data",
     *      description="Returns transfer data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Transfer id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/TransferResource")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      ),
     *      @OA\Response(
     *           response=404,
     *           description="Not Found",
     *       )
     *     )
     */
    public function show(string $id, Request $request, TransferRepository $transferRepository)
    {
        $transfer = $transferRepository->find($id);

        if (!$transfer) {
            return response(status:404);
        }

        if ($request->user()->cannot('view', $transfer)) {
            return response(status:403);
        }

        return new TransferResource($transfer);
    }
}
