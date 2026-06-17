<?php

namespace App\Helpers;

class ApiResponse
{
    /**
     * Respon berhasil (Success - 2xx).
     */
    public static function success($data, string $message = 'Data retrieved successfully', int $code = 200, array $meta = [])
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'service_name' => 'Prasyarat-Kurikulum-Service',
                'api_version' => 'v1',
            ], $meta),
        ];

        return response()->json($response, $code);
    }

    /**
     * Respon gagal (Error - 4xx/5xx).
     */
    public static function error(string $message = 'Something went wrong', int $code = 500, $errors = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
