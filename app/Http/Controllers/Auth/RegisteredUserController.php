<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // --- 1. แก้ไข Validation Rules ---
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'max:255', 'unique:' . User::class], // <--- เพิ่ม username
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tel' => ['nullable', 'string', 'max:20'], // <--- เพิ่ม tel (อนุญาตให้ว่าง)
        ]);

        // --- 2. แก้ไขการสร้าง User ---
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tel' => $request->tel,
            'user_type_id' => 1, // <--- ✨ เพิ่มบรรทัดนี้ (ตั้งค่าเริ่มต้น user type = 1)
            'status' => 'active',     // <--- ✨ เพิ่มบรรทัดนี้ (ตั้งค่าเริ่มต้น status = 1 (Active))
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
