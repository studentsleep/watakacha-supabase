<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    // 🏠 หน้าแรก: โชว์แค่ 9 ชิ้นล่าสุด
    public function index()
    {
        // จำค่าไว้ 60 นาที (ถ้ามีการเพิ่มสินค้าใหม่ ต้องรอ 60 นาทีหรือกด clear cache ถึงจะเห็น)
        $items = Cache::remember('welcome_items', 60, function () {
            return Item::with('images') // Eager Loading (ถูกต้องแล้ว)
                ->where('status', 'active')
                ->where('stock', '>', 0)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        });

        return view('welcome', compact('items'));
    }

    // 🛍️ หน้าแคตตาล็อก: โชว์ทั้งหมด + ค้นหา
    public function catalog(Request $request)
    {
        $search = $request->input('search');

        $query = Item::with('images')->where('status', 'active');

        // ระบบค้นหา
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // แบ่งหน้าทีละ 12 ชิ้น
        $items = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        return view('catalog', compact('items'));
    }

    // 🎉 หน้าโปรโมชั่น
    public function promotions()
    {
        $today = Carbon::now();

        // ดึงโปรฯ ที่ Active และยังไม่หมดเขต
        $promotions = Promotion::where('status', 'active')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderBy('end_date', 'asc') // หมดเขตก่อนขึ้นก่อน
            ->get();

        return view('promotions', compact('promotions'));
    }

    // 📞 หน้าติดต่อเรา
    public function contact()
    {
        return view('contact');
    }
}
