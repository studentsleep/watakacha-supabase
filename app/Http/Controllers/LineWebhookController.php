<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LineWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. รับค่าที่ LINE ส่งมา (Log ดูว่าส่งอะไรมาบ้าง)
        $body = $request->all();
        Log::info('LINE Webhook Received:', $body);

        // 2. ตอบกลับ LINE ว่า "ได้รับแล้ว" (HTTP 200 OK)
        // สำคัญมาก! ถ้าไม่ตอบ 200 LINE จะถือว่าส่งไม่ผ่าน
        return response('OK', 200);
    }
}
