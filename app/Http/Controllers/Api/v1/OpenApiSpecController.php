<?php

namespace App\Http\Controllers\Api\v1;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Pet Shop API - Swagger Documentation",
 *      description="This API has been created as part of Buckhill's Backend Developer Task.",
 *      @OA\Contact(
 *          email="maryann.makena00@gmail.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\SecurityScheme(
 *     @OA\Flow(
 *         flow="clientCredentials",
 *         tokenUrl="oauth/token",
 *         scopes={}
 *     ),
 *     securityScheme="bearerAuth",
 *     in="header",
 *     type="http",
 *     name="oauth2",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 *
 * @OA\Tag(
 *     name="Admin",
 *     description="Admin API endpoint"
 * )
 *
 *
 * @OA\Tag(
 *     name="User",
 *     description="User API endpoint"
 * )
 */
class OpenApiSpecController
{
}
