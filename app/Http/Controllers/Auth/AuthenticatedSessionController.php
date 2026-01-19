<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. ตรวจสอบรหัสผ่าน
        $request->authenticate();

        // 2. สร้าง Session ใหม่
        $request->session()->regenerate();

        // 3. เช็คตำแหน่ง (Role)
        $role = strtolower($request->user()->userType->name ?? '');

        // 4. ส่งไปหน้าบ้านของตัวเอง
        if ($role === 'manager') {
            // ไปหน้า Dashboard ของ Manager
            return redirect()->intended(route('manager.dashboard', absolute: false));
        } elseif ($role === 'reception') {
            // ไปหน้าเช่าชุด
            return redirect()->intended(route('reception.rental', absolute: false));
        }

        // 5. ถ้าไม่ใช่ 2 ตำแหน่งบน ให้เด้งไปหน้า Unauthorized
        return redirect()->route('unauthorized');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
