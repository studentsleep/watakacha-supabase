<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MemberAuthController extends Controller
{
    // แสดงหน้า Login
    public function showLoginForm()
    {
        return view('member.auth.login');
    }

    // ประมวลผล Login
    public function login(Request $request)
    {
        // 1. รับค่า login_id (ซึ่งจะเป็น username หรือ tel ก็ได้) และ password
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. ตรวจสอบว่า login_id เป็น "เบอร์โทร" หรือ "Username"
        // ถ้าเป็นตัวเลขล้วน ให้ถือว่าเป็นเบอร์โทร
        $loginType = is_numeric($request->login_id) ? 'tel' : 'username';

        // 3. เตรียมข้อมูลสำหรับตรวจสอบ
        $credentials = [
            $loginType => $request->login_id,
            'password' => $request->password,
            'status'   => 'active' // (Optional) เช็คด้วยว่าต้อง Active เท่านั้น
        ];

        // 4. ทำการ Login ผ่าน Guard 'member'
        if (Auth::guard('member')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('welcome'));
        }

        return back()->withErrors([
            'login_id' => 'ข้อมูลเข้าสู่ระบบไม่ถูกต้อง หรือบัญชีถูกระงับ',
        ])->withInput($request->only('login_id'));
    }

    // แสดงหน้า Register
    public function showRegisterForm()
    {
        return view('member.auth.register');
    }

    // ✅ ประมวลผลการสมัครสมาชิก (แก้ Error member.store)
    public function store(Request $request)
    {
        $request->validate([
            'username'   => 'required|string|max:50|unique:member_accounts', // เช็คซ้ำในตาราง member_accounts
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'tel'        => 'required|string|max:15|unique:member_accounts',
            'email'      => 'nullable|email|max:150|unique:member_accounts', // email ซ้ำไม่ได้ (ถ้ามี)
            'password'   => 'required|string|min:8|confirmed', // ต้องมี field password_confirmation ส่งมาด้วย
        ]);

        // สร้างสมาชิกใหม่
        $member = MemberAccount::create([
            'username'   => $request->username,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'tel'        => $request->tel,
            'email'      => $request->email,
            'password'   => Hash::make($request->password), // Laravel 11+ model cast hashed แล้ว แต่ใส่ Hash::make ชัวร์สุด

            // ✅ ค่า Default อัตโนมัติ
            'status'     => 'active',
            'points'     => 0,
            'line_user_id' => null, // รอผูกทีหลัง
        ]);

        // Login ให้เลยหลังสมัครเสร็จ
        Auth::guard('member')->login($member);

        return redirect()->route('welcome')->with('success', 'สมัครสมาชิกสำเร็จ! ยินดีต้อนรับครับ');
    }

    // ออกจากระบบ
    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
