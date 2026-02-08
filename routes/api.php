<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// 👇 1. บรรทัดนี้สำคัญมาก! ต้องมีเพื่อเรียกใช้ Controller
use App\Http\Controllers\LineWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 👇 2. สร้าง Route สำหรับรับ Webhook
Route::post('/line/webhook', [LineWebhookController::class, 'handle']);
