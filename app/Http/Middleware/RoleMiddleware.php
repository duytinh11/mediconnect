<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Xử lý request trước khi vào Controller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Danh sách role được phép truy cập
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Kiểm tra người dùng có đăng nhập chưa và có role hợp lệ không
        if (!$user || !in_array($user->role, $roles, true)) {
            return response()->json([
                'message' => 'Forbidden — Bạn không có quyền truy cập tài nguyên này.',
            ], 403);
        }

        // Nếu hợp lệ thì cho request đi tiếp
        return $next($request);
    }
}
