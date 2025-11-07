<?php

use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\ContentController;
use App\Http\Controllers\Api\Admin\DoctorAdminController;
use App\Http\Controllers\Api\Admin\PatientAdminController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorSearchController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/**
 * Public auth routes
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [PasswordResetController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetController::class, 'reset']);

/**
 * Public doctor search/listing
 */
Route::get('/doctors', [DoctorSearchController::class, 'index']);
Route::get('/doctors/{doctor}', [DoctorSearchController::class, 'show']);

/**
 * Authenticated routes
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::apiResource('appointments', AppointmentController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy'])
        ->middleware('role:patient,doctor,admin');

    Route::get('/me', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'user' => $request->user()->load(['doctor', 'patient']),
        ]);
    });

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('cities', CityController::class);
        Route::apiResource('doctors', DoctorAdminController::class);
        Route::apiResource('patients', PatientAdminController::class);
        Route::apiResource('contents', ContentController::class);
    });
});
