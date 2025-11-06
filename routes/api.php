<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Đây là nơi định nghĩa các route cho API.
| Mặc định, những route này được load thông qua RouteServiceProvider
| và tất cả đều có prefix "/api" trong URL (ví dụ: /api/login, /api/register).
|
*/

/**
 * 🧾 Auth Routes (FR1, FR4, FR8)
 */

// Đăng ký tài khoản mới
Route::post('/register', [AuthController::class, 'register']);

// Đăng nhập tài khoản
Route::post('/login', [AuthController::class, 'login']);

// Password reset
Route::post('/password/forgot', [PasswordResetController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetController::class, 'reset']);

// Các route yêu cầu đã đăng nhập (bảo vệ bằng Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout']);

    // Update profile
    Route::put('/profile', [ProfileController::class, 'update']);

    // (Tuỳ chọn) Kiểm tra token còn hiệu lực không
    Route::get('/me', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'user' => $request->user()->load(['doctor', 'patient'])
        ]);
    });
});
