<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// (Models ทั้งหมด)
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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ManagerController extends Controller
{
    public function index(Request $request)
    {
        $table = $request->input('table', 'users');
        $search = $request->input('search');

        $status = $request->input('status');
        $typeId = $request->input('type_id');

        $data = ['table' => $table];

        if ($table == 'users') {
            $query = User::with('userType');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }
            if ($status) $query->where('status', $status);
            if ($typeId) $query->where('user_type_id', $typeId);

            $data['users'] = $query->orderBy('user_id', 'desc')->paginate(20)->withQueryString();
            $data['user_types'] = UserType::orderBy('name')->get();
        } elseif ($table == 'member_accounts') {
            $query = MemberAccount::query();
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%");
                });
            }
            if ($status) $query->where('status', $status);
            $data['members'] = $query->orderBy('member_id', 'desc')->paginate(20)->withQueryString();
        } elseif (in_array($table, ['care_shops', 'makeup_artists', 'photographers', 'promotions'])) {
            $modelMap = [
                'care_shops' => CareShop::class,
                'makeup_artists' => MakeupArtist::class,
                'photographers' => Photographer::class,
                'promotions' => Promotion::class,
            ];
            $pkMap = [
                'care_shops' => 'care_shop_id',
                'makeup_artists' => 'makeup_id',
                'photographers' => 'photographer_id',
                'promotions' => 'promotion_id',
            ];

            $model = $modelMap[$table];
            $pk = $pkMap[$table];
            $query = $model::query();

            if ($search) {
                if ($table == 'care_shops') $query->where('care_name', 'like', "%{$search}%");
                elseif ($table == 'promotions') $query->where('promotion_name', 'like', "%{$search}%");
                else $query->where('first_name', 'like', "%{$search}%");
            }
            if ($status) $query->where('status', $status);

            $data[$table] = $query->orderBy($pk, 'desc')->paginate(20)->withQueryString();
        } elseif ($table == 'items') {
            $query = Item::with(['type', 'unit', 'images']);
            if ($search) $query->where('item_name', 'like', "%{$search}%");
            $data['items'] = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
            $data['units'] = ItemUnit::orderBy('name')->get();
            $data['types'] = ItemType::orderBy('name')->get();
        } elseif ($table == 'item_units') {
            // [จุดที่แก้ไข] เปลี่ยนเป็น paginate() เพื่อแก้ Error firstItem()
            $query = ItemUnit::query();
            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }
            $data['units'] = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        } elseif ($table == 'item_types') {
            $query = ItemType::query();

            // 1. เพิ่ม Logic ค้นหา
            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }

            // 2. เปลี่ยนเป็น Paginate
            $data['types'] = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        } elseif ($table == 'point_transactions') {
            $query = PointTransaction::with('member');
            if ($search) {
                $query->whereHas('member', function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%");
                })->orWhere('description', 'like', "%{$search}%");
            }
            $data['transactions'] = $query->orderBy('transaction_date', 'desc')->paginate(30)->withQueryString();
        } elseif ($table == 'user_types') {
            $query = UserType::query();

            // เพิ่ม Logic ค้นหา
            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }

            // เปลี่ยนเป็น Paginate (แบ่งหน้า)
            $data['user_types'] = $query->orderBy('user_type_id', 'desc')
                ->paginate(20)
                ->withQueryString();
        } elseif ($table == 'photographer_packages') {
            $query = PhotographerPackage::query();
            if ($search) $query->where('package_name', 'like', "%{$search}%");
            $data['photographer_packages'] = $query->orderBy('package_id', 'desc')->paginate(20)->withQueryString();
        }

        return view('manager.index', $data);
    }

    // --- Items (PK: id) ---
    public function storeItem(Request $request)
    {
        // [นี่คือจุดที่พัง]
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            // [แก้ไข] ต้องตรวจสอบกับ 'id' (PK ของตาราง item_units)
            // 'id' => 'required|exists:item_units,id',
            // [แก้ไข] ต้องตรวจสอบกับ 'id' (PK ของตาราง item_types)
            // 'id' => 'required|exists:item_types,id',
            'item_type_id' => 'required',
            'item_unit_id' => 'required',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $item = Item::create([
            'item_name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            // 'id' => $data['id'],
            // 'id' => $data['id'],
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
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item created successfully.');
    }

    public function updateItem(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            // [แก้ไข] ต้องตรวจสอบกับ 'id'
            // 'id' => 'required|exists:item_units,id',
            // // [แก้ไข] ต้องตรวจสอบกับ 'id'
            // 'id' => 'required|exists:item_types,id',
            'item_type_id' => 'required',
            'item_unit_id' => 'required',
        ]);
        $item->update([
            'item_name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            // 'id' => $data['id'],
            // 'id' => $data['id'],
            'item_type_id' => $data['item_type_id'],
            'item_unit_id' => $data['item_unit_id'],
        ]);
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item updated successfully.');
    }

    public function uploadItemImage(Request $request, Item $item)
    {
        // 1. Validate แบบ Array
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048', // ตรวจสอบทีละไฟล์
        ]);

        // 2. วนลูปบันทึกไฟล์
        if ($request->hasFile('images')) {
            // เช็คก่อนว่าตอนนี้มีรูปอยู่แล้วหรือไม่ (เพื่อกำหนด is_main ของรูปแรกที่เพิ่มเข้าไปใหม่ ถ้ายังไม่มีรูปเลย)
            $hasExistingImages = $item->images()->exists();

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('items', 'public');

                $item->images()->create([
                    'path' => $path,
                    // ถ้ายังไม่มีรูปเลย -> รูปแรกสุดที่อัป (index 0) จะเป็น main
                    // ถ้ามีรูปอยู่แล้ว -> รูปใหม่ทั้งหมดจะไม่ใช่ main
                    'is_main' => (!$hasExistingImages && $index === 0),
                ]);
            }
        }

        return back()->with('status', 'อัปโหลดรูปภาพเรียบร้อยแล้ว');
    }

    /**
     * ลบรูปภาพ (ทีละรูป)
     */
    public function destroyItemImage(ItemImage $image)
    {
        // (จำเป็นต้องใช้ Storage)
        Storage::disk('public')->delete($image->path);

        $item = $image->item; // ดึง Item แม่มาก่อน
        $wasMain = $image->is_main; // ตรวจสอบว่าเป็นรูปหลักหรือไม่

        $image->delete(); // ลบจากฐานข้อมูล

        // [Logic สำคัญ] ถ้าลบรูปหลัก และยังมีรูปอื่นเหลืออยู่ ให้ตั้งรูปแรกสุดเป็นรูปหลักใหม่
        if ($wasMain && $item->images()->count() > 0) {
            $newItemMain = $item->images()->first();
            $newItemMain->is_main = true;
            $newItemMain->save();
        }

        return back()->with('status', 'Image deleted successfully.');
    }

    /**
     * ตั้งค่ารูปภาพหลัก (ทีละรูป)
     */
    public function setMainImage(ItemImage $image)
    {
        $item = $image->item;

        // 1. ล้างค่า is_main ทั้งหมดของ Item นี้
        $item->images()->update(['is_main' => false]);

        // 2. ตั้งค่า is_main = true ให้กับรูปที่เลือก
        $image->is_main = true;
        $image->save();

        return back()->with('status', 'Main image set successfully.');
    }

    /**
     * ลบ Item ทั้งหมด (รวมถึงรูปภาพ)
     */
    public function destroyItem(Item $item)
    {
        // 1. ลบไฟล์รูปภาพทั้งหมดใน Storage
        foreach ($item->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        // 2. ลบข้อมูลรูปภาพในตาราง item_images
        $item->images()->delete();

        // 3. ลบ Item
        $item->delete();

        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item and all associated images deleted successfully.');
    }


    // --- Item Units (PK: id) ---
    public function storeUnit(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:item_units,name', 'description' => 'nullable|string']);
        ItemUnit::create($request->all());
        return redirect()->route('manager.index', ['table' => 'item_units'])->with('status', 'Unit created successfully.');
    }

    public function updateUnit(Request $request, ItemUnit $unit)
    {
        // [แก้ไข] ใช้ 'id'
        $request->validate(['name' => 'required|string|max:255|unique:item_units,name,' . $unit->id . ',id', 'description' => 'nullable|string']);
        $unit->update($request->all());
        return redirect()->route('manager.index', ['table' => 'item_units'])->with('status', 'Unit updated successfully.');
    }

    public function destroyUnit(ItemUnit $unit)
    {
        $unit->delete();
        return redirect()->route('manager.index', ['table' => 'item_units'])->with('status', 'Unit deleted successfully.');
    }

    // --- Item Types (PK: id) ---
    public function storeType(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:item_types,name', 'description' => 'nullable|string']);
        ItemType::create($request->all());
        return redirect()->route('manager.index', ['table' => 'item_types'])->with('status', 'Type created successfully.');
    }

    public function updateType(Request $request, ItemType $type)
    {
        // [แก้ไข] ใช้ 'id'
        $request->validate(['name' => 'required|string|max:255|unique:item_types,name,' . $type->id . ',id', 'description' => 'nullable|string']);
        $type->update($request->all());
        return redirect()->route('manager.index', ['table' => 'item_types'])->with('status', 'Type updated successfully.');
    }

    public function destroyType(ItemType $type)
    {
        $type->delete();
        return redirect()->route('manager.index', ['table' => 'item_types'])->with('status', 'Type deleted successfully.');
    }

    // --- User Types (PK: user_type_id) ---
    public function storeUserType(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50|unique:user_types,name', 'description' => 'nullable|string']);
        UserType::create($request->all());
        return redirect()->route('manager.index', ['table' => 'user_types'])->with('status', 'User Type created successfully.');
    }

    public function updateUserType(Request $request, UserType $user_type)
    {
        // [ถูกต้อง] (PK นี้ถูกต้องอยู่แล้ว)
        $request->validate(['name' => ['required', 'string', 'max:50', Rule::unique('user_types')->ignore($user_type->user_type_id, 'user_type_id')], 'description' => 'nullable|string']);
        $user_type->update($request->all());
        return redirect()->route('manager.index', ['table' => 'user_types'])->with('status', 'User Type updated successfully.');
    }

    public function destroyUserType(UserType $user_type)
    {
        $user_type->delete();
        return redirect()->route('manager.index', ['table' => 'user_types'])->with('status', 'User Type deleted successfully.');
    }

    // --- Users (PK: user_id) ---
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('user_accounts')],
            'email' => ['required', 'email', 'max:255', Rule::unique('user_accounts')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            // [ถูกต้อง] (PK นี้ถูกต้องอยู่แล้ว)
            'user_type_id' => 'required|exists:user_types,user_type_id',
            'status' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        User::create($data); // (Model 'hashed' cast จะ Hash ให้อัตโนมัติ)

        return redirect()->route('manager.index', ['table' => 'users'])->with('status', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            // [ถูกต้อง] (PK นี้ถูกต้องอยู่แล้ว)
            'username' => ['required', 'string', 'max:50', Rule::unique('user_accounts')->ignore($user->user_id, 'user_id')],
            'email' => ['required', 'email', 'max:255', Rule::unique('user_accounts')->ignore($user->user_id, 'user_id')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'user_type_id' => 'required|exists:user_types,user_type_id',
            'status' => 'required|string',
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = $request->only(['username', 'email', 'first_name', 'last_name', 'tel', 'user_type_id', 'status']);

        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $user->update($updateData);
        return redirect()->route('manager.index', ['table' => 'users'])->with('status', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        if ($user->user_id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $user->delete();
        return redirect()->route('manager.index', ['table' => 'users'])->with('status', 'User deleted successfully.');
    }

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
            // [แก้ไข] ใช้ 'confirmed' เพื่อบังคับให้ "password_confirmation" ตรงกัน
            'password' => 'required|string|min:8|confirmed',
        ]);

        MemberAccount::create($data);

        return redirect()->route('manager.index', ['table' => 'member_accounts'])->with('status', 'Member created successfully.');
    }

    public function updateMember(Request $request, MemberAccount $member)
    {
        // 1. ตรวจสอบข้อมูลหลัก
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('member_accounts')->ignore($member->member_id, 'member_id')],
            'email' => ['required', 'email', 'max:255', Rule::unique('member_accounts')->ignore($member->member_id, 'member_id')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'status' => 'required|string',
            'points' => 'required|integer|min:0',

            // [แก้ไข] กฎการตรวจสอบรหัสผ่านใหม่
            // - ถ้ากรอก password (ใหม่) => current_password (เก่า) ต้องถูกกรอกด้วย
            //'current_password' => ['nullable', 'required_with:password', 'string'],
            // - ถ้ากรอก current_password (เก่า) => password (ใหม่) ต้องถูกกรอก และต้องมี confirmation
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. ดึงข้อมูลหลักที่จะอัปเดต
        $updateData = $request->only(['username', 'email', 'first_name', 'last_name', 'tel', 'status', 'points']);

        // 3. [Logic ใหม่] ตรวจสอบและอัปเดตรหัสผ่าน (ถ้ามีการกรอก)
        if (!empty($data['password'])) {

            // 3.1 ตรวจสอบว่ารหัสผ่านเก่า (current_password) ที่กรอกมา ตรงกับในฐานข้อมูลหรือไม่
            if (!Hash::check($data['current_password'], $member->password)) {
                // ถ้าไม่ตรง ให้ส่ง Error กลับไป
                return back()->with('error', 'The provided current password does not match our records.');
            }

            // 3.2 ถ้ารหัสผ่านเก่าถูกต้อง ให้เพิ่มรหัสผ่านใหม่เข้าไปใน $updateData
            // (Model 'hashed' cast จะ Hash ให้อัตโนมัติ)
            $updateData['password'] = $data['password'];
        }

        // 4. อัปเดตข้อมูล
        $member->update($updateData);

        // 5. ส่งข้อความสำเร็จ (ถ้าอัปเดตรหัสผ่านสำเร็จ มันจะรวมอยู่ในนี้)
        $statusMessage = 'Member updated successfully.';
        if (isset($updateData['password'])) {
            $statusMessage = 'Member and password updated successfully.';
        }

        return redirect()->route('manager.index', ['table' => 'member_accounts'])->with('status', $statusMessage);
    }

    public function destroyMember(MemberAccount $member)
    {
        $member->delete();
        return redirect()->route('manager.index', ['table' => 'member_accounts'])->with('status', 'Member deleted successfully.');
    }

    // --- Care Shops (PK: care_shop_id) ---
    public function storeCareShop(Request $request)
    {
        $data = $request->validate([
            'care_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'tel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string|max:50',
        ]);
        CareShop::create($data);
        return redirect()->route('manager.index', ['table' => 'care_shops'])->with('status', 'Care Shop created successfully.');
    }

    public function updateCareShop(Request $request, CareShop $care_shop)
    {
        $data = $request->validate([
            'care_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'tel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string|max:50',
        ]);
        $care_shop->update($data);
        return redirect()->route('manager.index', ['table' => 'care_shops'])->with('status', 'Care Shop updated successfully.');
    }

    public function destroyCareShop(CareShop $care_shop)
    {
        $care_shop->delete();
        return redirect()->route('manager.index', ['table' => 'care_shops'])->with('status', 'Care Shop deleted successfully.');
    }

    // --- Makeup Artists (PK: makeup_id) ---
    public function storeMakeupArtist(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        MakeupArtist::create($data);
        return redirect()->route('manager.index', ['table' => 'makeup_artists'])->with('status', 'Makeup Artist created successfully.');
    }

    public function updateMakeupArtist(Request $request, MakeupArtist $makeup_artist)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $makeup_artist->update($data);
        return redirect()->route('manager.index', ['table' => 'makeup_artists'])->with('status', 'Makeup Artist updated successfully.');
    }

    public function destroyMakeupArtist(MakeupArtist $makeup_artist)
    {
        $makeup_artist->delete();
        return redirect()->route('manager.index', ['table' => 'makeup_artists'])->with('status', 'Makeup Artist deleted successfully.');
    }

    // --- Photographers (PK: photographer_id) ---
    public function storePhotographer(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string|max:50',
        ]);
        Photographer::create($data);
        return redirect()->route('manager.index', ['table' => 'photographers'])->with('status', 'Photographer created successfully.');
    }

    public function updatePhotographer(Request $request, Photographer $photographer)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string|max:50',
        ]);
        $photographer->update($data);
        return redirect()->route('manager.index', ['table' => 'photographers'])->with('status', 'Photographer updated successfully.');
    }

    public function destroyPhotographer(Photographer $photographer)
    {
        $photographer->delete();
        return redirect()->route('manager.index', ['table' => 'photographers'])->with('status', 'Photographer deleted successfully.');
    }

    // --- Photographer Packages (PK: package_id) ---
    public function storePhotographerPackage(Request $request)
    {
        $data = $request->validate([
            'package_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        PhotographerPackage::create($data);
        return redirect()->route('manager.index', ['table' => 'photographer_packages'])->with('status', 'Package created successfully.');
    }

    public function updatePhotographerPackage(Request $request, PhotographerPackage $photographer_package)
    {
        $data = $request->validate([
            'package_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $photographer_package->update($data);
        return redirect()->route('manager.index', ['table' => 'photographer_packages'])->with('status', 'Package updated successfully.');
    }

    public function destroyPhotographerPackage(PhotographerPackage $photographer_package)
    {
        $photographer_package->delete();
        return redirect()->route('manager.index', ['table' => 'photographer_packages'])->with('status', 'Package deleted successfully.');
    }

    // --- Promotions (PK: promotion_id) ---
    public function storePromotion(Request $request)
    {
        $data = $request->validate([
            'promotion_name' => 'required|string|max:255',
            'discount_type' => 'required|string|max:50',
            'discount_value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|max:50',
        ]);
        Promotion::create($data);
        return redirect()->route('manager.index', ['table' => 'promotions'])->with('status', 'Promotion created successfully.');
    }

    public function updatePromotion(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'promotion_name' => 'required|string|max:255',
            'discount_type' => 'required|string|max:50',
            'discount_value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|max:50',
        ]);
        $promotion->update($data);
        return redirect()->route('manager.index', ['table' => 'promotions'])->with('status', 'Promotion updated successfully.');
    }

    public function destroyPromotion(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('manager.index', ['table' => 'promotions'])->with('status', 'Promotion deleted successfully.');
    }
}
