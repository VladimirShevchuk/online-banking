<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountStatementResource;
use App\Repositories\ActivityRepository;
use Illuminate\Http\Request;

class AccountStatementController extends Controller
{
    /**
     * @OA\Get(
     *      path="/accounts/statement",
     *      operationId="getAccountStatement",
     *      tags={"Accounts"},
     *      summary="Get list of account transactions",
     *      description="Returns list of account's transactions",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
     *     )
     */
    public function index(Request $request, ActivityRepository $activityRepository)
    {
        return AccountStatementResource::collection(
            $activityRepository->getAccountBalanceChangesPaginated(
                (int)$request->user()->account->getKey(),
                (int)$request->input('perPage')
            )
        );
    }
}
