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
use App\Models\Accessory;
use App\Models\RentalAccessory;
use App\Models\Payment; // อย่าลืม use Payment
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceptionController extends Controller
{
    // =========================================================================
    // ส่วนที่ 1: หน้าจัดการการเช่า (Rental System)
    // =========================================================================

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
            // ส่งเฉพาะของที่มี stock > 0 ไปให้เลือก (แต่ตอน submit จะเช็ควันว่างอีกที)
            'accessories' => Accessory::where('stock', '>', 0)->get(),
        ];

        return view('reception.rental', $data);
    }

    /**
     * บันทึกข้อมูลการเช่าลงฐานข้อมูล (Store Rental)
     */
    public function storeRental(Request $request)
    {
        // 1. Validate ข้อมูล
        $request->validate([
            'deposit_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'rental_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:rental_date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'accessories' => 'nullable|array',
            'accessories.*.id' => 'required|exists:accessories,id',
            'accessories.*.quantity' => 'required|integer|min:1',
        ]);

        // 2. เช็คสต็อก Item (Advance Booking Check - Buffer 5 วัน)
        foreach ($request->items as $itemData) {
            if (!$this->isItemAvailable($itemData['id'], $request->rental_date, $request->return_date, $itemData['quantity'])) {
                $itemName = \App\Models\Item::find($itemData['id'])->item_name;
                return response()->json(['success' => false, 'message' => "สินค้า '{$itemName}' ไม่ว่างในช่วงเวลานั้น (ติดจอง หรืออยู่ในช่วงเตรียมของ 5 วัน)"], 400);
            }
        }

        // 3. เช็คสต็อก Accessories (Advance Booking Check)
        if ($request->has('accessories')) {
            foreach ($request->accessories as $accData) {
                if (!$this->isAccessoryAvailable($accData['id'], $request->rental_date, $request->return_date, $accData['quantity'])) {
                    $accName = \App\Models\Accessory::find($accData['id'])->name;
                    return response()->json(['success' => false, 'message' => "อุปกรณ์เสริม '{$accName}' ไม่ว่างในช่วงเวลานั้น"], 400);
                }
            }
        }

        DB::beginTransaction();
        try {
            // 4. สร้าง Rental Header
            $rental = new Rental();
            $rental->member_id = $request->member_id;
            $rental->user_id = Auth::id();
            $rental->rental_date = $request->rental_date;
            $rental->return_date = $request->return_date;
            $rental->promotion_id = $request->promotion_id;
            $rental->makeup_id = $request->makeup_id;
            $rental->photographer_id = $request->photographer_id;
            $rental->package_id = $request->package_id;
            $rental->status = 'rented';
            $rental->total_amount = $request->total_amount;
            $rental->save();

            // 5. บันทึก Rental Items 
            // (หมายเหตุ: ไม่ตัด Stock จริง เพราะใช้ Logic ตรวจสอบวันว่างแทน)
            foreach ($request->items as $itemData) {
                RentalItem::create([
                    'rental_id' => $rental->rental_id,
                    'item_id' => $itemData['id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                ]);
            }

            // 6. บันทึก Accessories
            if ($request->has('accessories')) {
                foreach ($request->accessories as $accData) {
                    $dbAccessory = \App\Models\Accessory::find($accData['id']);
                    if ($dbAccessory) {
                        DB::table('rental_accessories')->insert([
                            'rental_id' => $rental->rental_id,
                            'accessory_id' => $accData['id'],
                            'quantity' => $accData['quantity'],
                            'price' => $dbAccessory->price,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // 7. บันทึก Payment (มัดจำ)
            if ($request->deposit_amount > 0) {
                Payment::create([
                    'rental_id' => $rental->rental_id,
                    'amount' => $request->deposit_amount,
                    'payment_method' => $request->payment_method,
                    'type' => 'deposit', // ระบุว่าเป็นค่ามัดจำ
                    'status' => 'paid',
                    'payment_date' => now(),
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'บันทึกการเช่าเรียบร้อยแล้ว',
                'rental_id' => $rental->rental_id, // ส่ง ID กลับไป
                'staff_name' => Auth::user()->name // ส่งชื่อพนักงานกลับไป (สมมติว่าตาราง users มี column name)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'บันทึกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // ส่วนที่ 2: Helper Functions สำหรับเช็คสต็อก (Advance Booking)
    // =========================================================================

    // เช็คว่า "สินค้าหลัก" ว่างไหมในช่วงเวลานั้น
    // Private Function เช็คของว่างตามช่วงเวลา (แก้ไขให้รองรับ PostgreSQL/MySQL)
    private function isItemAvailable($itemId, $rentalDate, $returnDate, $requestQty)
    {
        $buffer = 5; // วันเผื่อหน้า-หลัง

        // คำนวณวันที่เปรียบเทียบเตรียมไว้เลย (ย้าย logic มาทำใน PHP)
        // สูตร: (RentalStart - 5) <= ReqEnd  ==>  RentalStart <= (ReqEnd + 5)
        //       (RentalEnd + 5) >= ReqStart  ==>  RentalEnd >= (ReqStart - 5)

        $checkStart = Carbon::parse($rentalDate)->subDays($buffer);
        $checkEnd = Carbon::parse($returnDate)->addDays($buffer);

        // หาจำนวนของที่ "ถูกจองอยู่" ในช่วงเวลาที่ทับซ้อน
        $reservedQty = DB::table('rental_items')
            ->join('rentals', 'rental_items.rental_id', '=', 'rentals.rental_id')
            ->where('rental_items.item_id', $itemId)
            ->whereNotIn('rentals.status', ['returned', 'cancelled']) // ไม่นับรายการที่คืนแล้ว
            ->where(function ($query) use ($checkStart, $checkEnd) {
                // ใช้ where ปกติ แทน whereRaw เพื่อรองรับทุก Database
                $query->where('rentals.rental_date', '<=', $checkEnd)
                    ->where('rentals.return_date', '>=', $checkStart);
            })
            ->sum('rental_items.quantity');

        // ดึงจำนวนของทั้งหมดที่มีในร้าน
        $totalStock = Item::where('id', $itemId)->value('stock');

        // ถ้า ของที่มี - ของที่ถูกจอง >= ของที่ขอเช่า แสดงว่าว่าง
        return ($totalStock - $reservedQty) >= $requestQty;
    }

    // เช็คว่า "อุปกรณ์เสริม" ว่างไหมในช่วงเวลานั้น
    // เพิ่มฟังก์ชันตรวจสอบสต็อกสำหรับ Accessories (แก้ไขให้รองรับ PostgreSQL/MySQL)
    private function isAccessoryAvailable($accId, $rentalDate, $returnDate, $requestQty)
    {
        $buffer = 5;

        $checkStart = Carbon::parse($rentalDate)->subDays($buffer);
        $checkEnd = Carbon::parse($returnDate)->addDays($buffer);

        $reservedQty = DB::table('rental_accessories')
            ->join('rentals', 'rental_accessories.rental_id', '=', 'rentals.rental_id')
            ->where('rental_accessories.accessory_id', $accId)
            ->whereNotIn('rentals.status', ['returned', 'cancelled'])
            ->where(function ($query) use ($checkStart, $checkEnd) {
                $query->where('rentals.rental_date', '<=', $checkEnd)
                    ->where('rentals.return_date', '>=', $checkStart);
            })
            ->sum('rental_accessories.quantity');

        $totalStock = Accessory::where('id', $accId)->value('stock');

        return ($totalStock - $reservedQty) >= $requestQty;
    }

    // =========================================================================
    // ส่วนที่ 3: ระบบคืนชุด (Return System)
    // =========================================================================

    public function returnIndex(Request $request)
    {
        // ดึงเฉพาะรายการที่ยังไม่คืน
        $query = Rental::with(['member', 'items.item'])
            ->where('status', 'rented');

        // ระบบค้นหา
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rental_id', 'LIKE', "%{$search}%")
                    ->orWhereHas('member', function ($m) use ($search) {
                        $m->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('tel', 'LIKE', "%{$search}%");
                    });
            });
        }

        $rentals = $query->orderBy('return_date', 'asc')->paginate(10);
        return view('reception.return', compact('rentals'));
    }

    /**
     * ประมวลผลการคืนชุด (Process Return)
     * - บันทึกของเสีย, ค่าปรับ
     * - รับชำระเงินส่วนที่เหลือ
     * - เปลี่ยนสถานะเป็น returned (เพื่อให้สต็อกว่างสำหรับคนอื่น)
     */
    public function processReturn(Request $request, $rentalId)
    {
        DB::beginTransaction();
        try {
            $rental = Rental::with(['items', 'payments'])->findOrFail($rentalId);

            if ($rental->status !== 'rented') {
                return response()->json(['success' => false, 'message' => 'รายการนี้สถานะไม่ถูกต้อง หรือถูกคืนไปแล้ว'], 400);
            }

            // 1. รับค่าจากหน้าบ้าน
            $itemsDamage = $request->input('items_damage', []);
            $overdueFine = $request->input('overdue_fine', 0);
            $paymentMethod = $request->input('payment_method', 'cash');

            // 2. คำนวณยอดค้างชำระ (ค่าเช่า - ยอดที่จ่ายไปแล้ว)
            $totalRentalPrice = $rental->total_amount;
            $totalPaid = $rental->payments->where('status', 'paid')->sum('amount');
            $remainingAmount = max(0, $totalRentalPrice - $totalPaid);

            // 3. วนลูปบันทึกความเสียหาย
            $totalDamageFine = 0;
            foreach ($itemsDamage as $damage) {
                // ค้นหารายการสินค้านั้นในบิล
                $rentalItem = RentalItem::where('rental_id', $rental->rental_id)
                    ->where('item_id', $damage['item_id'])
                    ->first();

                if ($rentalItem) {
                    // บันทึกรายละเอียดความเสียหายต่อท้าย (Append Description)
                    $newNote = "[เสีย {$damage['qty']} ชิ้น: {$damage['note']} (ปรับ " . number_format($damage['fine']) . ")]";
                    $rentalItem->description = trim($rentalItem->description . ' ' . $newNote);

                    // เพิ่มยอดค่าปรับของชิ้นนั้น
                    $rentalItem->fine_amount += $damage['fine'];
                    $rentalItem->save();

                    $totalDamageFine += $damage['fine'];
                }
            }

            // 4. คำนวณยอดเงินรวมที่ต้องจ่ายวันนี้ (ค้างชำระ + ปรับช้า + ปรับเสีย)
            $grandTotalToPay = $remainingAmount + $overdueFine + $totalDamageFine;

            // 5. บันทึก Payment (ถ้ายอด > 0)
            if ($grandTotalToPay > 0) {
                Payment::create([
                    'rental_id' => $rental->rental_id,
                    'amount' => $grandTotalToPay,
                    'payment_method' => $paymentMethod,
                    'type' => 'fine_remaining', // ประเภทรายการ: เคลียร์ยอดคงค้างและค่าปรับ
                    'status' => 'paid',
                    'payment_date' => now(),
                ]);
            }

            // 6. การจัดการ Stock
            // *** สำคัญ: ไม่ต้องคืนสต็อก (increment) เพราะตอนเช่าเราไม่ได้ตัดสต็อก (decrement) ***
            // ระบบใช้ Time-based checking ดังนั้นพอสถานะเปลี่ยนเป็น 'returned'
            // ฟังก์ชัน isItemAvailable จะมองว่าช่วงเวลานี้ว่างเองโดยอัตโนมัติ

            // 7. ปิดงาน
            $rental->status = 'returned';
            $rental->fine_amount = $overdueFine + $totalDamageFine; // เก็บยอดค่าปรับรวมไว้ที่หัวบิลด้วย
            $rental->save();

            // 8. ให้แต้มสมาชิก (ถ้ามี)
            if ($rental->member_id) {
                $pointsEarned = floor($rental->total_amount / 100); // 100 บาท = 1 แต้ม
                if ($pointsEarned > 0) {
                    $member = MemberAccount::find($rental->member_id);
                    if ($member) {
                        $member->increment('points', $pointsEarned);
                        PointTransaction::create([
                            'member_id' => $member->member_id,
                            'rental_id' => $rental->rental_id,
                            'point_change' => $pointsEarned,
                            'change_type' => 'earn',
                            'description' => 'ได้รับแต้มจากการเช่า #' . $rental->rental_id,
                            'transaction_date' => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'บันทึกการคืนและรับชำระเงินเรียบร้อยแล้ว']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // ส่วนที่ 4: ประวัติการเช่า (History)
    // =========================================================================

    public function history(Request $request)
    {
        $query = Rental::with([
            'member',       // ข้อมูลลูกค้า
            'user',         // ข้อมูลพนักงานขาย (แก้เรื่องชื่อหาย)
            'payments',     // ประวัติการจ่ายเงิน
            'items.item',   // รายการสินค้าหลัก + รายละเอียดสินค้า
            'accessories'   // รายการอุปกรณ์เสริม
        ]);

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rental_id', 'LIKE', "%{$search}%")
                    ->orWhereHas('member', function ($m) use ($search) {
                        $m->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('tel', 'LIKE', "%{$search}%");
                    });
            });
        }

        $rentals = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('reception.history', compact('rentals'));
    }

    // =========================================================================
    // ส่วนที่ 5: API Helpers (สำหรับ AJAX)
    // =========================================================================

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
     * ค้นหาสินค้า (แก้ไขใหม่ให้กรองเฉพาะของที่ว่างในช่วงวันเช่า)
     */
    public function searchItems(Request $request)
    {
        $query = $request->get('q');
        // รับวันที่เช่า-คืนจากหน้าบ้าน ถ้าไม่มีให้ใช้วันปัจจุบัน
        $rentalDate = $request->get('rental_date', now()->toDateString());
        $returnDate = $request->get('return_date', now()->addDays(1)->toDateString());

        // 1. ดึงสินค้าทั้งหมดที่ตรงกับคำค้นหา
        $items = Item::where('stock', '>', 0)
            ->where('status', 'active')
            ->where(function ($sq) use ($query) {
                $sq->where('item_name', 'LIKE', "%{$query}%")
                    ->orWhere('id', $query);
            })
            ->limit(20) // จำกัดจำนวนเพื่อความเร็ว
            ->get();

        // 2. กรองเฉพาะชิ้นที่ว่างในช่วงเวลานั้น (ใช้ isItemAvailable)
        $availableItems = $items->filter(function ($item) use ($rentalDate, $returnDate) {
            return $this->isItemAvailable($item->id, $rentalDate, $returnDate, 1);
        });

        return response()->json($availableItems->values());
    }

    // =========================================================================
    // ส่วนที่ 6: ประวัติบริการ (Service History)
    // =========================================================================

    public function serviceHistory(Request $request)
    {
        $query = Rental::with(['member', 'makeupArtist', 'photographer', 'photographerPackage'])
            ->where(function ($q) {
                $q->whereNotNull('makeup_id')
                    ->orWhereNotNull('photographer_id');
            });

        if ($request->has('type') && $request->type != 'all') {
            if ($request->type == 'makeup') {
                $query->whereNotNull('makeup_id');
            } elseif ($request->type == 'photo') {
                $query->whereNotNull('photographer_id');
            }
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('member', function ($m) use ($search) {
                    $m->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('makeupArtist', function ($mk) use ($search) {
                        $mk->where('first_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('photographer', function ($ph) use ($search) {
                        $ph->where('first_name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $services = $query->orderBy('rental_date', 'desc')->paginate(15);
        return view('reception.service_history', compact('services'));
    }

    // =========================================================================
    // ส่วนที่ 7: ประวัติการชำระเงิน (Payment History)
    // =========================================================================

    public function paymentHistory(Request $request)
    {
        $query = Payment::with(['rental.member', 'rental.user']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rental_id', 'like', "%{$search}%")
                    ->orWhereHas('rental.member', function ($m) use ($search) {
                        $m->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);
        return view('reception.payment_history', compact('payments'));
    }
}
