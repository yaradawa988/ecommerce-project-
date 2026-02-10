<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Success response
     *
     * @param mixed  $data
     * @param string $message
     * @param int    $code
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Error response
     *
     * @param string $message
     * @param int    $code
     * @param mixed  $data
     * @return JsonResponse
     */
    public static function error(string $message = 'Error', int $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
}
