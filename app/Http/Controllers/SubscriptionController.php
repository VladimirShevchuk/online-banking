<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * @OA\Post(
     *      path="/subscriptions",
     *      operationId="storeSubscription",
     *      tags={"Subscriptions"},
     *      summary="Store new subscription",
     *      description="Returns subscription data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreSubscriptionRequest")
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
    public function store(StoreSubscriptionRequest $request)
    {
        $subscription = Subscription::create($request->validated());

        return new SubscriptionResource($subscription);
    }

    /**
     * @OA\Get(
     *      path="/subscriptions",
     *      operationId="getSubscriptions",
     *      tags={"Subscriptions"},
     *      summary="Get list of subscriptions",
     *      description="Returns subscriptions list",
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
    public function index(Request $request, SubscriptionRepository $subscriptionRepository)
    {
        return SubscriptionResource::collection(
            $subscriptionRepository->getUserSubscriptionsPaginated(
                (int)$request->user()->getKey(),
                (int)$request->input('perPage')
            )
        );
    }
}
