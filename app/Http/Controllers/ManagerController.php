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
        // 1. เช็ค Role (ถ้าเป็นพนักงาน ให้ไปหน้าเช่าชุด)
        if (Auth::user()->user_type_id == 2) {
            return redirect()->route('reception.rental');
        }

        Carbon::setLocale('th');
        $today = Carbon::today();
        $filter = $request->get('filter', 'week');

        // Top Cards Data
        $totalRevenueToday = Payment::whereDate('payment_date', $today)->sum('amount');
        $totalExpenseToday = ItemMaintenance::whereDate('received_at', $today)->sum('shop_cost');
        $rentalsToday = Rental::whereDate('rental_date', $today)->count();
        $damagedItemsCount = Item::whereIn('status', ['maintenance', 'damaged'])->count();

        // Recent Damaged Items
        $damagedItemsList = ItemMaintenance::with('item')
            ->whereNotNull('damage_description')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Chart Data Calculation
        $chartLabels = [];
        $incomeData = [];
        $expenseData = [];

        if ($filter == 'year') {
            for ($i = 1; $i <= 12; $i++) {
                $date = Carbon::create(null, $i, 1);
                $chartLabels[] = $date->isoFormat('MMMM');
                $incomeData[] = Payment::whereYear('payment_date', $today->year)->whereMonth('payment_date', $i)->sum('amount');
                $expenseData[] = ItemMaintenance::whereYear('received_at', $today->year)->whereMonth('received_at', $i)->sum('shop_cost');
            }
        } elseif ($filter == 'month') {
            $daysInMonth = $today->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = Carbon::create($today->year, $today->month, $i);
                $chartLabels[] = $date->isoFormat('D MMM');
                $incomeData[] = Payment::whereDate('payment_date', $date)->sum('amount');
                $expenseData[] = ItemMaintenance::whereDate('received_at', $date)->sum('shop_cost');
            }
        } else {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $chartLabels[] = $date->isoFormat('ddd D');
                $incomeData[] = Payment::whereDate('payment_date', $date)->sum('amount');
                $expenseData[] = ItemMaintenance::whereDate('received_at', $date)->sum('shop_cost');
            }
        }

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
            'totalRevenueToday',
            'totalExpenseToday',
            'rentalsToday',
            'damagedItemsCount',
            'damagedItemsList',
            'chartLabels',
            'incomeData',
            'expenseData',
            'itemStatus',
            'filter'
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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('items', 'public');
                $item->images()->create(['path' => $path, 'is_main' => $index === 0]);
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
        ]);
        $item->update([
            'item_name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'item_type_id' => $data['item_type_id'],
            'item_unit_id' => $data['item_unit_id'],
        ]);
        return redirect()->route('manager.items.index')->with('status', 'อัปเดตสินค้าสำเร็จ');
    }
    public function destroyItem(Item $item)
    {
        foreach ($item->images as $img) Storage::disk('public')->delete($img->path);
        $item->images()->delete();
        $item->delete();
        return redirect()->route('manager.items.index')->with('status', 'ลบสินค้าสำเร็จ');
    }
    public function uploadItemImage(Request $request, Item $item)
    {
        $request->validate(['images' => 'required', 'images.*' => 'image|max:2048']);
        if ($request->hasFile('images')) {
            $hasExisting = $item->images()->exists();
            foreach ($request->file('images') as $idx => $image) {
                $path = $image->store('items', 'public');
                $item->images()->create(['path' => $path, 'is_main' => (!$hasExisting && $idx === 0)]);
            }
        }
        return back()->with('status', 'อัปโหลดรูปภาพสำเร็จ');
    }
    public function destroyItemImage(ItemImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $item = $image->item;
        $wasMain = $image->is_main;
        $image->delete();
        if ($wasMain && $item->images()->count() > 0) {
            $newMain = $item->images()->first();
            $newMain->is_main = true;
            $newMain->save();
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
        $data = $request->validate(['first_name' => 'required', 'last_name' => 'required', 'tel' => 'nullable', 'email' => 'nullable','lineid' => 'nullable|string|max:100', 'status' => 'required']);
        Photographer::create($data);
        return redirect()->route('manager.photographers.index')->with('status', 'เพิ่มช่างภาพสำเร็จ');
    }
    public function updatePhotographer(Request $request, Photographer $photographer)
    {
        $data = $request->validate(['first_name' => 'required', 'last_name' => 'required', 'tel' => 'nullable', 'email' => 'nullable','lineid' => 'nullable|string|max:100', 'status' => 'required']);
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
}
