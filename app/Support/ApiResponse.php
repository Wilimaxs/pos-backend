<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    public static function success(
        string $message,
        mixed  $data = null,
        ?array $meta = null,
        int    $statusCode = 200
    ): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];

        if ($meta != null) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    public static function error(
        string $message,
        mixed  $data = null,
        int    $statusCode = 400
    ): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }
}
