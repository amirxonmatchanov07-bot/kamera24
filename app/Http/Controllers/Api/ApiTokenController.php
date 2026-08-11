<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'api_token' => $request->user()->api_token,
        ]);
    }

    public function regenerate(Request $request): JsonResponse
    {
        $token = $request->user()->generateApiToken();

        return response()->json([
            'message' => 'Yangi token yaratildi',
            'api_token' => $token,
        ]);
    }
}
