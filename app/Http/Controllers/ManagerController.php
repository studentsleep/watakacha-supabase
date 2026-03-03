<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Models
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\ItemType;
use App\Models\ItemImage;
use App\Models\User;
use App\Models\UserType;
use App\Models\MemberAccount;
use App\Models\PointTransaction;
use App\Models\CareShop;
use App\Models\MakeupArtist;
use App\Models\Photographer;
use App\Models\PhotographerPackage;
use App\Models\Promotion;
use App\Models\Accessory;
use App\Models\Payment;
use App\Models\ItemMaintenance;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\RentalAccessory;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ManagerController extends Controller
{
    // =========================================================================
    // 📊 Dashboard
    // =========================================================================
    public function dashboard(Request $request)
    {
        // 1. เช็ค Role
        if (Auth::user()->user_type_id == 2) {
            return redirect()->route('reception.rental');
        }

        Carbon::setLocale('th');
        $today = Carbon::today();
        $filter = $request->get('filter', 'week');

        // กำหนดช่วงเวลา (StartDate - EndDate)
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            if ($filter == 'year') {
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
            } elseif ($filter == 'month') {
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
            } elseif ($filter == 'today') { // ✅ เพิ่มส่วนนี้ครับ
                $startDate = Carbon::today(); // 00:00:00
                $endDate = Carbon::today()->endOfDay(); // 23:59:59
            } else {
                $startDate = Carbon::now()->subDays(7)->startOfDay(); // Default 7 วัน
                $endDate = Carbon::now()->endOfDay();
            }
        }

        // ==================================================================================
        // 📊 ส่วนที่ 1: การเงิน (Financial Stats)
        // ==================================================================================

        // ดึงรายการเช่าในช่วงเวลานี้ (ไม่เอายกเลิก)
        $rentals = Rental::with(['items', 'accessories', 'promotion', 'makeupArtist', 'photographerPackage'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        // ตัวแปรสำหรับ Pie Chart (Revenue Breakdown)
        $revItemsNet = 0; // รายได้ชุด (หลังหักส่วนลด)
        $revAccessories = 0; // รายได้อุปกรณ์ (ไม่หักส่วนลด)
        $revServices = 0; // รายได้บริการ (ราคาหน้าร้าน)

        // ตัวแปรสำหรับ Service Profit Chart
        $costServices = 0; // ต้นทุนช่าง (จ่ายจริง)

        foreach ($rentals as $rental) {
            // 1. คำนวณรายได้สินค้า (Gross)
            $itemGross = $rental->items->sum(function ($i) {
                return $i->price * $i->quantity;
            });

            // 2. คำนวณส่วนลด (หักเฉพาะค่าชุด)
            $discount = 0;
            if ($rental->promotion) {
                if ($rental->promotion->discount_type == 'percentage') {
                    $discount = ($itemGross * $rental->promotion->discount_value) / 100;
                } else {
                    $discount = $rental->promotion->discount_value;
                }
            }
            // ป้องกันส่วนลดเกินราคาของ
            $discount = min($discount, $itemGross);

            // บวกเข้ายอดรวม (Net Item Income)
            $revItemsNet += ($itemGross - $discount);

            // 3. คำนวณรายได้อุปกรณ์เสริม
            $accIncome = $rental->accessories->sum(function ($a) {
                return $a->pivot->price * $a->pivot->quantity;
            });
            $revAccessories += $accIncome;

            // 4. คำนวณรายได้บริการ (ราคาหน้าร้าน)
            $makeupPrice = $rental->makeupArtist ? $rental->makeupArtist->price : 0;
            $photoPrice = $rental->photographerPackage ? $rental->photographerPackage->price : 0;
            $revServices += ($makeupPrice + $photoPrice);

            // 5. คำนวณรายจ่ายค่าช่าง (ต้นทุนจริง)
            //$costServices += ($rental->makeup_cost ?? 0) + ($rental->photographer_cost ?? 0);
        }

        // รายจ่ายค่าซ่อมบำรุง (Maintenance Cost) ในช่วงเวลานี้
        $costMaintenance = ItemMaintenance::whereBetween('received_at', [$startDate, $endDate])->sum('actual_cost');

        // $costServices = Rental::whereBetween('updated_at', [$startDate, $endDate])
        //     ->where('service_cost_status', 'paid')
        //     ->sum(DB::raw('COALESCE(makeup_cost, 0) + COALESCE(photographer_cost, 0)'));

        $costServicesQuery = Rental::where('service_cost_status', 'paid');

        if ($filter == 'today' && !$request->has('start_date')) {
            // ถ้าเลือกวันนี้ ให้ใช้ whereDate เฉพาะวันนี้เลย (แม่นยำ 100%)
            $costServicesQuery->whereDate('updated_at', Carbon::today());
        } else {
            // ถ้าเป็น 7 วัน, เดือน, ปี หรือเลือกวันที่เอง ให้ใช้ whereBetween
            $costServicesQuery->whereBetween('updated_at', [$startDate, $endDate]);
        }

        $costServices = $costServicesQuery->sum(DB::raw('COALESCE(makeup_cost, 0) + COALESCE(photographer_cost, 0)'));
        // รวมยอดเพื่อแสดง Top Cards (แบบสรุปช่วงเวลา)
        $totalRevenuePeriod = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('amount');
        $totalExpensePeriod = $costServices + $costMaintenance;
        $totalProfitPeriod = $totalRevenuePeriod - $totalExpensePeriod;

        // ==================================================================================
        // 🏆 ส่วนที่ 2: สถิติยอดนิยม (Top Stats)
        // ==================================================================================

        // 1. สินค้ายอดนิยม (Top 5 Items)
        $topItems = RentalItem::select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('rental', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', '!=', 'cancelled');
            })
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('item') // Eager load
            ->get();

        // 2. อุปกรณ์เสริมยอดนิยม (Top 5 Accessories)
        $topAccessories = RentalAccessory::select('accessory_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('rental', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', '!=', 'cancelled');
            })
            ->groupBy('accessory_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('accessory')
            ->get();

        // 3. รายงานโปรโมชั่น (Promotion Usage)
        $promotionStats = Rental::select('promotion_id', DB::raw('count(*) as usage_count'))
            ->whereNotNull('promotion_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->groupBy('promotion_id')
            ->with('promotion')
            ->get();

        // ==================================================================================
        // 📈 ส่วนที่ 3: เตรียมข้อมูลกราฟ (Chart Data Calculation) - แก้ Error Undefined Variable
        // ==================================================================================
        $chartLabels = [];
        $incomeData = [];
        $expenseData = [];

        // กำหนด Loop ช่วงเวลาสำหรับกราฟ
        if ($filter == 'year') {
            // รายปี (12 เดือน)
            for ($i = 1; $i <= 12; $i++) {
                $loopDate = Carbon::create($today->year, $i, 1);
                $chartLabels[] = $loopDate->isoFormat('MMM'); // ม.ค., ก.พ.

                $incomeData[] = Payment::whereYear('payment_date', $today->year)->whereMonth('payment_date', $i)->sum('amount');

                $mtCost = ItemMaintenance::whereYear('received_at', $today->year)->whereMonth('received_at', $i)->sum('actual_cost');
                $svCost = Rental::whereYear('created_at', $today->year)
                    ->whereMonth('updated_at', $i)
                    ->where('service_cost_status', 'paid')
                    ->sum(DB::raw('COALESCE(makeup_cost, 0) + COALESCE(photographer_cost, 0)'));
                $expenseData[] = $mtCost + $svCost;
            }
        } elseif ($filter == 'month') {
            // รายเดือน (ทุกวันในเดือน)
            $daysInMonth = $today->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $loopDate = Carbon::create($today->year, $today->month, $i);
                $chartLabels[] = $loopDate->format('d'); // วันที่ 1, 2, 3...

                $incomeData[] = Payment::whereDate('payment_date', $loopDate)->sum('amount');

                $mtCost = ItemMaintenance::whereDate('received_at', $loopDate)->sum('actual_cost');
                $svCost = Rental::whereDate('updated_at', $loopDate)
                    ->where('service_cost_status', 'paid')
                    ->sum(DB::raw('COALESCE(makeup_cost, 0) + COALESCE(photographer_cost, 0)'));
                $expenseData[] = $mtCost + $svCost;
            }
        } else {
            // Default: 7 วันย้อนหลัง หรือ ตามช่วงวันที่เลือก (ถ้าห่างกันไม่มาก)
            // ถ้าเลือกช่วงวันเอง ให้ Loop ตามจริง
            $loopStart = $startDate->copy();
            $loopEnd = $endDate->copy();

            // ป้องกัน Loop เยอะเกินไป (Max 30 วัน)
            if ($loopStart->diffInDays($loopEnd) > 31) {
                $loopStart = $loopEnd->copy()->subDays(30);
            }

            while ($loopStart->lte($loopEnd)) {
                $chartLabels[] = $loopStart->isoFormat('D MMM'); // 1 ม.ค.

                $incomeData[] = Payment::whereDate('payment_date', $loopStart)->sum('amount');

                $mtCost = ItemMaintenance::whereDate('received_at', $loopStart)->sum('actual_cost');
                $svCost = Rental::whereDate('updated_at', $loopStart)
                    ->where('service_cost_status', 'paid')
                    ->sum(DB::raw('COALESCE(makeup_cost, 0) + COALESCE(photographer_cost, 0)'));
                $expenseData[] = $mtCost + $svCost;

                $loopStart->addDay();
            }
        }
        // ==================================================================================
        // 📦 ส่วนที่ 4: ข้อมูลพื้นฐานเดิม (สำหรับแสดงผลส่วนอื่นๆ)
        // ==================================================================================

        // ยอดวันนี้ (รายวัน) - คงไว้เหมือนเดิมเพื่อให้เห็น Realtime
        $todayRevenue = Payment::whereDate('payment_date', $today)->sum('amount');
        $todayExpense = ItemMaintenance::whereDate('received_at', $today)->sum('actual_cost')
            + Rental::whereDate('updated_at', $today)
            ->where('service_cost_status', 'paid')
            ->sum(DB::raw('COALESCE(makeup_cost, 0) + COALESCE(photographer_cost, 0)'));

        // รายการชำรุดล่าสุด
        $damagedItemsList = ItemMaintenance::with(['item', 'accessory'])
            ->whereNotNull('damage_description')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $damagedItemsCount = Item::whereIn('status', ['maintenance', 'damaged'])->count();

        // Status Chart Data
        $rawStatus = Item::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')->toArray();

        $itemStatus = [
            'ว่าง (Ready)' => $rawStatus['active'] ?? 0,
            'ถูกเช่า (Rented)' => $rawStatus['rented'] ?? 0,
            'ซ่อม/ซัก (Maintenance)' => ($rawStatus['maintenance'] ?? 0) + ($rawStatus['damaged'] ?? 0),
        ];


        return view('dashboard', compact(
            // Filter Data
            'filter',
            'startDate',
            'endDate',
            // Period Stats
            'revItemsNet',
            'revAccessories',
            'revServices',
            'costServices',
            'costMaintenance',
            'totalRevenuePeriod',
            'totalExpensePeriod',
            'totalProfitPeriod',
            // Lists
            'topItems',
            'topAccessories',
            'promotionStats',
            // Daily & Basic Data
            'todayRevenue',
            'todayExpense',
            'damagedItemsList',
            'damagedItemsCount',
            'chartLabels',
            'incomeData',
            'expenseData',
            'itemStatus'
        ));
    }

    // =========================================================================
    // 🟢 กลุ่ม 1: ระบบสมาชิก & พนักงาน (Index Methods)
    // =========================================================================

    public function usersIndex(Request $request)
    {
        $search = $request->input('search');
        $typeId = $request->input('type_id');

        $query = User::with('userType');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($typeId) $query->where('user_type_id', $typeId);

        $users = $query->orderBy('user_id', 'desc')->paginate(20)->withQueryString();
        $user_types = UserType::orderBy('name')->get();

        return view('manager.users.index', compact('users', 'user_types'));
    }

    public function membersIndex(Request $request)
    {
        $search = $request->input('search');
        $query = MemberAccount::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('tel', 'like', "%{$search}%");
            });
        }
        $members = $query->orderBy('member_id', 'desc')->paginate(20)->withQueryString();
        return view('manager.members.index', compact('members'));
    }

    public function userTypesIndex(Request $request)
    {
        $search = $request->input('search');
        $query = UserType::query();
        if ($search) $query->where('name', 'like', "%{$search}%");
        $user_types = $query->orderBy('user_type_id', 'desc')->paginate(20)->withQueryString();
        return view('manager.user_types.index', compact('user_types'));
    }

    // =========================================================================
    // 🟣 กลุ่ม 2: คลังสินค้า (Index Methods)
    // =========================================================================

    public function itemsIndex(Request $request)
    {
        $search = $request->input('search');
        $query = Item::with(['type', 'unit', 'images']);
        if ($search) $query->where('item_name', 'like', "%{$search}%");

        $items = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $units = ItemUnit::orderBy('name')->get();
        $types = ItemType::orderBy('name')->get();

        return view('manager.items.index', compact('items', 'units', 'types'));
    }

    public function accessoriesIndex(Request $request)
    {
        $search = $request->input('search');
        $query = Accessory::with(['type', 'unit']);
        if ($search) $query->where('name', 'like', "%{$search}%");

        $accessories = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $units = ItemUnit::orderBy('name')->get();
        $types = ItemType::orderBy('name')->get();

        return view('manager.accessories.index', compact('accessories', 'units', 'types'));
    }

    public function itemTypesIndex(Request $request)
    {
        $search = $request->input('search');
        $query = ItemType::query();
        if ($search) $query->where('name', 'like', "%{$search}%");
        $types = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        return view('manager.item_types.index', compact('types'));
    }

    public function unitsIndex(Request $request)
    {
        $search = $request->input('search');
        $query = ItemUnit::query();
        if ($search) $query->where('name', 'like', "%{$search}%");
        $units = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        return view('manager.item_units.index', compact('units'));
    }

    // =========================================================================
    // 🩷 กลุ่ม 3: พาร์ทเนอร์ & บริการ (Index Methods)
    // =========================================================================

    public function careShopsIndex(Request $request)
    {
        $search = $request->input('search');
        $query = CareShop::query();
        if ($search) $query->where('care_name', 'like', "%{$search}%");
        $care_shops = $query->orderBy('care_shop_id', 'desc')->paginate(20)->withQueryString();
        return view('manager.care_shops.index', compact('care_shops'));
    }

    public function makeupArtistsIndex(Request $request)
    {
        $search = $request->input('search');
        $query = MakeupArtist::query();
        if ($search) $query->where('first_name', 'like', "%{$search}%");
        $makeup_artists = $query->orderBy('makeup_id', 'desc')->paginate(20)->withQueryString();
        return view('manager.makeup_artists.index', compact('makeup_artists'));
    }

    public function photographersIndex(Request $request)
    {
        $search = $request->input('search');
        $query = Photographer::query();
        if ($search) $query->where('first_name', 'like', "%{$search}%");
        $photographers = $query->orderBy('photographer_id', 'desc')->paginate(20)->withQueryString();
        return view('manager.photographers.index', compact('photographers'));
    }

    public function photographerPackagesIndex(Request $request)
    {
        $search = $request->input('search');
        $query = PhotographerPackage::query();
        if ($search) $query->where('package_name', 'like', "%{$search}%");
        $photographer_packages = $query->orderBy('package_id', 'desc')->paginate(20)->withQueryString();
        return view('manager.photographer_packages.index', compact('photographer_packages'));
    }

    // =========================================================================
    // 🟡 กลุ่ม 4: การตลาด (Index Methods)
    // =========================================================================

    public function promotionsIndex(Request $request)
    {
        $search = $request->input('search');
        $query = Promotion::query();
        if ($search) $query->where('promotion_name', 'like', "%{$search}%");
        $promotions = $query->orderBy('promotion_id', 'desc')->paginate(20)->withQueryString();
        return view('manager.promotions.index', compact('promotions'));
    }

    // =========================================================================
    // 🛠️ CRUD Functions (Store / Update / Destroy)
    // =========================================================================

    // --- Users ---
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('user_accounts')],
            'email' => ['required', 'email', 'max:255', Rule::unique('user_accounts')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'user_type_id' => 'required|exists:user_types,user_type_id',
            'status' => 'required|string',
            'password' => 'required|string|min:8',
        ]);
        User::create($data);
        return redirect()->route('manager.users.index')->with('status', 'สร้างผู้ใช้สำเร็จ');
    }
    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('user_accounts')->ignore($user->user_id, 'user_id')],
            'email' => ['required', 'email', 'max:255', Rule::unique('user_accounts')->ignore($user->user_id, 'user_id')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'user_type_id' => 'required|exists:user_types,user_type_id',
            'status' => 'required|string',
            'password' => 'nullable|string|min:8',
        ]);
        $updateData = $request->except(['password']);
        if (!empty($data['password'])) $updateData['password'] = $data['password'];
        $user->update($updateData);
        return redirect()->route('manager.users.index')->with('status', 'อัปเดตผู้ใช้สำเร็จ');
    }
    public function destroyUser(User $user)
    {
        if ($user->user_id === Auth::id()) return back()->with('error', 'ไม่สามารถลบบัญชีตัวเองได้');
        $user->delete();
        return redirect()->route('manager.users.index')->with('status', 'ลบผู้ใช้สำเร็จ');
    }

    // --- Members ---
    public function storeMember(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('member_accounts')],
            'email' => ['required', 'email', 'max:255', Rule::unique('member_accounts')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'status' => 'required|string',
            'points' => 'required|integer|min:0',
            'password' => 'required|string|min:8|confirmed',
        ]);
        MemberAccount::create($data);
        return redirect()->route('manager.members.index')->with('status', 'เพิ่มสมาชิกสำเร็จ');
    }
    public function updateMember(Request $request, MemberAccount $member)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('member_accounts')->ignore($member->member_id, 'member_id')],
            'email' => ['required', 'email', 'max:255', Rule::unique('member_accounts')->ignore($member->member_id, 'member_id')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'status' => 'required|string',
            'points' => 'required|integer|min:0',
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        $updateData = $request->except(['password', 'password_confirmation', 'current_password']);
        if (!empty($data['password'])) {
            // Check Current Password Logic (Optional)
            $updateData['password'] = $data['password'];
        }
        $member->update($updateData);
        return redirect()->route('manager.members.index')->with('status', 'อัปเดตสมาชิกสำเร็จ');
    }
    public function destroyMember(MemberAccount $member)
    {
        $member->delete();
        return redirect()->route('manager.members.index')->with('status', 'ลบสมาชิกสำเร็จ');
    }

    // --- Items ---
    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'item_type_id' => 'required',
            'item_unit_id' => 'required',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);
        $item = Item::create([
            'item_name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'item_type_id' => $data['item_type_id'],
            'item_unit_id' => $data['item_unit_id'],
            'status' => 'active',
        ]);
        if ($request->hasFile('images')) {
            $hasExisting = $item->images()->exists();
            foreach ($request->file('images') as $idx => $image) {
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => [
                        'secure' => true
                    ]
                ]);

                // สั่ง Upload ผ่าน API โดยตรง
                $uploadApi = new UploadApi();
                $result = $uploadApi->upload($image->getRealPath(), [
                    'folder' => 'items' // ชื่อโฟลเดอร์ใน Cloudinary
                ]);

                // ดึง URL กลับมา
                $url = $result['secure_url'];

                // บันทึกลง Database
                $item->images()->create([
                    'path' => $url,
                    'is_main' => (!$hasExisting && $idx === 0)
                ]);
            }
        }
        return redirect()->route('manager.items.index')->with('status', 'เพิ่มสินค้าสำเร็จ');
    }
    public function updateItem(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'item_type_id' => 'required',
            'item_unit_id' => 'required',
            'status' => 'required|string',
        ]);
        $item->update([
            'item_name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'item_type_id' => $data['item_type_id'],
            'item_unit_id' => $data['item_unit_id'],
            'status' => $data['status'],
        ]);
        return redirect()->route('manager.items.index')->with('status', 'อัปเดตสินค้าสำเร็จ');
    }
    public function destroyItem(Item $item)
    {
        // 1. วนลูปรุปภาพทั้งหมดของสินค้านี้
        foreach ($item->images as $image) {
            // แกะ Public ID จาก URL
            $publicId = $this->getPublicIdFromUrl($image->path);
            // ถ้าแกะได้ ให้สั่งลบบน Cloudinary
            if ($publicId) {
                Cloudinary::destroy($publicId);
            }
        }
        // 2. ลบข้อมูลใน Database (ใช้ Transaction เพื่อความชัวร์)
        DB::transaction(function () use ($item) {
            $item->images()->delete(); // ลบ record รูป
            $item->delete();           // ลบสินค้า
        });
        return redirect()->route('manager.items.index')->with('status', 'ลบสินค้าและรูปภาพสำเร็จ');
    }
    public function uploadItemImage(Request $request, Item $item)
    {
        $request->validate(['images' => 'required', 'images.*' => 'image|max:2048']);

        if ($request->hasFile('images')) {
            $hasExisting = $item->images()->exists();

            foreach ($request->file('images') as $idx => $image) {

                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => [
                        'secure' => true
                    ]
                ]);

                // สั่ง Upload ผ่าน API โดยตรง
                $uploadApi = new UploadApi();
                $result = $uploadApi->upload($image->getRealPath(), [
                    'folder' => 'items' // ชื่อโฟลเดอร์ใน Cloudinary
                ]);

                // ดึง URL กลับมา
                $url = $result['secure_url'];

                // บันทึกลง Database
                $item->images()->create([
                    'path' => $url,
                    'is_main' => (!$hasExisting && $idx === 0)
                ]);
            }
        }
        return back()->with('status', 'อัปโหลดรูปภาพสำเร็จ');
    }
    public function bulkDestroyImages(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'กรุณาเลือกรูปภาพที่ต้องการลบ');
        }

        $images = ItemImage::whereIn('id', $ids)->get();

        // ตั้งค่า Cloudinary Manual (กัน Error key "cloud")
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => ['secure' => true]
        ]);
        $uploadApi = new UploadApi();

        foreach ($images as $image) {
            // 1. ลบไฟล์บน Cloudinary
            $publicId = $this->getPublicIdFromUrl($image->path);
            if ($publicId) {
                $uploadApi->destroy($publicId);
            }

            $item = $image->item;
            $wasMain = $image->is_main;

            // 2. ลบใน DB
            $image->delete();

            // 3. ถ้าลบรูปหลัก ให้ตั้งรูปใหม่แทน
            if ($wasMain && $item->images()->count() > 0) {
                $newMain = $item->images()->first();
                $newMain->update(['is_main' => true]);
            }
        }

        return back()->with('status', 'ลบรูปภาพที่เลือกเรียบร้อยแล้ว');
    }
    public function destroyItemImage(ItemImage $image)
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => ['secure' => true]
        ]);

        $publicId = $this->getPublicIdFromUrl($image->path);
        if ($publicId) {
            (new UploadApi())->destroy($publicId);
        }

        $item = $image->item;
        $wasMain = $image->is_main;
        $image->delete();

        if ($wasMain && $item->images()->count() > 0) {
            $item->images()->first()->update(['is_main' => true]);
        }

        return back()->with('status', 'ลบรูปภาพสำเร็จ');
    }
    public function setMainImage(ItemImage $image)
    {
        $image->item->images()->update(['is_main' => false]);
        $image->is_main = true;
        $image->save();
        return back()->with('status', 'ตั้งค่ารูปหลักสำเร็จ');
    }

    // --- Accessories ---
    public function storeAccessory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'item_type_id' => 'required|exists:item_types,id',
            'item_unit_id' => 'required|exists:item_units,id',
        ]);
        Accessory::create($data);
        return redirect()->route('manager.accessories.index')->with('status', 'เพิ่มอุปกรณ์เสริมสำเร็จ');
    }
    public function updateAccessory(Request $request, Accessory $accessory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'item_type_id' => 'required|exists:item_types,id',
            'item_unit_id' => 'required|exists:item_units,id',
        ]);
        $accessory->update($data);
        return redirect()->route('manager.accessories.index')->with('status', 'อัปเดตอุปกรณ์เสริมสำเร็จ');
    }
    public function destroyAccessory(Accessory $accessory)
    {
        $accessory->delete();
        return redirect()->route('manager.accessories.index')->with('status', 'ลบอุปกรณ์เสริมสำเร็จ');
    }

    // --- Types & Units ---
    public function storeType(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']);
        ItemType::create($request->all());
        return redirect()->route('manager.item_types.index')->with('status', 'เพิ่มประเภทสินค้าสำเร็จ');
    }
    public function updateType(Request $request, ItemType $type)
    {
        $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']);
        $type->update($request->all());
        return redirect()->route('manager.item_types.index')->with('status', 'อัปเดตประเภทสินค้าสำเร็จ');
    }
    public function destroyType(ItemType $type)
    {
        $type->delete();
        return redirect()->route('manager.item_types.index')->with('status', 'ลบประเภทสินค้าสำเร็จ');
    }

    public function storeUnit(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']);
        ItemUnit::create($request->all());
        return redirect()->route('manager.units.index')->with('status', 'เพิ่มหน่วยนับสำเร็จ');
    }
    public function updateUnit(Request $request, ItemUnit $unit)
    {
        $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']);
        $unit->update($request->all());
        return redirect()->route('manager.units.index')->with('status', 'อัปเดตหน่วยนับสำเร็จ');
    }
    public function destroyUnit(ItemUnit $unit)
    {
        $unit->delete();
        return redirect()->route('manager.units.index')->with('status', 'ลบหน่วยนับสำเร็จ');
    }
    public function storeUserType(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50', 'description' => 'nullable|string']);
        UserType::create($request->all());
        return redirect()->route('manager.user_types.index')->with('status', 'เพิ่มประเภทผู้ใช้สำเร็จ');
    }
    public function updateUserType(Request $request, UserType $user_type)
    {
        $request->validate(['name' => 'required|string|max:50', 'description' => 'nullable|string']);
        $user_type->update($request->all());
        return redirect()->route('manager.user_types.index')->with('status', 'อัปเดตประเภทผู้ใช้สำเร็จ');
    }
    public function destroyUserType(UserType $user_type)
    {
        $user_type->delete();
        return redirect()->route('manager.user_types.index')->with('status', 'ลบประเภทผู้ใช้สำเร็จ');
    }

    // --- Care Shops, Artists, Photographers, Packages, Promotions ---
    public function storeCareShop(Request $request)
    {
        $data = $request->validate(['care_name' => 'required|string|max:255', 'address' => 'nullable|string', 'tel' => 'nullable|string|max:20', 'email' => 'nullable|email|max:255', 'status' => 'required|string|max:50']);
        CareShop::create($data);
        return redirect()->route('manager.care_shops.index')->with('status', 'เพิ่มร้านสำเร็จ');
    }
    public function updateCareShop(Request $request, CareShop $care_shop)
    {
        $data = $request->validate(['care_name' => 'required|string|max:255', 'address' => 'nullable|string', 'tel' => 'nullable|string|max:20', 'email' => 'nullable|email|max:255', 'status' => 'required|string|max:50']);
        $care_shop->update($data);
        return redirect()->route('manager.care_shops.index')->with('status', 'อัปเดตร้านสำเร็จ');
    }
    public function destroyCareShop(CareShop $care_shop)
    {
        $care_shop->delete();
        return redirect()->route('manager.care_shops.index')->with('status', 'ลบร้านสำเร็จ');
    }

    public function storeMakeupArtist(Request $request)
    {
        $data = $request->validate(['first_name' => 'required', 'last_name' => 'required', 'tel' => 'nullable', 'email' => 'nullable', 'status' => 'required', 'price' => 'required', 'lineid' => 'nullable|string|max:100', 'description' => 'nullable']);
        MakeupArtist::create($data);
        return redirect()->route('manager.makeup_artists.index')->with('status', 'เพิ่มช่างแต่งหน้าสำเร็จ');
    }
    public function updateMakeupArtist(Request $request, MakeupArtist $makeup_artist)
    {
        $data = $request->validate(['first_name' => 'required', 'last_name' => 'required', 'tel' => 'nullable', 'email' => 'nullable', 'status' => 'required', 'price' => 'required', 'lineid' => 'nullable|string|max:100', 'description' => 'nullable']);
        $makeup_artist->update($data);
        return redirect()->route('manager.makeup_artists.index')->with('status', 'อัปเดตช่างแต่งหน้าสำเร็จ');
    }
    public function destroyMakeupArtist(MakeupArtist $makeup_artist)
    {
        $makeup_artist->delete();
        return redirect()->route('manager.makeup_artists.index')->with('status', 'ลบช่างแต่งหน้าสำเร็จ');
    }

    public function storePhotographer(Request $request)
    {
        $data = $request->validate(['first_name' => 'required', 'last_name' => 'required', 'tel' => 'nullable', 'email' => 'nullable', 'lineid' => 'nullable|string|max:100', 'status' => 'required']);
        Photographer::create($data);
        return redirect()->route('manager.photographers.index')->with('status', 'เพิ่มช่างภาพสำเร็จ');
    }
    public function updatePhotographer(Request $request, Photographer $photographer)
    {
        $data = $request->validate(['first_name' => 'required', 'last_name' => 'required', 'tel' => 'nullable', 'email' => 'nullable', 'lineid' => 'nullable|string|max:100', 'status' => 'required']);
        $photographer->update($data);
        return redirect()->route('manager.photographers.index')->with('status', 'อัปเดตช่างภาพสำเร็จ');
    }
    public function destroyPhotographer(Photographer $photographer)
    {
        $photographer->delete();
        return redirect()->route('manager.photographers.index')->with('status', 'ลบช่างภาพสำเร็จ');
    }

    public function storePhotographerPackage(Request $request)
    {
        $data = $request->validate(['package_name' => 'required', 'price' => 'required', 'description' => 'nullable']);
        PhotographerPackage::create($data);
        return redirect()->route('manager.photographer_packages.index')->with('status', 'เพิ่มแพ็คเกจสำเร็จ');
    }
    public function updatePhotographerPackage(Request $request, PhotographerPackage $photographer_package)
    {
        $data = $request->validate(['package_name' => 'required', 'price' => 'required', 'description' => 'nullable']);
        $photographer_package->update($data);
        return redirect()->route('manager.photographer_packages.index')->with('status', 'อัปเดตแพ็คเกจสำเร็จ');
    }
    public function destroyPhotographerPackage(PhotographerPackage $photographer_package)
    {
        $photographer_package->delete();
        return redirect()->route('manager.photographer_packages.index')->with('status', 'ลบแพ็คเกจสำเร็จ');
    }

    public function storePromotion(Request $request)
    {
        $data = $request->validate(['promotion_name' => 'required', 'discount_type' => 'required', 'discount_value' => 'required', 'description' => 'nullable', 'start_date' => 'nullable', 'end_date' => 'nullable', 'status' => 'required']);
        Promotion::create($data);
        return redirect()->route('manager.promotions.index')->with('status', 'เพิ่มโปรโมชั่นสำเร็จ');
    }
    public function updatePromotion(Request $request, Promotion $promotion)
    {
        $data = $request->validate(['promotion_name' => 'required', 'discount_type' => 'required', 'discount_value' => 'required', 'description' => 'nullable', 'start_date' => 'nullable', 'end_date' => 'nullable', 'status' => 'required']);
        $promotion->update($data);
        return redirect()->route('manager.promotions.index')->with('status', 'อัปเดตโปรโมชั่นสำเร็จ');
    }
    public function destroyPromotion(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('manager.promotions.index')->with('status', 'ลบโปรโมชั่นสำเร็จ');
    }
    // ฟังก์ชันสำหรับแปลง URL ยาวๆ ให้เป็น Public ID เพื่อเอาไปลบ
    private function getPublicIdFromUrl($url)
    {
        // ตัวอย่าง URL: https://res.cloudinary.com/demo/image/upload/v123456789/items/my-image.jpg
        // เราต้องการแค่: items/my-image

        // ใช้ Regex จับค่าที่อยู่หลังคำว่า 'upload/' (และข้าม version 'v123..') จนถึงก่อนนามสกุลไฟล์
        if (preg_match('/upload\/(?:v\d+\/)?(.+)\.[^.]+$/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
