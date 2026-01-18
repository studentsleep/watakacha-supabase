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
        $request->authenticate();

        $request->session()->regenerate();

        // เช็ค Role เพื่อพาไปหน้าที่เหมาะสม
        if ($request->user()->user_type_id == 1) {
            // ผู้จัดการ -> ไปหน้า Dashboard กราฟ
            return redirect()->intended(route('dashboard', absolute: false));
        } elseif ($request->user()->user_type_id == 2) {
            // พนักงาน -> ไปหน้าเช่าชุดทันที (หรือ calendar แล้วแต่เลือก)
            return redirect()->intended(route('reception.rental', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
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
