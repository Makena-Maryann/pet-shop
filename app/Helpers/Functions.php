<?php

namespace App\Helpers;

/**
 * Returns a custom API response
 *
 * @param bool $success
 * @param mixed $data
 * @param string|null $error
 * @param array $errors
 * @param int $statusCode
 *
 * @return \Illuminate\Http\JsonResponse
 */
function customApiResponse($success, $data = [], $error = null, $errors = [], $extra = [], $statusCode = 200)
{
    $response = [
        'success' => (int) $success,
        'data' => $data,
        'error' => $error,
        'errors' => $errors,
        'extra' => $extra,
    ];

    return response()->json($response, $statusCode);
}
