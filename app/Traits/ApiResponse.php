<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait ApiResponse
{
    protected function successResponse(mixed $data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = 400, ?array $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    protected function paginatedResponse(
        AnonymousResourceCollection $collection,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        $response = $collection->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $response['data'],
            'pagination' => [
                'current_page' => $response['meta']['current_page'],
                'last_page' => $response['meta']['last_page'],
                'per_page' => $response['meta']['per_page'],
                'total' => $response['meta']['total'],
                'from' => $response['meta']['from'],
                'to' => $response['meta']['to'],
            ],
            'links' => $response['links'],
        ]);
    }
}
