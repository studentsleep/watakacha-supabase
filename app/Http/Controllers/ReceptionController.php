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
use App\Models\PointTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceptionController extends Controller
{
    // =========================================================================
    // ส่วนที่ 1: ระบบเช่า (Rental System)
    // =========================================================================

    /**
     * แสดงหน้าจอหลักสำหรับการเช่าชุด
     */
    public function index()
    {
        // ตรวจสอบสิทธิ์ (Manager=1, Reception=2)
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

    /**
     * บันทึกข้อมูลการเช่าลงฐานข้อมูล
     */
    public function storeRental(Request $request)
    {
        // 1. Validate ข้อมูลที่ส่งมา
        $request->validate([
            'member_id' => 'nullable',
            'rental_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:rental_date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.price' => 'required|numeric',
            // บริการเสริม
            'promotion_id' => 'nullable|exists:promotions,promotion_id',
            'makeup_id' => 'nullable|exists:makeup_artists,makeup_id',
            'photographer_id' => 'nullable|exists:photographers,photographer_id',
            'package_id' => 'nullable|exists:photographer_packages,package_id',
        ]);

        DB::beginTransaction();

        try {
            // 2. สร้าง Rental Header (ใบเช่าหลัก)
            $rental = new Rental();
            $rental->member_id = $request->member_id;
            $rental->user_id = Auth::id(); // พนักงานที่ทำรายการ
            $rental->rental_date = $request->rental_date;
            $rental->return_date = $request->return_date;
            
            // บันทึกบริการเสริม
            $rental->promotion_id = $request->promotion_id;
            $rental->makeup_id = $request->makeup_id;
            $rental->photographer_id = $request->photographer_id;
            $rental->package_id = $request->package_id;
            
            $rental->status = 'rented'; // สถานะเริ่มต้นคือ "กำลังเช่า"
            $rental->total_amount = $request->total_amount;
            $rental->save();

            // 3. บันทึกรายการสินค้า (Rental Items) และตัดสต็อก
            foreach ($request->items as $itemData) {
                RentalItem::create([
                    'rental_id' => $rental->rental_id,
                    'item_id' => $itemData['id'], // ใช้ item_id เชื่อมกับตาราง Items
                    'quantity' => 1,
                    'price' => $itemData['price'],
                    // fine_amount และ description จะถูกอัปเดตตอนคืนของ
                ]);

                // ตัดสต็อกสินค้า
                $dbItem = Item::find($itemData['id']);
                if ($dbItem) {
                    $dbItem->decrement('stock', 1);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'บันทึกการเช่าเรียบร้อยแล้ว']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'บันทึกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // ส่วนที่ 2: ระบบคืนชุด (Return System)
    // =========================================================================

    /**
     * แสดงหน้ารายการที่รอคืน (สถานะ rented)
     */
    public function returnIndex(Request $request)
    {
        // ดึงเฉพาะรายการที่ยังไม่คืน
        $query = Rental::with(['member', 'items.item'])
            ->where('status', 'rented'); 

        // ระบบค้นหา (Search)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('rental_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('member', function($m) use ($search) {
                      $m->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('tel', 'LIKE', "%{$search}%");
                  });
            });
        }

        // เรียงลำดับตามกำหนดคืน (ใครต้องคืนก่อนขึ้นก่อน)
        $rentals = $query->orderBy('return_date', 'asc')->paginate(10);

        // ต้องมีไฟล์ resources/views/reception/return.blade.php
        return view('reception.return', compact('rentals'));
    }

    /**
     * ประมวลผลการคืนชุด: บันทึกความเสียหาย, ค่าปรับ, คืนสต็อก และให้แต้ม
     */
    public function processReturn(Request $request, $rentalId)
    {
        DB::beginTransaction();
        try {
            $rental = Rental::with('items')->findOrFail($rentalId);

            // ป้องกันการคืนซ้ำ
            if ($rental->status !== 'rented') {
                return response()->json(['success' => false, 'message' => 'รายการนี้ถูกคืนไปแล้ว หรือสถานะไม่ถูกต้อง'], 400);
            }

            // รับข้อมูลจาก Frontend (ส่งมาเป็น JSON)
            $itemsDamage = $request->input('items_damage', []); // รายการของเสีย
            $overdueFine = $request->input('overdue_fine', 0); // ค่าปรับล่าช้าที่คำนวณมาแล้ว
            $totalDamageFine = 0;

            // 1. อัปเดตรายละเอียดสินค้า (Rental Items) ตามความเสียหาย
            foreach ($itemsDamage as $damageInfo) {
                if (!empty($damageInfo['damage_type'])) {
                    // ค้นหารายการย่อยด้วย ID ของตาราง rental_items
                    $rentalItem = RentalItem::find($damageInfo['id']); 
                    if ($rentalItem) {
                        $rentalItem->description = "เสียหาย: " . $damageInfo['damage_type'] . ($damageInfo['note'] ? " ({$damageInfo['note']})" : "");
                        $rentalItem->fine_amount = $damageInfo['fine_amount'];
                        $rentalItem->save();
                        
                        $totalDamageFine += $damageInfo['fine_amount'];
                    }
                }
            }

            // 2. อัปเดตสถานะใบเช่า (Rental Header)
            $rental->status = 'returned';
            // บันทึกยอดค่าปรับรวม (Overdue + Damage)
            $rental->fine_amount = $overdueFine + $totalDamageFine; 
            
            // ถ้ามีคอลัมน์ actual_return_date ใน DB ให้ uncomment บรรทัดนี้
            // $rental->actual_return_date = now(); 
            
            $rental->save();

            // 3. คืนสต็อกสินค้า
            foreach ($rental->items as $rentalItem) {
                $item = Item::find($rentalItem->item_id);
                if ($item) {
                    // คืนสต็อกกลับเข้าระบบ
                    $item->increment('stock', $rentalItem->quantity);
                }
            }

            // 4. คำนวณและให้แต้มสมาชิก (100 บาท = 1 แต้ม)
            // คิดจากยอดค่าเช่าปกติ (ไม่รวมค่าปรับ)
            if ($rental->member_id) {
                $pointsEarned = floor($rental->total_amount / 100);

                if ($pointsEarned > 0) {
                    $member = MemberAccount::find($rental->member_id);
                    if ($member) {
                        $member->increment('points', $pointsEarned);

                        // บันทึกประวัติการได้แต้ม
                        // ต้องมี Model PointTransaction และตาราง point_transactions
                        PointTransaction::create([
                            'member_id' => $member->member_id,
                            'points' => $pointsEarned,
                            'transaction_type' => 'earn', // ประเภท: ได้รับแต้ม
                            'description' => 'ได้รับแต้มจากการเช่า #' . $rental->rental_id,
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            
            // สร้างข้อความตอบกลับ
            $msg = "คืนชุดเรียบร้อย!";
            if ($rental->fine_amount > 0) {
                $msg .= "\n\n💰 มียอดค่าปรับรวม: " . number_format($rental->fine_amount, 2) . " บาท";
            }
            
            return response()->json(['success' => true, 'message' => $msg]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // ส่วนที่ 3: ประวัติการเช่า (History)
    // =========================================================================

    /**
     * แสดงประวัติการเช่าทั้งหมด พร้อมตัวกรอง
     */
    public function history(Request $request)
    {
        $query = Rental::with(['member', 'user']);

        // Filter ตามสถานะ
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Search (เลขที่บิล, ชื่อสมาชิก, เบอร์โทร)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('rental_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('member', function($m) use ($search) {
                      $m->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('tel', 'LIKE', "%{$search}%");
                  });
            });
        }

        // เรียงลำดับจากล่าสุดไปเก่าสุด
        $rentals = $query->orderBy('created_at', 'desc')->paginate(15);

        // ต้องมีไฟล์ resources/views/reception/history.blade.php
        return view('reception.history', compact('rentals'));
    }

    // =========================================================================
    // ส่วนที่ 4: API Helpers (สำหรับ AJAX)
    // =========================================================================

    /**
     * ตรวจสอบข้อมูลสมาชิก (ใช้ในหน้าเช่า)
     */
    public function checkMember(Request $request)
    {
        $query = $request->get('q');
        $member = MemberAccount::where('member_id', $query)
            ->orWhere('username', $query)
            ->orWhere('tel', $query)
            ->first();

        return $member 
            ? response()->json(['success' => true, 'member' => $member])
            : response()->json(['success' => false]);
    }

    /**
     * ค้นหาสินค้า (ใช้ในหน้าเช่า)
     */
    public function searchItems(Request $request)
    {
        $query = $request->get('q');
        
        // ค้นหาเฉพาะสินค้าที่มีสต็อกและสถานะ active
        $q = Item::where('stock', '>', 0)->where('status', 'active');

        if (!empty($query)) {
            $q->where(function ($sq) use ($query) {
                $sq->where('item_name', 'LIKE', "%{$query}%")
                   ->orWhere('id', $query);
            });
        } else {
            // ถ้าไม่ได้พิมพ์ค้นหา ให้สุ่มมาแสดง 10 รายการ
            $q->inRandomOrder()->limit(10);
        }

        return response()->json($q->get());
    }
}