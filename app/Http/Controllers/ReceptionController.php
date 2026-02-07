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
use App\Models\Payment;
use App\Models\ItemMaintenance;
use App\Models\RentalAccessory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReceptionController extends Controller
{
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
            'accessories' => Accessory::where('stock', '>', 0)->get(),
        ];

        return view('reception.rental', $data);
    }

    // ฟังก์ชันแสดงประวัติแต้ม
    public function pointHistory(Request $request)
    {
        $query = PointTransaction::with('member');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'ILIKE', "%{$search}%")
                    ->orWhere('last_name', 'ILIKE', "%{$search}%")
                    ->orWhere('member_id', 'ILIKE', "%{$search}%");
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('reception.point_history', compact('transactions'));
    }

    // =========================================================================
    // 📅 ส่วนที่ 1: หน้าประวัติ (History) - ส่งข้อมูล Master Data ไปด้วย
    // =========================================================================
    public function history(Request $request)
    {
        // 1. ส่วนรอกดยืนยันชำระเงิน (Pending Payment)
        $pending = Rental::with(['member', 'items.item', 'accessories'])
            ->where('status', Rental::STATUS_PENDING_PAYMENT)
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. ส่วนประวัติการเช่า (Active: รอรับชุด + กำลังเช่า)
        $active = Rental::with(['member', 'items.item', 'accessories'])
            ->whereIn('status', [Rental::STATUS_AWAITING_PICKUP, Rental::STATUS_RENTED])
            ->orderBy('rental_date', 'asc')
            ->get();

        // 3. ประวัติการคืน (History: คืนแล้ว + ยกเลิก)
        $historyQuery = Rental::with(['member', 'items.item', 'accessories'])
            ->whereIn('status', [Rental::STATUS_RETURNED, Rental::STATUS_CANCELLED]);

        if ($request->has('search')) {
            $search = $request->search;
            $historyQuery->where(function ($q) use ($search) {
                $q->whereRaw("CAST(rental_id AS TEXT) ILIKE ?", ["%{$search}%"])
                    ->orWhereHas('member', function ($m) use ($search) {
                        $m->where('first_name', 'ILIKE', "%{$search}%")
                            ->orWhere('tel', 'ILIKE', "%{$search}%");
                    });
            });
        }

        $history = $historyQuery->orderBy('updated_at', 'desc')->paginate(10);

        // ✅ ข้อมูล Master Data สำหรับ Dropdown ในหน้าแก้ไข
        $promotions = Promotion::where('status', 'active')->where(function ($q) {
            $q->whereNull('end_date')->orWhere('end_date', '>=', now());
        })->get();
        $makeup_artists = MakeupArtist::where('status', 'active')->get();
        $photographers = Photographer::where('status', 'active')->get();
        $photo_packages = PhotographerPackage::all();
        $accessories = Accessory::where('stock', '>', 0)->get();

        return view('reception.history', compact('pending', 'active', 'history', 'promotions', 'makeup_artists', 'photographers', 'photo_packages', 'accessories'));
    }

    // =========================================================================
    // 🚀 2. สร้างรายการเช่า (จองของ)
    // =========================================================================
    public function storeRental(Request $request)
    {
        $request->validate([
            'rental_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        $rentalDate = Carbon::parse($request->rental_date);
        $returnDate = $rentalDate->copy()->addDays(6);

        // เช็คสต็อก (รวมถึงสถานะ Pending ด้วย)
        foreach ($request->items as $itemData) {
            if (!$this->isItemAvailable($itemData['id'], $rentalDate->toDateString(), $itemData['quantity'])) {
                $itemName = Item::find($itemData['id'])->item_name;
                return response()->json(['success' => false, 'message' => "สินค้า '{$itemName}' ไม่ว่างในช่วงเวลาดังกล่าว"], 400);
            }
        }

        if ($request->has('accessories')) {
            foreach ($request->accessories as $accData) {
                if (!$this->isAccessoryAvailable($accData['id'], $rentalDate->toDateString(), $accData['quantity'])) {
                    $accName = Accessory::find($accData['id'])->name;
                    return response()->json(['success' => false, 'message' => "อุปกรณ์เสริม '{$accName}' ไม่ว่างในช่วงเวลาดังกล่าว"], 400);
                }
            }
        }

        DB::beginTransaction();
        try {
            $description = null;
            if (!$request->member_id) {
                $guestName = $request->guest_name ?? '-';
                $guestPhone = $request->guest_phone ?? '-';
                $description = "คุณ" . $guestName . " โทร " . $guestPhone;
            }

            $rental = new Rental();
            $rental->member_id = $request->member_id;
            $rental->user_id = Auth::id();
            $rental->rental_date = $rentalDate;
            $rental->return_date = $returnDate;
            $rental->promotion_id = $request->promotion_id;
            $rental->makeup_id = $request->makeup_id;
            $rental->photographer_id = $request->photographer_id;
            $rental->package_id = $request->package_id;
            $rental->status = Rental::STATUS_PENDING_PAYMENT;
            $rental->description = $description;
            $rental->total_amount = $request->total_amount;
            $rental->save();

            foreach ($request->items as $itemData) {
                RentalItem::create([
                    'rental_id' => $rental->rental_id,
                    'item_id' => $itemData['id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                ]);
            }

            if ($request->has('accessories')) {
                foreach ($request->accessories as $accData) {
                    $dbAccessory = Accessory::find($accData['id']);
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'จองสำเร็จ! กรุณายืนยันการชำระเงิน',
                'rental_id' => $rental->rental_id,
                'redirect_url' => route('reception.history')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'บันทึกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // ✏️ 3. แก้ไขรายการเช่า (Edit Rental) - เฉพาะสถานะ Pending
    // =========================================================================
    public function updateRental(Request $request, $rentalId)
    {
        $rental = Rental::findOrFail($rentalId);

        if ($rental->status !== Rental::STATUS_PENDING_PAYMENT) {
            return response()->json(['success' => false, 'message' => 'แก้ไขได้เฉพาะรายการที่รอชำระเงินเท่านั้น'], 400);
        }

        DB::beginTransaction();
        try {
            // 1. อัปเดตข้อมูล Header
            $rental->rental_date = Carbon::parse($request->rental_date);
            $rental->return_date = Carbon::parse($request->rental_date)->addDays(6);
            $rental->promotion_id = $request->promotion_id;
            $rental->makeup_id = $request->makeup_id;
            $rental->photographer_id = $request->photographer_id;
            $rental->package_id = $request->package_id;

            // 2. ล้างรายการเก่าออก (Re-save strategy)
            RentalItem::where('rental_id', $rentalId)->delete();
            DB::table('rental_accessories')->where('rental_id', $rentalId)->delete();

            // 3. ใส่รายการใหม่ (Main Items)
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    RentalItem::create([
                        'rental_id' => $rental->rental_id,
                        'item_id' => $itemData['item_id'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                    ]);
                }
            }

            // 4. ใส่รายการใหม่ (Accessories)
            if ($request->has('accessories')) {
                foreach ($request->accessories as $accData) {
                    $acc = Accessory::find($accData['id']);
                    if ($acc) {
                        DB::table('rental_accessories')->insert([
                            'rental_id' => $rental->rental_id,
                            'accessory_id' => $accData['id'],
                            'quantity' => $accData['quantity'],
                            'price' => $acc->price,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // 5. อัปเดตยอดเงินรวม
            $rental->total_amount = $request->total_amount;
            $rental->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'บันทึกการแก้ไขเรียบร้อยแล้ว']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // 💰 4. ยืนยันชำระเงิน (Confirm Payment)
    // =========================================================================
    public function confirmPayment(Request $request, $rentalId)
    {
        // 🔍 1. Debug: ดูว่ามีค่าอะไรส่งมาบ้าง (เช็คใน storage/logs/laravel.log)
        Log::info("Confirm Payment Request for Rental ID: {$rentalId}", $request->all());

        // 2. Validation (ถ้าไม่ผ่าน มันจะส่งกลับเป็น 422 JSON โดยอัตโนมัติ)
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string', // cash, transfer, credit_card
            'points_used' => 'nullable|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            $rental = Rental::findOrFail($rentalId);

            // 🔍 3. Debug Status: ดูว่าสถานะปัจจุบันคืออะไร
            Log::info("Current Rental Status: " . $rental->status);

            // เช็คสถานะ (แนะนำให้เช็คแบบ Trim string เผื่อมีเว้นวรรค)
            if (trim($rental->status) !== 'pending_payment') {
                return response()->json([
                    'success' => false,
                    'message' => 'ทำรายการไม่ได้: สถานะปัจจุบันคือ ' . $rental->status
                ], 400);
            }

            // จัดการแต้ม
            $pointsUsed = $request->points_used ?? 0;
            if ($rental->member_id && $pointsUsed > 0) {
                $member = MemberAccount::find($rental->member_id);

                // เช็คว่ามีสมาชิกจริงไหม และแต้มพอไหม
                if (!$member) {
                    return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลสมาชิก'], 400);
                }
                if ($member->points < $pointsUsed) {
                    return response()->json(['success' => false, 'message' => "แต้มไม่พอ (มี {$member->points} ใช้ {$pointsUsed})"], 400);
                }

                $member->decrement('points', $pointsUsed);

                // ⚠️ เช็ค Model PointTransaction ว่ามี fillable ครบไหม
                PointTransaction::create([
                    'member_id' => $member->member_id,
                    'rental_id' => $rental->rental_id,
                    'point_change' => -$pointsUsed,
                    'change_type' => 'redeem',
                    'description' => 'ใช้แต้มแลกส่วนลด (มัดจำ)',
                    'transaction_date' => now(),
                ]);
            }

            // ⚠️ เช็ค Model Payment ว่ามี fillable ครบไหม
            Payment::create([
                'rental_id' => $rental->rental_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'type' => 'deposit',
                'status' => 'paid',
                'payment_date' => now(),
            ]);

            $rental->status = 'awaiting_pickup';
            $rental->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'บันทึกการชำระเงินเรียบร้อย']);
        } catch (\Exception $e) {
            DB::rollBack();

            // 🔍 4. Log Error ที่แท้จริง: บันทึก error ลงไฟล์ log
            Log::error("Confirm Payment Error: " . $e->getMessage());
            Log::error($e->getTraceAsString()); // ดูบรรทัดที่เกิดเหตุ

            return response()->json([
                'success' => false,
                'message' => 'System Error: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
            ], 500);
        }
    }

    // =========================================================================
    // 📦 5. ยืนยันรับชุด (Confirm Pickup)
    // =========================================================================
    public function confirmPickup($rentalId)
    {
        $rental = Rental::findOrFail($rentalId);

        if ($rental->status !== Rental::STATUS_AWAITING_PICKUP) {
            return back()->with('error', 'สถานะไม่ถูกต้อง (ต้องชำระเงินก่อน)');
        }

        $rental->status = Rental::STATUS_RENTED;
        $rental->save();

        return back()->with('success', 'ยืนยันการรับชุดเรียบร้อย สถานะ: กำลังเช่า');
    }

    // =========================================================================
    // ❌ 6. ยกเลิกบิล (Cancel Rental)
    // =========================================================================
    public function cancelRental($rentalId)
    {
        DB::beginTransaction();
        try {
            $rental = Rental::findOrFail($rentalId);

            if (in_array($rental->status, [Rental::STATUS_RETURNED, Rental::STATUS_CANCELLED])) {
                return back()->with('error', 'รายการนี้ไม่สามารถยกเลิกได้');
            }

            // คืนแต้ม (ถ้าใช้ไปแล้ว)
            $redeemTrans = PointTransaction::where('rental_id', $rentalId)->where('change_type', 'redeem')->first();
            if ($redeemTrans) {
                $member = MemberAccount::find($rental->member_id);
                if ($member) {
                    $pointsToReturn = abs($redeemTrans->point_change);
                    $member->increment('points', $pointsToReturn);
                    PointTransaction::create([
                        'member_id' => $member->member_id,
                        'rental_id' => $rental->rental_id,
                        'point_change' => $pointsToReturn,
                        'change_type' => 'refund',
                        'description' => 'คืนแต้มจากการยกเลิกบิล',
                        'transaction_date' => now(),
                    ]);
                }
            }

            $rental->status = Rental::STATUS_CANCELLED;
            $rental->save();

            DB::commit();
            return back()->with('success', 'ยกเลิกบิลเรียบร้อย คืนแต้มและสต็อกแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 🔄 7. รับคืนชุด (Return) + แจกแต้ม
    // =========================================================================
    public function returnIndex(Request $request)
    {
        $query = Rental::with(['member', 'payments', 'items.item', 'items.accessory', 'accessories'])
            ->where('status', Rental::STATUS_RENTED);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CAST(rental_id AS TEXT) ILIKE ?", ["%{$search}%"])
                    ->orWhereHas('member', function ($m) use ($search) {
                        $m->where('first_name', 'ILIKE', "%{$search}%")->orWhere('tel', 'ILIKE', "%{$search}%");
                    });
            });
        }
        $rentals = $query->orderBy('return_date', 'asc')->paginate(10);
        return view('reception.return', compact('rentals'));
    }

    public function processReturn(Request $request, $rentalId)
    {
        DB::beginTransaction();
        try {
            $rental = Rental::with(['items', 'accessories', 'payments'])->findOrFail($rentalId);

            if ($rental->status !== Rental::STATUS_RENTED) {
                return response()->json(['success' => false, 'message' => 'รายการนี้สถานะไม่ถูกต้อง'], 400);
            }

            $itemsDamage = $request->input('items_damage', []);
            $overdueFine = $request->input('overdue_fine', 0);
            $paymentMethod = $request->input('payment_method', 'cash');

            $totalRentalPrice = $rental->total_amount;
            $totalPaid = $rental->payments->where('status', 'paid')->sum('amount');
            $remainingAmount = max(0, $totalRentalPrice - $totalPaid);
            $totalDamageFine = 0;
            $damageNotes = [];

            // 1. จัดการความเสียหาย (Damages) & ค่าปรับ
            foreach ($itemsDamage as $damage) {
                $isAccessory = $damage['is_accessory'] ?? false;
                $targetId = $damage['item_id'];
                $fine = $damage['fine'];
                $qty = $damage['qty'];
                $note = $damage['note'];

                if ($isAccessory) {
                    // 🔴 ลบ หรือ Comment ส่วนนี้ออกครับ (ตัวต้นเหตุ Error) 🔴
                    /* DB::table('rental_accessories')
                        ->where('rental_id', $rental->rental_id)
                        ->where('accessory_id', $targetId)
                        ->update([]); 
                    */

                    // ✅ เก็บแค่ Logic คำนวณเงินและ Note ก็พอครับ
                    $totalDamageFine += $fine;

                    // เก็บ Note ไว้ใช้ตอนสร้างใบซ่อม
                    $key = 'acc_' . $targetId;
                    if (!isset($damageNotes[$key])) $damageNotes[$key] = "";
                    $damageNotes[$key] .= "[เสีย {$qty}: {$note} (ปรับ " . number_format($fine) . ")] ";
                } else {
                    // กรณีชุดหลัก (ทำเหมือนเดิม)
                    $rentalItem = RentalItem::where('rental_id', $rental->rental_id)
                        ->where('item_id', $targetId)
                        ->first();

                    if ($rentalItem) {
                        $newNote = "[เสีย {$qty}: {$note} (ปรับ " . number_format($fine) . ")]";
                        $rentalItem->description = trim($rentalItem->description . ' ' . $newNote);
                        $rentalItem->fine_amount += $fine;
                        $rentalItem->save();

                        $key = 'item_' . $targetId;
                        if (!isset($damageNotes[$key])) $damageNotes[$key] = "";
                        $damageNotes[$key] .= $note . ", ";
                    }
                    $totalDamageFine += $fine; // บวกค่าปรับรวม
                }
            }

            // 2. ส่งซัก/ซ่อม (สร้าง ItemMaintenance)

            // 2.1 สำหรับชุดหลัก
            foreach ($rental->items as $rentalLine) {
                if ($rentalLine->item_id) {
                    $key = 'item_' . $rentalLine->item_id;
                    $note = isset($damageNotes[$key]) ? rtrim($damageNotes[$key], ", ") : 'ส่งซักปกติ';

                    ItemMaintenance::create([
                        'rental_id' => $rental->rental_id,
                        'item_id' => $rentalLine->item_id,
                        'accessory_id' => null,
                        'status' => 'pending',
                        'damage_description' => $note,
                        'type' => isset($damageNotes[$key]) ? 'repair' : 'laundry'
                    ]);

                    $item = Item::find($rentalLine->item_id);
                    if ($item) {
                        $item->status = 'maintenance';
                        $item->save();
                    }
                }
            }

            // 2.2 สำหรับอุปกรณ์เสริม
            foreach ($rental->accessories as $acc) {
                $key = 'acc_' . $acc->id;
                $note = isset($damageNotes[$key]) ? rtrim($damageNotes[$key], ", ") : 'ทำความสะอาด/เช็คสภาพ';

                ItemMaintenance::create([
                    'rental_id' => $rental->rental_id,
                    'item_id' => null,
                    'accessory_id' => $acc->id,
                    'status' => 'pending',
                    'damage_description' => $note,
                    'type' => isset($damageNotes[$key]) ? 'repair' : 'laundry'
                ]);
            }

            // 3. จ่ายเงินส่วนต่าง
            $grandTotalToPay = $remainingAmount + $overdueFine + $totalDamageFine;
            if ($grandTotalToPay > 0) {
                Payment::create([
                    'rental_id' => $rental->rental_id,
                    'amount' => $grandTotalToPay,
                    'payment_method' => $paymentMethod,
                    'type' => 'fine_remaining',
                    'status' => 'paid',
                    'payment_date' => now(),
                ]);
            }

            // 4. อัปเดตสถานะบิล
            $rental->status = Rental::STATUS_RETURNED;
            $rental->return_date = now();
            $rental->fine_amount = $overdueFine + $totalDamageFine;
            $rental->save();

            // 5. ให้แต้ม
            if ($rental->member_id) {
                $pointsEarned = floor($rental->total_amount / 100);
                if ($pointsEarned > 0) {
                    $member = MemberAccount::find($rental->member_id);
                    if ($member) {
                        $member->increment('points', $pointsEarned);
                        PointTransaction::create([
                            'member_id' => $member->member_id,
                            'rental_id' => $rental->rental_id,
                            'point_change' => $pointsEarned,
                            'change_type' => 'earn',
                            'description' => "ได้รับแต้มจากการเช่า (คืนชุดสำเร็จ)",
                            'transaction_date' => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'บันทึกการคืนสำเร็จ (ส่งซัก/ซ่อมเรียบร้อย)']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Return Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // 🛠️ Helpers & Others
    // =========================================================================

    private function isItemAvailable($itemId, $newStartDate, $requestQty)
    {
        $newStart = Carbon::parse($newStartDate);
        $newEnd   = $newStart->copy()->addDays(9);
        $reservedQty = DB::table('rental_items')
            ->join('rentals', 'rental_items.rental_id', '=', 'rentals.rental_id')
            ->where('rental_items.item_id', $itemId)
            ->whereNotIn('rentals.status', [Rental::STATUS_RETURNED, Rental::STATUS_CANCELLED])
            ->where(function ($query) use ($newStart, $newEnd) {
                $query->whereRaw("rentals.rental_date <= ?", [$newEnd])
                    ->whereRaw("(rentals.rental_date + INTERVAL '9 day') >= ?", [$newStart]);
            })->sum('rental_items.quantity');
        $totalStock = Item::where('id', $itemId)->value('stock');
        return ($totalStock - $reservedQty) >= $requestQty;
    }

    private function isAccessoryAvailable($accId, $newStartDate, $requestQty)
    {
        $newStart = Carbon::parse($newStartDate);
        $newEnd   = $newStart->copy()->addDays(9);
        $reservedQty = DB::table('rental_accessories')
            ->join('rentals', 'rental_accessories.rental_id', '=', 'rentals.rental_id')
            ->where('rental_accessories.accessory_id', $accId)
            ->whereNotIn('rentals.status', [Rental::STATUS_RETURNED, Rental::STATUS_CANCELLED])
            ->where(function ($query) use ($newStart, $newEnd) {
                $query->whereRaw("rentals.rental_date <= ?", [$newEnd])
                    ->whereRaw("(rentals.rental_date + INTERVAL '9 day') >= ?", [$newStart]);
            })->sum('rental_accessories.quantity');
        $totalStock = Accessory::where('id', $accId)->value('stock');
        return ($totalStock - $reservedQty) >= $requestQty;
    }

    private function calculateAvailableQty($itemId, $rentalDate)
    {
        $newStart = Carbon::parse($rentalDate);
        $newEnd   = $newStart->copy()->addDays(9);
        $reservedQty = DB::table('rental_items')
            ->join('rentals', 'rental_items.rental_id', '=', 'rentals.rental_id')
            ->where('rental_items.item_id', $itemId)
            ->whereNotIn('rentals.status', [Rental::STATUS_RETURNED, Rental::STATUS_CANCELLED])
            ->where(function ($query) use ($newStart, $newEnd) {
                $query->whereRaw("rentals.rental_date <= ?", [$newEnd])
                    ->whereRaw("(rentals.rental_date + INTERVAL '9 day') >= ?", [$newStart]);
            })->sum('rental_items.quantity');
        $totalStock = Item::where('id', $itemId)->value('stock');
        return max(0, $totalStock - $reservedQty);
    }

    public function searchItems(Request $request)
    {
        $query = $request->get('q');
        $rentalDate = $request->get('rental_date', now()->toDateString());
        $items = Item::where('stock', '>', 0)
            ->where('status', 'active')
            ->where(function ($sq) use ($query) {
                $sq->where('item_name', 'ILIKE', "%{$query}%")->orWhereRaw("CAST(id AS TEXT) ILIKE ?", ["%{$query}%"]);
            })->limit(20)->get();

        $items = $items->map(function ($item) use ($rentalDate) {
            $item->available_stock = $this->calculateAvailableQty($item->id, $rentalDate);
            return $item;
        });

        return response()->json($items->filter(function ($item) {
            return $item->available_stock > 0;
        })->values());
    }

    // =========================================================================
    // 🗓️ Calendar & History (Others)
    // =========================================================================

    public function calendar()
    {
        return view('reception.calendar');
    }

    public function getCalendarEvents()
    {
        // ดึง Rental พร้อมรายการสินค้า (items) และอุปกรณ์เสริม (accessories)
        $rentals = Rental::with(['member', 'items.item', 'accessories'])->where('status', '!=', Rental::STATUS_CANCELLED)->get();
        $events = [];
        $today = Carbon::now()->startOfDay();

        foreach ($rentals as $rental) {
            $customerName = $rental->member ? ($rental->member->first_name . ' ' . $rental->member->last_name) : ($rental->description ?? 'Guest');

            // ✅ รวมชื่อสินค้าและอุปกรณ์เสริม
            $itemNames = [];
            // 1. ชุดหลัก
            foreach ($rental->items as $rItem) {
                if ($rItem->item) {
                    $itemNames[] = $rItem->item->item_name;
                }
            }
            // 2. อุปกรณ์เสริม
            foreach ($rental->accessories as $rAcc) {
                $itemNames[] = $rAcc->name . " (Accessory)";
            }

            // ตัดคำถ้ายาวเกินไป
            $itemText = count($itemNames) > 0 ? implode(', ', array_slice($itemNames, 0, 2)) : 'No Item';
            if (count($itemNames) > 2) {
                $itemText .= " +" . (count($itemNames) - 2);
            }

            if (!$rental->rental_date || !$rental->return_date) continue;

            $rentalStart = Carbon::parse($rental->rental_date);
            $returnDate = Carbon::parse($rental->return_date);
            $rentalEnd = $returnDate->copy()->addDay();

            // กำหนดสี
            $color = '#4285F4';
            if ($rental->status === Rental::STATUS_PENDING_PAYMENT) $color = '#F59E0B';
            elseif ($rental->status === Rental::STATUS_AWAITING_PICKUP) $color = '#8B5CF6';
            elseif ($rental->status === Rental::STATUS_RETURNED) $color = '#9CA3AF';
            elseif ($returnDate->lt($today) && $rental->status === Rental::STATUS_RENTED) $color = '#EF4444';

            $title = "#{$rental->rental_id} {$customerName} ({$itemText})";
            $events[] = [
                'title' => $title,
                'start' => $rentalStart->toDateString(),
                'end' => $rentalEnd->toDateString(),
                'color' => $color,
                'textColor' => '#FFFFFF',
                'allDay' => true,
                'url' => route('reception.history', ['search' => $rental->rental_id]),
                'extendedProps' => ['type' => 'rental', 'tel' => $rental->member ? $rental->member->tel : ($rental->guest_phone ?? '-')]
            ];

            // Event ซ่อมบำรุง (Maintenance)
            $maintStart = $returnDate->copy()->addDay();
            $maintEnd = $maintStart->copy()->addDays(3);
            $events[] = [
                'title' => "🔧 ดูแล: #{$rental->rental_id} ({$itemText})",
                'start' => $maintStart->toDateString(),
                'end' => $maintEnd->toDateString(),
                'color' => '#FEF3C7',
                'textColor' => '#92400e',
                'allDay' => true,
                'extendedProps' => ['type' => 'maintenance']
            ];
        }
        return response()->json($events);
    }

    public function checkMember(Request $request)
    {
        $m = MemberAccount::where('tel', $request->get('q'))
            ->orWhere('member_id', $request->get('q'))
            ->first();
        return response()->json($m ? ['success' => true, 'member' => $m] : ['success' => false]);
    }

    public function serviceHistory(Request $request)
    {
        $query = Rental::with(['member', 'makeupArtist', 'photographer', 'photographerPackage'])->where(function ($q) {
            $q->whereNotNull('makeup_id')->orWhereNotNull('photographer_id');
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
                    $m->where('first_name', 'LIKE', "%{$search}%")->orWhere('last_name', 'LIKE', "%{$search}%");
                })->orWhereHas('makeupArtist', function ($mk) use ($search) {
                    $mk->where('first_name', 'LIKE', "%{$search}%");
                })->orWhereHas('photographer', function ($ph) use ($search) {
                    $ph->where('first_name', 'LIKE', "%{$search}%");
                });
            });
        }
        $services = $query->orderBy('rental_date', 'desc')->paginate(15);
        return view('reception.service_history', compact('services'));
    }

    public function paymentHistory(Request $request)
    {
        $query = Payment::with(['rental.member', 'rental.user']);
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CAST(rental_id AS TEXT) ILIKE ?", ["%{$search}%"])->orWhereHas('rental.member', function ($m) use ($search) {
                    $m->where('first_name', 'ILIKE', "%{$search}%")->orWhere('last_name', 'ILIKE', "%{$search}%");
                });
            });
        }
        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);
        return view('reception.payment_history', compact('payments'));
    }

    public function createMember()
    {
        return view('reception.members.create');
    }

    public function storeMember(Request $request)
    {
        $request->validate([
            'tel' => 'required|string|numeric|digits_between:9,10|unique:member_accounts,tel',
            'password' => 'required|digits:6',
        ], [
            'tel.unique' => 'เบอร์โทรศัพท์นี้เป็นสมาชิกอยู่แล้ว',
            'tel.digits_between' => 'เบอร์โทรศัพท์ไม่ถูกต้อง',
            'password.digits' => 'รหัสผ่านต้องเป็นวันเดือนปีเกิด 6 หลักเท่านั้น (เช่น 260119)'
        ]);

        $member = new MemberAccount();
        $member->tel = $request->tel;
        $member->username = $request->tel;
        $member->last_name = $request->tel;
        $member->first_name = 'ลูกค้า';
        $member->password = Hash::make($request->password);
        $member->email = $request->tel . '@noemail.com';
        $member->points = 0;
        $member->status = 'active';
        $member->save();

        return redirect()->route('reception.member.create')
            ->with('status', 'สมัครสมาชิกสำเร็จ! เบอร์: ' . $request->tel);
    }
}
