<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. เช็คว่าล็อกอินหรือยัง
        if (!$user) {
            return redirect()->route('login');
        }

        // 2. ดึงชื่อตำแหน่งมาแปลงเป็นตัวพิมพ์เล็ก
        $role = strtolower($user->userType->name ?? '');

        // 3. กำหนดตำแหน่งที่ "อนุญาต" ให้ผ่านไปได้
        $allowedRoles = ['manager', 'reception'];

        // 4. ถ้าอยู่ในกลุ่มที่อนุญาต -> ให้ผ่านไป (return $next)
        if (in_array($role, $allowedRoles)) {
            return $next($request);
        }

        // 5. ถ้าไม่ใช่ (เป็นแม่บ้าน, ช่างภาพ ฯลฯ) -> ส่งไปหน้า unauthorized
        // เช็คก่อนว่าไม่ได้อยู่ที่หน้า unauthorized อยู่แล้ว (เพื่อป้องกัน Loop)
        if ($request->routeIs('unauthorized')) {
            return $next($request);
        }

        return redirect()->route('unauthorized');
    }
}
