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
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    // ฟังก์ชันแสดงประวัติแต้ม (ย้ายมาจาก Manager)
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

    public function storeRental(Request $request)
    {
        // 1. Validate ข้อมูล
        $request->validate([
            'deposit_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'rental_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'points_used' => 'nullable|integer|min:0', // รับค่าแต้มที่ใช้
        ]);

        // คำนวณวันคืน (เช่า 7 วัน)
        $rentalDate = Carbon::parse($request->rental_date);
        $returnDate = $rentalDate->copy()->addDays(6);

        // 2. เช็คสต็อก Item (แบบ 10 วัน)
        foreach ($request->items as $itemData) {
            if (!$this->isItemAvailable($itemData['id'], $rentalDate->toDateString(), $itemData['quantity'])) {
                $itemName = Item::find($itemData['id'])->item_name;
                return response()->json(['success' => false, 'message' => "สินค้า '{$itemName}' ไม่ว่างในช่วงเวลาดังกล่าว"], 400);
            }
        }

        // 3. เช็คสต็อก Accessories
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

            // 4. สร้าง Rental Header
            $rental = new Rental();
            $rental->member_id = $request->member_id;
            $rental->user_id = Auth::id();
            $rental->rental_date = $rentalDate;
            $rental->return_date = $returnDate;
            $rental->promotion_id = $request->promotion_id;
            $rental->makeup_id = $request->makeup_id;
            $rental->photographer_id = $request->photographer_id;
            $rental->package_id = $request->package_id;
            $rental->status = 'rented';
            $rental->description = $description;

            // 5. จัดการส่วนลดจากแต้ม (Point Redemption)
            $pointsUsed = $request->points_used ?? 0;

            if ($request->member_id && $pointsUsed > 0) {
                $member = MemberAccount::find($request->member_id);
                // เช็คว่ามีแต้มพอไหม
                if ($member && $member->points >= $pointsUsed) {
                    // ตัดแต้มสมาชิก
                    $member->decrement('points', $pointsUsed);

                    // บันทึก Transaction การใช้แต้ม
                    // หมายเหตุ: rental_id จะถูกอัปเดตทีหลังหลัง save() หรือใส่ null ไว้ก่อน
                    PointTransaction::create([
                        'member_id' => $member->member_id,
                        'rental_id' => null,
                        'point_change' => -$pointsUsed,
                        'change_type' => 'redeem',
                        'description' => 'ใช้แต้มแลกส่วนลดค่าเช่า',
                        'transaction_date' => now(),
                    ]);
                } else {
                    // ถ้าแต้มไม่พอ ให้แจ้ง error หรือข้ามไป (ในที่นี้ข้ามไป ไม่ตัดแต้ม)
                    $pointsUsed = 0;
                }
            }

            // บันทึกยอดเงินสุทธิ (Grand Total ที่หักส่วนลดทุกอย่างแล้วจากหน้าบ้าน)
            $rental->total_amount = $request->total_amount;
            $rental->save(); // ได้ rental_id แล้ว

            // อัปเดต rental_id กลับไปที่ transaction การใช้แต้ม (ถ้ามีการใช้)
            if ($pointsUsed > 0) {
                PointTransaction::where('member_id', $request->member_id)
                    ->where('change_type', 'redeem')
                    ->whereNull('rental_id') // หาอันที่เพิ่งสร้างและยังไม่มี rental_id
                    ->latest()
                    ->first()
                    ?->update(['rental_id' => $rental->rental_id]);
            }

            // 6. บันทึก Rental Items
            foreach ($request->items as $itemData) {
                RentalItem::create([
                    'rental_id' => $rental->rental_id,
                    'item_id' => $itemData['id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                ]);
            }

            // 7. บันทึก Accessories
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

            // 8. บันทึก Payment (มัดจำ)
            if ($request->deposit_amount > 0) {
                Payment::create([
                    'rental_id' => $rental->rental_id,
                    'amount' => $request->deposit_amount,
                    'payment_method' => $request->payment_method,
                    'type' => 'deposit',
                    'status' => 'paid',
                    'payment_date' => now(),
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'บันทึกการเช่าเรียบร้อยแล้ว',
                'rental_id' => $rental->rental_id,
                'staff_name' => Auth::user()->name ?? Auth::user()->first_name ?? 'Admin'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'บันทึกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // 🛠️ ส่วนที่ 2: Logic เช็คสต็อก (แก้ไขให้รองรับ PostgreSQL)
    // =========================================================================

    private function isItemAvailable($itemId, $newStartDate, $requestQty)
    {
        $newStart = Carbon::parse($newStartDate);
        $newEnd   = $newStart->copy()->addDays(9);

        $reservedQty = DB::table('rental_items')
            ->join('rentals', 'rental_items.rental_id', '=', 'rentals.rental_id')
            ->where('rental_items.item_id', $itemId)
            ->whereNotIn('rentals.status', ['returned', 'cancelled'])
            ->where(function ($query) use ($newStart, $newEnd) {
                // 🛠️ แก้ไข: ใช้ Syntax ของ PostgreSQL (+ INTERVAL)
                $query->whereRaw("rentals.rental_date <= ?", [$newEnd])
                    ->whereRaw("(rentals.rental_date + INTERVAL '9 day') >= ?", [$newStart]);
            })
            ->sum('rental_items.quantity');

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
            ->whereNotIn('rentals.status', ['returned', 'cancelled'])
            ->where(function ($query) use ($newStart, $newEnd) {
                // 🛠️ แก้ไข: ใช้ Syntax ของ PostgreSQL
                $query->whereRaw("rentals.rental_date <= ?", [$newEnd])
                    ->whereRaw("(rentals.rental_date + INTERVAL '9 day') >= ?", [$newStart]);
            })
            ->sum('rental_accessories.quantity');

        $totalStock = Accessory::where('id', $accId)->value('stock');
        return ($totalStock - $reservedQty) >= $requestQty;
    }

    private function calculateAvailableQty($itemId, $rentalDate)
    {
        // คำนวณช่วงเวลา Block 10 วัน (7 เช่า + 3 ดูแล)
        $newStart = Carbon::parse($rentalDate);
        $newEnd   = $newStart->copy()->addDays(9);

        // นับจำนวนที่ติดจองในช่วงเวลานั้น
        $reservedQty = DB::table('rental_items')
            ->join('rentals', 'rental_items.rental_id', '=', 'rentals.rental_id')
            ->where('rental_items.item_id', $itemId)
            ->whereNotIn('rentals.status', ['returned', 'cancelled'])
            ->where(function ($query) use ($newStart, $newEnd) {
                // เช็คช่วงเวลาทับซ้อน (Overlap)
                // ใช้ Syntax Postgres (Supabase)
                $query->whereRaw("rentals.rental_date <= ?", [$newEnd])
                    ->whereRaw("(rentals.rental_date + INTERVAL '9 day') >= ?", [$newStart]);
            })
            ->sum('rental_items.quantity');

        // ดึงสต็อกทั้งหมดที่มี
        $totalStock = Item::where('id', $itemId)->value('stock');

        // คืนค่าสต็อกที่ว่าง (ถ้าติดลบให้ตอบ 0)
        return max(0, $totalStock - $reservedQty);
    }
    public function searchItems(Request $request)
    {
        $query = $request->get('q');
        $rentalDate = $request->get('rental_date', now()->toDateString());

        // 1. ดึงสินค้าที่ตรงกับคำค้นหา
        $items = Item::where('stock', '>', 0)
            ->where('status', 'active')
            ->where(function ($sq) use ($query) {
                $sq->where('item_name', 'ILIKE', "%{$query}%")
                    ->orWhereRaw("CAST(id AS TEXT) ILIKE ?", ["%{$query}%"]);
            })
            ->limit(20)
            ->get();

        // 2. [จุดสำคัญ] วนลูปคำนวณสต็อกว่าง แล้วแปะค่าใส่ตัวแปร available_stock
        $items = $items->map(function ($item) use ($rentalDate) {
            $item->available_stock = $this->calculateAvailableQty($item->id, $rentalDate);
            return $item;
        });

        // 3. กรองเอาเฉพาะตัวที่ว่าง (Option: หรือจะส่งไปหมดแล้วให้หน้าบ้านโชว์สีแดงก็ได้)
        // ในที่นี้กรองเอาเฉพาะตัวที่มีของว่างอย่างน้อย 1 ชิ้น
        $availableItems = $items->filter(function ($item) {
            return $item->available_stock > 0;
        });

        return response()->json($availableItems->values());
    }

    // =========================================================================
    // ส่วนที่ 3: ระบบปฏิทิน (ปรับปรุงใหม่: แยกสีตามสถานะ)
    // =========================================================================

    public function calendar()
    {
        return view('reception.calendar');
    }

    public function getCalendarEvents()
    {
        // 1. ดึงข้อมูลการเช่าทั้งหมด (สถานะต้องไม่ใช่ยกเลิก)
        $rentals = Rental::with(['member', 'items.item'])
            ->where('status', '!=', 'cancelled')
            ->get();

        $events = [];
        $today = Carbon::now()->startOfDay();

        foreach ($rentals as $rental) {
            // 2. จัดการชื่อลูกค้า (ถ้าไม่มี Member ให้ใช้ Guest Description)
            $customerName = $rental->member
                ? ($rental->member->first_name . ' ' . $rental->member->last_name)
                : ($rental->description ?? 'Guest');

            // 3. จัดการชื่อชุด (แสดงแค่ชุดแรก + จำนวนที่เหลือ)
            $itemText = 'No Item';
            if ($rental->items->isNotEmpty() && $rental->items->first()->item) {
                $itemText = $rental->items->first()->item->item_name;
            }
            if ($rental->items->count() > 1) {
                $itemText .= " +" . ($rental->items->count() - 1);
            }

            // ข้ามถ้าระบุวันที่ไม่ครบ
            if (!$rental->rental_date || !$rental->return_date) continue;

            $rentalStart = Carbon::parse($rental->rental_date);
            $returnDate  = Carbon::parse($rental->return_date);

            // FullCalendar ใช้ end date แบบ exclusive (ต้องบวกเพิ่ม 1 วันเพื่อให้คลุมถึงวันคืน)
            $rentalEnd   = $returnDate->copy()->addDay();

            // -------------------------------------------------------
            // 🎨 กำหนดสีของ Event ตามสถานะ
            // -------------------------------------------------------
            $color = '#4285F4'; // 🔵 Blue (สถานะปกติ: กำลังเช่า)

            if ($rental->status === 'returned') {
                $color = '#9CA3AF'; // ⚪ Gray (คืนแล้ว)
            } elseif ($returnDate->lt($today)) {
                $color = '#EF4444'; // 🔴 Red (เกินกำหนดคืน)
            }

            // ชื่อ Event ที่จะแสดงในปฏิทิน
            $title = "#{$rental->rental_id} {$customerName} ({$itemText})";

            // -------------------------------------------------------
            // 1️⃣ สร้าง Event หลัก (ช่วงเวลาเช่า)
            // -------------------------------------------------------
            $events[] = [
                'title' => $title,
                'start' => $rentalStart->toDateString(),
                'end'   => $rentalEnd->toDateString(),
                'color' => $color,
                'textColor' => '#FFFFFF', // ตัวหนังสือสีขาว
                'allDay' => true,
                'url'   => route('reception.history', ['search' => $rental->rental_id]),
                'extendedProps' => [
                    'type' => 'rental',
                    'tel' => $rental->member ? $rental->member->tel : ($rental->guest_phone ?? '-') // ✅ เพิ่มบรรทัดนี้
                ]
            ];

            // -------------------------------------------------------
            // 2️⃣ สร้าง Event รอง (ช่วงดูแลชุด/ซักรีด) - ต่อท้ายวันคืน
            // -------------------------------------------------------
            // เริ่มต้นหลังจากวันคืน 1 วัน (ต่อเนื่องกัน)
            $maintStart = $returnDate->copy()->addDay();
            // ระยะเวลาดูแล 3 วัน
            $maintEnd   = $maintStart->copy()->addDays(3);

            $events[] = [
                // ระบุชื่อชุดใน Title เพื่อให้รู้ว่ากำลังดูแลชุดไหน
                'title' => "🔧 ดูแล: #{$rental->rental_id} ({$itemText})",
                'start' => $maintStart->toDateString(),
                'end'   => $maintEnd->toDateString(),
                'color' => '#FEF3C7', // 🟡 สีพื้นหลังเหลืองอ่อน (Amber-100)
                'textColor' => '#92400e', // สีตัวหนังสือเข้ม (Amber-800) ให้อ่านง่าย
                'allDay' => true,
                'extendedProps' => ['type' => 'maintenance'] // ระบุประเภทว่าเป็น 'maintenance'
            ];
        }

        return response()->json($events);
    }
    // =========================================================================
    // 🛠️ ส่วนที่ 4: แก้ไขหน้าประวัติ (History) ให้รองรับการค้นหา ID แบบ Postgres
    // =========================================================================

    public function history(Request $request)
    {
        $query = Rental::with(['member', 'user', 'payments', 'items.item', 'accessories']);

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // 🛠️ แก้ไข: ใช้ CAST(...) ILIKE สำหรับ ID
                $q->whereRaw("CAST(rental_id AS TEXT) ILIKE ?", ["%{$search}%"])
                    ->orWhereHas('member', function ($m) use ($search) {
                        $m->where('first_name', 'ILIKE', "%{$search}%")
                            ->orWhere('tel', 'ILIKE', "%{$search}%");
                    });
            });
        }

        $rentals = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('reception.history', compact('rentals'));
    }

    // ... (ส่วนอื่นๆ เช่น checkMember, returnIndex, processReturn, serviceHistory, paymentHistory เหมือนเดิม) ...
    // แนะนำให้เปลี่ยน LIKE เป็น ILIKE ในฟังก์ชันอื่นๆ ด้วยถ้าต้องการค้นหาแบบไม่สนตัวพิมพ์

    public function returnIndex(Request $request)
    {
        $query = Rental::with(['member', 'items.item'])->where('status', 'rented');
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CAST(rental_id AS TEXT) ILIKE ?", ["%{$search}%"])
                    ->orWhereHas('member', function ($m) use ($search) {
                        $m->where('first_name', 'ILIKE', "%{$search}%")
                            ->orWhere('tel', 'ILIKE', "%{$search}%");
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
            $rental = Rental::with(['items', 'payments'])->findOrFail($rentalId);
            if ($rental->status !== 'rented') {
                return response()->json(['success' => false, 'message' => 'รายการนี้สถานะไม่ถูกต้อง หรือถูกคืนไปแล้ว'], 400);
            }
            $itemsDamage = $request->input('items_damage', []);
            $overdueFine = $request->input('overdue_fine', 0);
            $paymentMethod = $request->input('payment_method', 'cash');
            $totalRentalPrice = $rental->total_amount;
            $totalPaid = $rental->payments->where('status', 'paid')->sum('amount');
            $remainingAmount = max(0, $totalRentalPrice - $totalPaid);
            $totalDamageFine = 0;
            foreach ($itemsDamage as $damage) {
                $rentalItem = RentalItem::where('rental_id', $rental->rental_id)
                    ->where('item_id', $damage['item_id'])
                    ->first();
                if ($rentalItem) {
                    $newNote = "[เสีย {$damage['qty']} ชิ้น: {$damage['note']} (ปรับ " . number_format($damage['fine']) . ")]";
                    $rentalItem->description = trim($rentalItem->description . ' ' . $newNote);
                    $rentalItem->fine_amount += $damage['fine'];
                    $rentalItem->save();
                    $totalDamageFine += $damage['fine'];
                }
            }
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
            $rental->status = 'returned';
            $rental->fine_amount = $overdueFine + $totalDamageFine;
            $rental->save();
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

    public function checkMember(Request $request)
    {
        $query = $request->get('q');
        $member = MemberAccount::where('member_id', $query)
            ->orWhere('username', $query)
            ->orWhere('tel', $query)
            ->first();
        return $member ? response()->json(['success' => true, 'member' => $member]) : response()->json(['success' => false]);
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
}
