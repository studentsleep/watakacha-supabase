<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LiffController extends Controller
{
    // 1. หน้าแสดงฟอร์ม Login (เปิดผ่าน LIFF)
    public function index()
    {
        return view('liff.login');
    }

    // 2. ฟังก์ชันตรวจสอบข้อมูลและผูกบัญชี
    public function login(Request $request)
    {
        $request->validate([
            'tel' => 'required',
            'password' => 'required',
            'line_user_id' => 'required', // สำคัญ! ต้องรับค่านี้มาจาก LIFF
        ]);

        // 2.1 ค้นหาสมาชิกจากเบอร์โทร
        // ตรวจสอบด้วยว่า line_user_id ต้องยังไม่ถูกใช้ หรือ เป็นของคนนี้อยู่แล้ว
        $member = MemberAccount::where('tel', $request->tel)->first();

        // 2.2 ตรวจสอบรหัสผ่าน (วันเดือนปีเกิด)
        if ($member && Hash::check($request->password, $member->password)) {

            // --- จุดสำคัญที่สุด (Auto-Binding) ---
            // ถ้าสมาชิกคนนี้ยังไม่มี line_id หรือต้องการอัปเดตใหม่ ให้บันทึกลงไป
            if (empty($member->line_user_id)) {
                $member->line_user_id = $request->line_user_id;
                $member->save();
            } else {
                // (Optional) กรณีเคยผูกกับไลน์อื่นมาแล้ว อาจจะต้องเช็คความปลอดภัยเพิ่ม
                if ($member->line_user_id !== $request->line_user_id) {
                    return back()->withErrors(['msg' => 'เบอร์นี้ถูกผูกกับไลน์อื่นไปแล้ว กรุณาติดต่อหน้าร้าน']);
                }
            }

            // 2.3 สั่ง Login เข้าสู่ระบบ (Laravel Auth)
            // สมมติว่าใช้ Guard ชื่อ 'member' (ถ้าใช้ web ธรรมดาก็ลบ ->guard('member') ออก)
            Auth::guard('web')->login($member);

            // 2.4 ส่งกลับไปหน้าประวัติ
            return redirect()->route('reception.history');
        }

        return back()->withErrors(['msg' => 'เบอร์โทรหรือรหัสผ่านไม่ถูกต้อง']);
    }

    // 3. ฟังก์ชันเช็ค Auto Login (ยิง Ajax มาถาม)
    public function checkAutoLogin(Request $request)
    {
        $lineId = $request->line_user_id;
        $member = MemberAccount::where('line_user_id', $lineId)->first();

        if ($member) {
            Auth::guard('web')->login($member);
            return response()->json(['status' => 'found', 'redirect' => route('reception.history')]);
        }

        return response()->json(['status' => 'not_found']);
    }
}
