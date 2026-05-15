<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'Operasi berhasil', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data ?? (object) [],
            'meta' => [
                'service_name' => config('iae.service_name', 'KRS-Service'),
                'api_version' => config('iae.api_version', 'v1'),
            ],
        ], $status);
    }

    public static function error(string $message, mixed $errors = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
