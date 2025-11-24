<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberAccount;
use App\Models\Item;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\Promotion;
use App\Models\MakeupArtist;
use App\Models\Photographer;
use App\Models\PhotographerPackage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // เพิ่มการใช้งาน DB transaction

class ReceptionController extends Controller
{
    // หน้าหลักการเช่า
    public function index()
    {
        if (Auth::user()->user_type_id != 2 && Auth::user()->user_type_id != 1) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึง');
        }

        $data = [
            'promotions' => Promotion::where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                })->get(),
            'makeup_artists' => MakeupArtist::where('status', 'active')->get(),
            'photographers' => Photographer::where('status', 'active')->get(),
            'photo_packages' => PhotographerPackage::all(),
        ];

        return view('reception.rental', $data);
    }

    // AJAX: ตรวจสอบสมาชิก
    public function checkMember(Request $request)
    {
        $query = $request->get('q');
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

        if (empty($query)) {
            $items = Item::where('stock', '>', 0)
                ->where('status', 'active')
                ->inRandomOrder()
                ->limit(10)
                ->get();
            return response()->json($items);
        }

        $items = Item::where(function ($q) use ($query) {
            $q->where('item_name', 'LIKE', "%{$query}%")
                ->orWhere('id', $query);
        })
            ->where('stock', '>', 0)
            ->where('status', 'active')
            ->limit(10)
            ->get();

        if ($items->isEmpty()) {
            $items = Item::where('stock', '>', 0)
                ->where('status', 'active')
                ->inRandomOrder()
                ->limit(5)
                ->get();
        }

        return response()->json($items);
    }

    // บันทึกการเช่า
    public function storeRental(Request $request)
    {
        // 1. Validate ข้อมูล
        $request->validate([
            'member_id' => 'nullable',
            'rental_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:rental_date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.price' => 'required|numeric',
            'promotion_id' => 'nullable|exists:promotions,promotion_id',
            'makeup_id' => 'nullable|exists:makeup_artists,makeup_id',
            'photographer_id' => 'nullable|exists:photographers,photographer_id',
            'package_id' => 'nullable|exists:photographer_packages,package_id',
        ]);

        // ใช้ Transaction เพื่อความปลอดภัย (ถ้าบันทึกไม่ครบให้ Rollback ทั้งหมด)
        DB::beginTransaction();

        try {
            // 2. สร้าง Rental Header
            $rental = new Rental();
            $rental->member_id = $request->member_id;
            $rental->user_id = Auth::id();
            $rental->rental_date = $request->rental_date;
            $rental->return_date = $request->return_date;
            
            // บันทึกข้อมูลบริการเสริม (ต้องมั่นใจว่ามีคอลัมน์เหล่านี้ในตาราง rentals)
            $rental->promotion_id = $request->promotion_id;
            $rental->makeup_id = $request->makeup_id;
            $rental->photographer_id = $request->photographer_id;
            $rental->package_id = $request->package_id;
            
            $rental->status = 'rented';
            $rental->total_amount = $request->total_amount; // รับยอดรวมจากหน้าบ้าน

            $rental->save();

            // 3. สร้าง Rental Items และตัดสต็อก
            foreach ($request->items as $itemData) {
                // บันทึกลงตาราง rental_items
                RentalItem::insert([
                    'rental_id' => $rental->rental_id,
                    
                    // [จุดที่ต้องระวัง!] 
                    // ใน Model RentalItem.php คุณใช้ 'id' เป็น FK เชื่อมกับ Item
                    // ถ้าใน DB ตาราง rental_items คอลัมน์ชื่อ 'id' จริงๆ ให้ใช้บรรทัดนี้:
                    'id' => $itemData['id'], 
                    
                    // แต่ถ้าใน DB คอลัมน์ชื่อ 'item_id' (และ 'id' เป็น PK) ให้แก้เป็น:
                    // 'item_id' => $itemData['id'],

                    'quantity' => 1,
                    'price' => $itemData['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // ตัดสต็อก
                $dbItem = Item::find($itemData['id']);
                if ($dbItem) {
                    $dbItem->decrement('stock', 1);
                }
            }

            DB::commit(); // ยืนยันการบันทึก
            return response()->json(['success' => true, 'message' => 'บันทึกการเช่าเรียบร้อยแล้ว']);

        } catch (\Exception $e) {
            DB::rollBack(); // ยกเลิกถ้ามี error
            // ส่ง Error กลับไปดูว่าเกิดจากอะไร (เช่น Column not found)
            return response()->json(['success' => false, 'message' => 'บันทึกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }
    }
}