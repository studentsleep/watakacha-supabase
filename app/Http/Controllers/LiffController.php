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
    // กำหนด LIFF ID เป็นค่าคงที่เพื่อเรียกใช้ง่ายๆ
    const LIFF_ID = '2009077441-uCh3VnXy';

    public function index()
    {
        return view('liff.login');
    }

    // =========================================================================
    // 2. ฟังก์ชัน Login (แก้ไขให้สั่งปิดหน้าต่าง LIFF)
    // =========================================================================
    public function login(Request $request)
    {
        $request->validate([
            'username'     => 'required|string',
            'password'     => 'required|string',
            'line_user_id' => 'required',
        ]);

        $member = MemberAccount::where('username', $request->username)->first();

        if ($member && Hash::check($request->password, $member->password)) {
            // ผูกบัญชี LINE
            if (empty($member->line_user_id)) {
                $member->line_user_id = $request->line_user_id;
                $member->save();
            } else {
                if ($member->line_user_id !== $request->line_user_id) {
                    return back()->withErrors(['msg' => 'บัญชีนี้ถูกผูกกับไลน์อื่นไปแล้ว']);
                }
            }

            Auth::guard('member')->login($member, true);
            $this->linkRichMenuToUser($request->line_user_id);

            // ✅ แก้ไข: ส่งสคริปต์ปิดหน้าต่าง LIFF แทนการ Redirect
            return $this->closeLiffWindow();
        }

        return back()->withErrors(['msg' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
    }

    // =========================================================================
    // 2.5 ฟังก์ชัน สมัครสมาชิก (แก้ไขให้สั่งปิดหน้าต่าง LIFF)
    // =========================================================================
    public function register(Request $request)
    {
        $request->validate([
            'username'     => 'required|string|unique:member_accounts,username',
            'first_name'   => 'required|string',
            'last_name'    => 'required|string',
            'tel'          => 'required|string',
            'password'     => 'required|string|min:6',
            'line_user_id' => 'required',
        ], [
            'username.unique' => 'ชื่อผู้ใช้นี้มีคนใช้แล้ว กรุณาเปลี่ยนใหม่',
            'password.min'    => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'
        ]);

        $member = MemberAccount::create([
            'username'     => $request->username,
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'tel'          => $request->tel,
            'password'     => Hash::make($request->password),
            'line_user_id' => $request->line_user_id,
        ]);

        Auth::guard('member')->login($member, true);
        $this->linkRichMenuToUser($request->line_user_id);

        // ✅ แก้ไข: ส่งสคริปต์ปิดหน้าต่าง LIFF
        return $this->closeLiffWindow("สมัครสมาชิกและผูกบัญชีสำเร็จ!");
    }

    // =========================================================================
    // 3. ฟังก์ชันเช็ค Auto Login (ใช้ AJAX เรียก ไม่ต้องแก้มาก)
    // =========================================================================
    public function checkAutoLogin(Request $request)
    {
        $lineId = $request->line_user_id;
        if (!$lineId) return response()->json(['status' => 'error']);

        $member = MemberAccount::where('line_user_id', $lineId)->first();

        if ($member) {
            Auth::guard('member')->login($member, true);
            $this->linkRichMenuToUser($lineId);

            return response()->json([
                'status' => 'found',
                'action' => 'close' // บอกฝั่ง JS ว่าให้ปิดหน้าต่าง
            ]);
        }
        return response()->json(['status' => 'not_found']);
    }

    // =========================================================================
    // 4. ฟังก์ชัน Logout
    // =========================================================================
    public function logout()
    {
        $user = Auth::guard('member')->user();
        if ($user && $user->line_user_id) {
            $this->unlinkRichMenu($user->line_user_id);
        }

        Auth::guard('member')->logout();

        // ✅ แก้ไข: สั่งปิดหน้าต่างหลังจาก Logout
        return $this->closeLiffWindow("ออกจากระบบเรียบร้อย");
    }

    // =========================================================================
    // 🛠️ PRIVATE HELPERS
    // =========================================================================

    // ฟังก์ชันช่วยสร้าง HTML/JS สำหรับปิดหน้าต่าง LIFF
    private function closeLiffWindow($message = null)
    {
        $alert = $message ? "alert('{$message}');" : "";
        $liffId = self::LIFF_ID;

        return "
            <script src='https://static.line-scdn.net/liff/edge/2/sdk.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    liff.init({ liffId: '{$liffId}' }).then(() => {
                        {$alert}
                        if (liff.isInClient()) {
                            liff.closeWindow();
                        } else {
                            window.close();
                        }
                    }).catch(() => {
                        {$alert}
                        window.close();
                    });
                });
            </script>
            <div style='text-align:center; padding-top:50px; font-family:sans-serif;'>
                <p>กำลังดำเนินการคซักครู่...</p>
            </div>
        ";
    }

    private function linkRichMenuToUser($lineUserId)
    {
        $richMenuIdMember = 'richmenu-969c757d6fc56beb4e02480c040279c8';
        $token = env('LINE_CHANNEL_ACCESS_TOKEN');

        if ($richMenuIdMember && $token) {
            Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                ->post("https://api.line.me/v2/bot/user/{$lineUserId}/richmenu/{$richMenuIdMember}");
        }
    }

    private function unlinkRichMenu($lineUserId)
    {
        $token = env('LINE_CHANNEL_ACCESS_TOKEN');
        if ($token) {
            Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                ->delete("https://api.line.me/v2/bot/user/{$lineUserId}/richmenu");
        }
    }
}
