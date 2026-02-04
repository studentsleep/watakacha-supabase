<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    // 🏠 หน้าแรก: โชว์ 8 ชิ้นล่าสุด
    public function index()
    {
        // Cache 60 นาที เพื่อความเร็ว
        $items = Cache::remember('welcome_items', 60, function () {
            return Item::with('images')
                ->where('status', 'active')
                ->where('stock', '>', 0)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        });

        return view('welcome', compact('items'));
    }

    // 🛍️ หน้าแคตตาล็อก: ระบบค้นหาจัดเต็ม + ตัวกรอง
    public function catalog(Request $request)
    {
        $search = $request->input('search');
        $typeId = $request->input('type_id'); // รับค่า Filter ประเภท

        // 1. Eager Load (ดึงตารางลูกมารอไว้เลย เพื่อประสิทธิภาพและกันข้อมูลหาย)
        $query = Item::with(['images', 'type', 'unit'])
            ->where('status', 'active');

        // 2. 🔍 ระบบค้นหาอัจฉริยะ (ค้นหาข้ามตาราง)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'ILIKE', "%{$search}%")      // ค้นชื่อสินค้า (ใช้ ILIKE สำหรับ Postgres)
                    ->orWhere('description', 'ILIKE', "%{$search}%")  // ค้นคำอธิบาย
                    ->orWhereHas('type', function ($t) use ($search) { // 🟢 ค้นในชื่อประเภท
                        $t->where('name', 'ILIKE', "%{$search}%");
                    })
                    ->orWhereHas('unit', function ($u) use ($search) { // 🟢 ค้นในชื่อหน่วยนับ
                        $u->where('name', 'ILIKE', "%{$search}%");
                    });
            });
        }

        // 3. 📂 กรองตามหมวดหมู่ (เมื่อกดจาก Sidebar)
        if ($typeId) {
            $query->where('item_type_id', $typeId);
        }

        // 4. จัดเรียงและแบ่งหน้า
        $items = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString(); // จำค่า Search ไว้ตอนกดเปลี่ยนหน้า

        // ส่งข้อมูลประเภทสินค้าไปทำ Sidebar
        $types = ItemType::all();

        return view('catalog', compact('items', 'types'));
    }

    // 🎉 หน้าโปรโมชั่น
    public function promotions()
    {
        $today = Carbon::now();

        $promotions = Promotion::where('status', 'active')
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
            })
            ->orderBy('end_date', 'asc')
            ->get();

        return view('promotions', compact('promotions'));
    }

    // 📞 หน้าติดต่อเรา
    public function contact()
    {
        return view('contact');
    }
}
