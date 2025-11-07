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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ManagerController extends Controller
{
    // (index function ถูกต้อง ไม่ต้องแก้ไข)
    public function index(Request $request)
    {
        $table = $request->input('table', 'users');
        $data = ['table' => $table];

        if ($table == 'users') {
            $data['users'] = User::with('userType')
                ->orderBy('username')
                ->paginate(20)
                ->withQueryString();
            $data['user_types'] = UserType::orderBy('name')->get();
        } elseif ($table == 'user_types') {
            $data['user_types'] = UserType::orderBy('name')->get();
        } elseif ($table == 'items') {
            $query = Item::with(['type', 'unit', 'images']);
            $data['items'] = $query->orderBy('item_name')->paginate(20)->withQueryString();
            $data['units'] = ItemUnit::orderBy('name')->get();
            $data['types'] = ItemType::orderBy('name')->get();
        } elseif ($table == 'item_units') {
            $data['units'] = ItemUnit::orderBy('name')->get();
        } elseif ($table == 'item_types') {
            $data['types'] = ItemType::orderBy('name')->get();
        } elseif ($table == 'member_accounts') {
            $data['members'] = MemberAccount::orderBy('username')
                ->paginate(20)
                ->withQueryString();
        } elseif ($table == 'point_transactions') {
            $data['transactions'] = PointTransaction::with('member') // ดึงข้อมูล Member มาด้วย
                ->orderBy('transaction_date', 'desc') // เรียงจากล่าสุดไปเก่าสุด
                ->paginate(30) // แสดงหน้าละ 30 รายการ
                ->withQueryString();
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
            'id' => 'required|exists:item_units,id',
            // [แก้ไข] ต้องตรวจสอบกับ 'id' (PK ของตาราง item_types)
            'id' => 'required|exists:item_types,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $item = Item::create([
            'item_name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'id' => $data['id'],
            'id' => $data['id'],
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
            'id' => 'required|exists:item_units,id',
            // [แก้ไข] ต้องตรวจสอบกับ 'id'
            'id' => 'required|exists:item_types,id',
        ]);
        $item->update([
            'item_name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'id' => $data['id'],
            'id' => $data['id'],
        ]);
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item updated successfully.');
    }

    public function uploadItemImage(Request $request, Item $item)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $path = $request->file('image')->store('items', 'public');

        // ตรวจสอบว่านี่เป็นรูปแรกหรือไม่
        $isFirstImage = $item->images()->count() == 0;

        $item->images()->create([
            'path' => $path,
            'is_main' => $isFirstImage, // ตั้งเป็นรูปหลักถ้านี่คือรูปแรก
        ]);

        return back()->with('status', 'Image uploaded successfully.');
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
}
