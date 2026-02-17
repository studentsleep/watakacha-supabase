<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LiffController extends Controller
{
    // =========================================================================
    // 1. หน้าแสดงฟอร์ม Login (เปิดผ่าน LIFF)
    // =========================================================================
    public function index()
    {
        return view('liff.login');
    }

    // =========================================================================
    // 2. ฟังก์ชัน Login (Username + Password)
    // =========================================================================
    public function login(Request $request)
    {
        // 2.1 Validate ข้อมูล
        $request->validate([
            'username'     => 'required|string',
            'password'     => 'required|string',
            'line_user_id' => 'required', // ต้องรับค่านี้มาจาก LIFF
        ]);

        // 2.2 ค้นหาสมาชิก
        $member = MemberAccount::where('username', $request->username)->first();

        // 2.3 ตรวจสอบรหัสผ่าน
        if ($member && Hash::check($request->password, $member->password)) {

            // --- 🔗 ผูกบัญชี LINE (Auto-Binding) ---
            if (empty($member->line_user_id)) {
                $member->line_user_id = $request->line_user_id;
                $member->save();
            } else {
                if ($member->line_user_id !== $request->line_user_id) {
                    return back()->withErrors(['msg' => 'บัญชีนี้ถูกผูกกับไลน์อื่นไปแล้ว']);
                }
            }

            // --- 🔑 Login เข้าสู่ระบบ ด้วย Guard 'member' ---
            // ใส่ true เพื่อ "Remember Me"
            Auth::guard('member')->login($member, true);

            // --- 🎨 เปลี่ยน Rich Menu เป็นแบบ Member ---
            $this->linkRichMenuToUser($request->line_user_id);

            // --- 🚀 Redirect ไปหน้าลูกค้า (Member Zone) ---
            return redirect()->route('member.history');
        }

        return back()->withErrors(['msg' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
    }

    // =========================================================================
    // 2.5 ฟังก์ชัน สมัครสมาชิกใหม่ผ่าน LIFF (Register + Auto Binding)
    // =========================================================================
    public function register(Request $request)
    {
        // 1. Validate ข้อมูล
        $request->validate([
            'username'     => 'required|string|unique:member_accounts,username',
            'first_name'   => 'required|string',
            'last_name'    => 'required|string',
            'tel'          => 'required|string',
            'password'     => 'required|string|min:6', // ตั้งรหัสผ่านขั้นต่ำ 6 ตัว
            'line_user_id' => 'required',
        ], [
            'username.unique' => 'ชื่อผู้ใช้นี้มีคนใช้แล้ว กรุณาเปลี่ยนใหม่',
            'password.min'    => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'
        ]);

        // 2. สร้างสมาชิกใหม่
        $member = MemberAccount::create([
            'username'     => $request->username,
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'tel'          => $request->tel,
            'password'     => Hash::make($request->password),
            'line_user_id' => $request->line_user_id, // ผูก LINE ทันที
        ]);

        // 3. Login เข้าสู่ระบบอัตโนมัติ
        Auth::guard('member')->login($member, true);

        // 4. เปลี่ยน Rich Menu เป็นแบบ Member
        $this->linkRichMenuToUser($request->line_user_id);

        // 5. เด้งไปหน้าประวัติ
        return redirect()->route('member.history')->with('success', 'สมัครสมาชิกสำเร็จ!');
    }

    // =========================================================================
    // 3. ฟังก์ชันเช็ค Auto Login (ใช้ตอนเปิด LIFF ครั้งแรก)
    // =========================================================================
    public function checkAutoLogin(Request $request)
    {
        $lineId = $request->line_user_id;

        if (!$lineId) {
            return response()->json(['status' => 'error', 'message' => 'No Line ID']);
        }

        $member = MemberAccount::where('line_user_id', $lineId)->first();

        if ($member) {
            // เจอว่าผูกไว้แล้ว -> Login ด้วย Guard 'member' เลย
            Auth::guard('member')->login($member, true);

            // เปลี่ยน Rich Menu เผื่อไว้ (กันเหนียว)
            $this->linkRichMenuToUser($lineId);

            return response()->json([
                'status' => 'found',
                'redirect' => route('member.history')
            ]);
        }

        return response()->json(['status' => 'not_found']);
    }

    // =========================================================================
    // 4. ฟังก์ชัน Logout (ออกจากระบบ + คืนค่าเมนูเดิม)
    // =========================================================================
    public function logout()
    {
        // เช็ค User จาก Guard 'member'
        $user = Auth::guard('member')->user();

        if ($user && $user->line_user_id) {
            // ปลด Rich Menu Member ออก (กลับไปใช้ Default Menu A)
            $this->unlinkRichMenu($user->line_user_id);
        }

        // Logout ออกจาก Guard 'member'
        Auth::guard('member')->logout();

        // สร้าง View ง่ายๆ บอกว่าออกแล้ว หรือให้ปิดหน้าต่าง
        return '<script>
                    alert("ออกจากระบบเรียบร้อย"); 
                    if(typeof liff !== "undefined"){ liff.closeWindow(); } 
                    else { window.close(); }
                </script>';
    }

    // =========================================================================
    // 🛠️ PRIVATE HELPER: จัดการ Rich Menu
    // =========================================================================

    // ใส่เมนูสมาชิก (Menu B)
    private function linkRichMenuToUser($lineUserId)
    {
        // 🔴 ใส่ Rich Menu ID ของเมนูสมาชิก (Menu B) ที่นี่
        $richMenuIdMember = 'richmenu-969c757d6fc56beb4e02480c040279c8';
        $token = env('LINE_CHANNEL_ACCESS_TOKEN');

        if ($richMenuIdMember && $token) {
            Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                ->post("https://api.line.me/v2/bot/user/{$lineUserId}/richmenu/{$richMenuIdMember}");
        }
    }

    // ปลดเมนูสมาชิก (กลับไปเป็น Default)
    private function unlinkRichMenu($lineUserId)
    {
        $token = env('LINE_CHANNEL_ACCESS_TOKEN');
        if ($token) {
            Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                ->delete("https://api.line.me/v2/bot/user/{$lineUserId}/richmenu");
        }
    }
}
