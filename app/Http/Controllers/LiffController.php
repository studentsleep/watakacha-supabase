<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // เพิ่ม Log เพื่อช่วยตรวจสอบปัญหา

class LiffController extends Controller
{
    // 1. หน้าแสดงฟอร์ม Login (เปิดผ่าน LIFF)
    public function index()
    {
        return view('liff.login');
    }

    // 2. ฟังก์ชันตรวจสอบข้อมูลและผูกบัญชี (ใช้ Username + Password)
    public function login(Request $request)
    {
        // 2.1 Validate ข้อมูล (เปลี่ยนจาก tel เป็น username)
        $request->validate([
            'username'     => 'required|string',
            'password'     => 'required|string',
            'line_user_id' => 'required', // สำคัญ! ต้องรับค่านี้มาจาก LIFF
        ]);

        // 2.2 ค้นหาสมาชิกจาก Username
        $member = MemberAccount::where('username', $request->username)->first();

        // 2.3 ตรวจสอบรหัสผ่าน
        if ($member && Hash::check($request->password, $member->password)) {

            // --- จุดสำคัญที่สุด (Auto-Binding) ---
            // ถ้าสมาชิกคนนี้ยังไม่มี line_id หรือต้องการอัปเดตใหม่ ให้บันทึกลงไป
            if (empty($member->line_user_id)) {
                $member->line_user_id = $request->line_user_id;
                $member->save();

                // 💡 (Optional) จุดนี้คือที่สำหรับ "ยิง API เปลี่ยน Rich Menu" 
                // ให้เป็นเมนูสมาชิก (ถ้าคุณทำฟังก์ชันนั้นเสร็จแล้ว ให้เรียกใช้ตรงนี้)

            } else {
                // กรณีเคยผูกกับไลน์อื่นมาแล้ว เช็คว่าตรงกับไลน์ปัจจุบันไหม
                if ($member->line_user_id !== $request->line_user_id) {
                    return back()->withErrors(['msg' => 'บัญชีนี้ถูกผูกกับไลน์อื่นไปแล้ว กรุณาติดต่อหน้าร้าน']);
                }
            }

            // 2.4 สั่ง Login เข้าสู่ระบบ (Laravel Auth)
            // ใช้ guard 'web' ตามปกติ
            Auth::guard('web')->login($member);

            // 2.5 ส่งกลับไปหน้าประวัติ (หรือหน้า Dashboard สมาชิก)
            return redirect()->route('reception.history');
        }

        // กรณี Login ไม่ผ่าน
        return back()->withErrors(['msg' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
    }

    // 3. ฟังก์ชันเช็ค Auto Login (ยิง Ajax มาถาม)
    // ฟังก์ชันนี้ไม่ต้องแก้ เพราะเช็คจาก line_user_id เหมือนเดิม
    public function checkAutoLogin(Request $request)
    {
        $lineId = $request->line_user_id;

        if (!$lineId) {
            return response()->json(['status' => 'error', 'message' => 'No Line ID']);
        }

        $member = MemberAccount::where('line_user_id', $lineId)->first();

        if ($member) {
            // ถ้าเจอว่าผูกไว้แล้ว ก็ Login ให้เลย
            Auth::guard('web')->login($member);
            return response()->json(['status' => 'found', 'redirect' => route('reception.history')]);
        }

        return response()->json(['status' => 'not_found']);
    }
}
