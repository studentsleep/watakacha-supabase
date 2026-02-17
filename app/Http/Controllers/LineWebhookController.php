<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // ใช้ยิง API หา LINE
use App\Models\MemberAccount;
use App\Models\Rental; // ⚠️ อย่าลืมสร้าง Model Rental (ถ้ายังไม่มี)

class LineWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. รับค่าที่ส่งมา
        $input = $request->all();
        Log::info('LINE Webhook:', $input); // เก็บ Log ไว้ดู

        // เช็คว่ามี events ส่งมาจริงไหม
        if (!isset($input['events']) || empty($input['events'])) {
            return response('OK', 200);
        }

        // 2. วนลูปเช็คทีละ event (เผื่อส่งมารัวๆ)
        foreach ($input['events'] as $event) {

            // เราสนใจแค่ event ที่เป็น "ข้อความ" (Message) และเป็น "ตัวหนังสือ" (Text)
            if ($event['type'] == 'message' && $event['message']['type'] == 'text') {

                $userMessage = trim($event['message']['text']); // ข้อความที่ลูกค้าพิมพ์
                $replyToken = $event['replyToken']; // ตั๋วสำหรับตอบกลับ
                $lineUserId = $event['source']['userId']; // ID ของลูกค้าคนนั้น

                // --- 🔍 ค้นหาลูกค้าในฐานข้อมูล ---
                $member = MemberAccount::where('line_user_id', $lineUserId)->first();

                // ถ้าไม่เจอสมาชิก (ยังไม่ได้ผูกบัญชี)
                if (!$member) {
                    // ถ้าเขากดถามแต้ม/เช่า แต่ยังไม่ผูกบัญชี ให้แจ้งเตือน
                    if ($userMessage == 'เช็คแต้ม' || $userMessage == 'เช็คสถานะการเช่า') {
                        $msg = "⚠️ คุณยังไม่ได้เชื่อมต่อบัญชีสมาชิก\nกรุณากดเมนู 'เข้าสู่ระบบ' ด้านล่างเพื่อผูกบัญชีก่อนนะครับ";
                        $this->replyMessage($replyToken, $msg);
                    }
                    continue; // ข้ามไปคนถัดไป
                }

                // --- ✅ Logic ตอบกลับตามคำสั่ง ---

                // กรณี 1: เช็คแต้ม
                if ($userMessage == 'เช็คแต้ม') {
                    $points = number_format($member->points);
                    $msg = "💎 คะแนนสะสมของคุณคือ: {$points} แต้ม";
                    $this->replyMessage($replyToken, $msg);
                }

                // กรณี 2: เช็คสถานะการเช่า
                elseif ($userMessage == 'เช็คสถานะการเช่า') {
                    // 1. ดึงข้อมูลโดยใช้ member_id (จาก Model MemberAccount)
                    // 2. ใช้ rental_id ในการเชื่อมความสัมพันธ์ (ตามที่กำหนดใน Model Rental)
                    $activeRentals = \App\Models\Rental::with(['items.item', 'accessories'])
                        ->where('member_id', $member->member_id) // ✅ ใช้ member_id ให้ตรงกับ PrimaryKey ของคุณ
                        ->whereNotIn('status', ['returned', 'cancelled', 'completed']) // ไม่เอาที่คืนหรือยกเลิกแล้ว
                        ->orderBy('rental_date', 'desc')
                        ->get();

                    if ($activeRentals->count() > 0) {
                        $msg = "📦 รายการเช่าปัจจุบันของคุณ:\n";
                        $msg .= "------------------------\n";

                        foreach ($activeRentals as $rental) {
                            // แปลงวันที่ให้สวยงาม
                            $rentalDate = $rental->rental_date ? $rental->rental_date->format('d/m/Y') : '-';
                            $dueDate = $rental->return_date ? $rental->return_date->format('d/m/Y') : '-';

                            $msg .= "🧾 เลขที่บิล: #{$rental->rental_id}\n"; // ✅ ใช้ rental_id
                            $msg .= "📅 วันเริ่มเช่า: {$rentalDate}\n";
                            $msg .= "⏳ กำหนดคืน: {$dueDate}\n";

                            // --- วนลูปแสดงรายการชุด ---
                            $msg .= "👗 รายการชุด:\n";
                            if ($rental->items && $rental->items->count() > 0) {
                                foreach ($rental->items as $rentalItem) {
                                    // ✅ อ้างอิงตาม RentalItem.php: $this->item->item_name
                                    $itemName = $rentalItem->item->item_name ?? 'ไม่ระบุชื่อชุด';
                                    $msg .= "   - {$itemName}\n";
                                }
                            } else {
                                $msg .= "   - (ไม่มีข้อมูลชุด)\n";
                            }

                            // --- วนลูปแสดงรายการเครื่องประดับ ---
                            if ($rental->accessories && $rental->accessories->count() > 0) {
                                $msg .= "💍 เครื่องประดับ:\n";
                                foreach ($rental->accessories as $acc) {
                                    // ✅ อ้างอิงตามตาราง Accessory ปกติมักใช้ name
                                    $msg .= "   - {$acc->name}\n";
                                }
                            }

                            // แปลงสถานะเป็นภาษาไทยให้ลูกค้าเข้าใจง่าย
                            $statusTh = [
                                'pending_payment' => 'รอชำระเงิน',
                                'awaiting_pickup' => 'รอรับของ',
                                'rented'          => 'กำลังเช่า',
                            ];
                            $currentStatus = $statusTh[$rental->status] ?? $rental->status;

                            $msg .= "📌 สถานะ: {$currentStatus}\n";
                            $msg .= "------------------------\n";
                        }
                    } else {
                        $msg = "✅ คุณไม่มีรายการเช่าค้างอยู่ในระบบค่ะ\nสนใจจองคิวลองชุด ทักแชทสอบถามแอดมินได้เลยนะคะ ✨";
                    }

                    $this->replyMessage($replyToken, $msg);
                }
            }
        }

        return response('OK', 200);
    }

    // --- ฟังก์ชันช่วยส่งข้อความกลับหา LINE ---
    private function replyMessage($replyToken, $textMessage)
    {
        $channelAccessToken = env('LINE_CHANNEL_ACCESS_TOKEN');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $channelAccessToken,
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $textMessage
                ]
            ]
        ]);

        // Log ดูผลลัพธ์การส่ง
        if ($response->failed()) {
            Log::error('LINE Reply Failed:', $response->json());
        }
    }
}
