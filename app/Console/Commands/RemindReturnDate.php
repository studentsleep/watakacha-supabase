<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RemindReturnDate extends Command
{
    // ชื่อคำสั่งสำหรับใช้เรียก
    protected $signature = 'remind:return-date';

    // คำอธิบายคำสั่ง
    protected $description = 'แจ้งเตือนลูกค้าล่วงหน้า 1 วันก่อนถึงวันคืนชุด';

    public function handle()
    {
        // หาวันพรุ่งนี้
        $tomorrow = Carbon::tomorrow()->toDateString();

        // ดึงรายการที่กำลังเช่า และต้องคืนพรุ่งนี้
        $rentals = Rental::with(['member', 'items.item'])
            ->where('status', 'rented')
            ->whereDate('return_date', $tomorrow)
            ->get();

        $count = 0;

        foreach ($rentals as $rental) {
            if ($rental->member && $rental->member->line_user_id) {

                // ดึงชื่อชุดมาแสดง
                $itemNames = [];
                foreach ($rental->items as $rItem) {
                    if ($rItem->item) {
                        $itemNames[] = $rItem->item->item_name;
                    }
                }
                $itemText = count($itemNames) > 0 ? implode(', ', $itemNames) : 'ชุดแต่งงาน/อุปกรณ์';

                // ข้อความแจ้งเตือน
                $msg = "🔔 แจ้งเตือนกำหนดคืนชุด (พรุ่งนี้!)\n\n"
                    . "รายการบิล: #{$rental->rental_id}\n"
                    . "ชุดที่เช่า: {$itemText}\n"
                    . "กำหนดคืน: " . Carbon::parse($rental->return_date)->format('d/m/Y') . "\n\n"
                    . "⚠️ หมายเหตุ: หากคืนชุดเกินกำหนด จะมีค่าปรับวันละ 100 บาท/ชุด นะคะ รบกวนนำชุดมาคืนตามกำหนดด้วยน้า ✨";

                // ส่งเข้า LINE
                $token = env('LINE_CHANNEL_ACCESS_TOKEN');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ])->post('https://api.line.me/v2/bot/message/push', [
                    'to' => $rental->member->line_user_id,
                    'messages' => [['type' => 'text', 'text' => $msg]]
                ]);

                if ($response->successful()) {
                    $count++;
                }
            }
        }

        Log::info("ส่งแจ้งเตือนคืนชุดล่วงหน้า 1 วัน เรียบร้อยแล้ว จำนวน: {$count} รายการ");
        $this->info("Successfully sent {$count} reminders.");
    }
}
