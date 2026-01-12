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
    // ... (ฟังก์ชัน index และ storeRental เหมือนเดิม ไม่ต้องแก้) ...
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

    public function storeRental(Request $request)
    {
        $request->validate([
            'deposit_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'rental_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $rentalDate = Carbon::parse($request->rental_date);
        $returnDate = $rentalDate->copy()->addDays(6);

        // เช็คสต็อก (ใช้ฟังก์ชันที่แก้ให้รองรับ Postgres แล้ว)
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
            $rental->status = 'rented';
            $rental->total_amount = $request->total_amount;
            $rental->description = $description;
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
        // ดึงข้อมูลการเช่าทั้งหมด (ที่ไม่ยกเลิก)
        $rentals = Rental::with(['member', 'items.item'])
            ->where('status', '!=', 'cancelled')
            ->get();

        $events = [];
        $today = Carbon::now()->startOfDay();

        foreach ($rentals as $rental) {
            // 1. ชื่อลูกค้า
            $customerName = $rental->member
                ? ($rental->member->first_name . ' ' . $rental->member->last_name)
                : ($rental->description ?? 'Guest');

            // 2. ชื่อชุด (เอาแค่ชุดแรกพอ ให้สั้นกระชับ)
            $itemText = 'No Item';
            if ($rental->items->isNotEmpty() && $rental->items->first()->item) {
                $itemText = $rental->items->first()->item->item_name;
            }
            if ($rental->items->count() > 1) {
                $itemText .= " +" . ($rental->items->count() - 1);
            }

            if (!$rental->rental_date || !$rental->return_date) continue;

            $rentalStart = Carbon::parse($rental->rental_date);
            $returnDate  = Carbon::parse($rental->return_date);
            // FullCalendar จบวันต้อง +1 เพื่อให้คลุมถึงสิ้นวันนั้น
            $rentalEnd   = $returnDate->copy()->addDay();

            // -------------------------------------------------------
            // 🎨 ตั้งค่าสีแบบ Google Calendar Style
            // -------------------------------------------------------
            $color = '#4285F4'; // 🔵 Blue (Google) - ปกติ
            $textColor = '#FFFFFF';
            $icon = '';

            if ($rental->status === 'returned') {
                $color = '#9AA0A6'; // ⚪ Gray (Google) - คืนแล้ว
                $icon = '✅';
            } elseif ($returnDate->lt($today)) {
                $color = '#EA4335'; // 🔴 Red (Google) - เกินกำหนด
                $icon = '⚠️';
            }

            // ข้อความที่จะโชว์ในแท่งบาร์ (สั้นๆ ได้ใจความ)
            $title = "{$icon} #{$rental->rental_id} {$customerName} ({$itemText})";

            // Event หลัก (ช่วงเช่า)
            $events[] = [
                'title' => $title,
                'start' => $rentalStart->toDateString(),
                'end'   => $rentalEnd->toDateString(),
                'color' => $color,
                'textColor' => $textColor,
                'allDay' => true, // ✅ บังคับเป็นแถบเต็มวัน
                'url'   => route('reception.history', ['search' => $rental->rental_id]),
                // ใส่ข้อมูลเพิ่มเติมสำหรับ Tooltip (ถ้าจะทำ)
                'extendedProps' => [
                    'status' => $rental->status,
                    'customer' => $customerName,
                    'items' => $itemText
                ]
            ];

            // Event รอง (ช่วงดูแลชุด) - แสดงเป็นพื้นหลัง (Background Event)
            // จะไม่แย่งซีน Event หลัก แต่จะถมสีลงในช่องวัน
            $maintStart = $returnDate->copy()->addDay();
            $maintEnd   = $maintStart->copy()->addDays(3);

            // ถ้าคืนแล้ว ไม่ต้องโชว์ Maintenance ให้รกตา (หรือโชว์จางๆ)
            if ($rental->status !== 'returned') {
                $events[] = [
                    'start' => $maintStart->toDateString(),
                    'end'   => $maintEnd->toDateString(),
                    'display' => 'background', // ✅ แสดงเป็นพื้นหลัง
                    'color' => '#FEF3C7', // 🟡 สีเหลืองอ่อนๆ (Tailwind amber-100)
                    'allDay' => true,
                ];
            }
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
        // ... (Logic เดิม ไม่ต้องแก้) ...
        // เพียงแค่ copy ส่วนเดิมมาวางได้เลยครับ
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
