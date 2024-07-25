<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Online Banking Documentation",
 *      description="Online Banking OpenApi description",
 *      @OA\Contact(
 *          email="admin@admin.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Online Banking API Server"
 * )

 *
 * @OA\Tag(
 *     name="Projects",
 *     description="API Endpoints of Online Banking"
 * )
 */
abstract class Controller
{
}
