<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function success(mixed $data, string $message = "Success", int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    public function error(string $message, int $statusCode = 400) : JsonResponse
    {
        return response()->json([
            'data' => null,
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}
