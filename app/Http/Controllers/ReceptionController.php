<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberAccount;
use App\Models\Item;
use App\Models\Rental; // คุณต้องสร้าง Model นี้ หรือใช้ตาราง rentals ที่มี
use App\Models\RentalItem; // Model สำหรับตารางลูก (ถ้ามี) หรือใช้ json
use App\Models\Promotion;           // [เพิ่ม]
use App\Models\MakeupArtist;        // [เพิ่ม]
use App\Models\Photographer;        // [เพิ่ม]
use App\Models\PhotographerPackage; // [เพิ่ม]
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReceptionController extends Controller
{
    // หน้าหลักการเช่า
    public function index()
    {
        // เช็คสิทธิ์ (เฉพาะ Reception หรือ Admin)
        if (Auth::user()->user_type_id != 2 && Auth::user()->user_type_id != 1) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึง');
        }

        $data = [
            'promotions' => Promotion::where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                })->get(),
            'makeup_artists' => MakeupArtist::where('status', 'active')->get(),
            'photographers' => Photographer::where('status', 'active')->get(),
            'photo_packages' => PhotographerPackage::all(), // แพ็คเกจมักไม่มี status
        ];

        return view('reception.rental', $data);
    }

    // AJAX: ตรวจสอบสมาชิก
    public function checkMember(Request $request)
    {
        $query = $request->get('q');
        // ค้นหาจาก member_id หรือ username หรือ เบอร์โทร
        $member = MemberAccount::where('member_id', $query)
                    ->orWhere('username', $query)
                    ->orWhere('tel', $query)
                    ->first();

        if ($member) {
            return response()->json(['success' => true, 'member' => $member]);
        }
        return response()->json(['success' => false]);
    }

    // AJAX: ค้นหาสินค้า
    public function searchItems(Request $request)
    {
        $query = $request->get('q');
        $items = Item::where('item_name', 'LIKE', "%{$query}%")
                    ->orWhere('id', $query) // ค้นหาด้วย ID สินค้า
                    ->where('stock', '>', 0) // ต้องมีของ
                    ->where('status', 'active')
                    ->limit(10)
                    ->get();

        return response()->json($items);
    }

    // บันทึกการเช่า
    public function storeRental(Request $request)
    {
        $request->validate([
            'member_id' => 'nullable|required|exists:member_accounts,member_id',
            'rental_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:rental_date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.price' => 'required|numeric',
        ]);

        // 1. สร้าง Rental Header (อิงตาม Structure ที่คุณเคยส่งมา rental.png)
        $rental = new Rental();
        $rental->member_id = $request->member_id;
        $rental->user_id = Auth::id(); // พนักงานที่ทำรายการ
        $rental->rental_date = $request->rental_date;
        $rental->return_date = $request->return_date;
        $rental->status = 'rented'; // หรือสถานะที่คุณใช้
        
        // คำนวณยอดรวม
        $totalAmount = 0;
        foreach ($request->items as $item) {
            $totalAmount += $item['price'];
        }
        $rental->total_amount = $totalAmount;
        $rental->save();

        // 2. สร้าง Rental Items (Detail) และตัดสต็อก
        foreach ($request->items as $itemData) {
            // บันทึกลงตาราง rental_items (ต้องสร้าง Model นี้ถ้ายังไม่มี)
            // RentalItem::create([...]); 
            // หรือถ้าใช้ตารางเดียวเก็บเป็น JSON ก็บันทึกใน step บน
            
            // *สมมติว่ามีตาราง rental_items*
             RentalItem::table('rental_items')->insert([
                 'rental_id' => $rental->rental_id, // ใช้ PK ที่เพิ่งสร้าง
                 'id' => $itemData['id'],
                 'quantity' => 1, // เช่าทีละ 1 หรือรับค่าจากหน้าบ้าน
                 'price' => $itemData['price']
             ]);

            // ตัดสต็อก
            $dbItem = Item::find($itemData['id']);
            $dbItem->decrement('stock', 1);
        }

        return response()->json(['success' => true, 'message' => 'บันทึกการเช่าเรียบร้อยแล้ว']);
    }
}