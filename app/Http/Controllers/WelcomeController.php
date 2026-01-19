<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class WelcomeController extends Controller
{
    public function index()
    {
        // ดึงข้อมูลสินค้า (เอาเฉพาะที่ Active) พร้อมรูปภาพ
        // สมมติว่ามี Relation 'images' และ scope 'active' หรือ where status
        // ถ้ายังไม่มี data จริง เดี๋ยวใน View ผมจะทำ Mockup ไว้ให้ครับ
        $items = Item::with('images')
            ->where('status', 'active') // สมมติว่ามี field status
            ->latest()
            ->take(12) // เอามาโชว์ 12 ชุดล่าสุด
            ->get();

        return view('welcome', compact('items'));
    }
}
