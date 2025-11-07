<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    /**
     * Bao bọc closure, bắt ValidationException và Throwable, ghi log và trả JSON phù hợp.
     */
    protected function wrap(Closure $fn): JsonResponse
    {
        try {
            $result = $fn();

            // Nếu đã trả Response/JsonResponse thì giữ nguyên
            if ($result instanceof \Illuminate\Http\Response || $result instanceof \Illuminate\Http\JsonResponse) {
                return $result;
            }

            // Nếu trả array hoặc model/resource thì convert thành JSON response
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('API error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
