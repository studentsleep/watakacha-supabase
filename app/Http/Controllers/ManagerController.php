<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// --- Model ทั้งหมด ---
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\ItemType;
use App\Models\ItemImage;
use App\Models\User;
use App\Models\UserType;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ManagerController extends Controller
{
    /**
     * หน้า Index หลักสำหรับจัดการข้อมูลทั้งหมด (โค้ดเดิมของคุณ)
     */
    public function index(Request $request)
    {
        $table = $request->input('table', 'users');
        $data = ['table' => $table,];

        if ($table == 'users') {
             $data['users'] = User::with('userType') 
                                ->orderBy('username')
                                ->paginate(20)
                                ->withQueryString();
            $data['user_types'] = UserType::orderBy('name')->get(); 
        } 
        elseif ($table == 'user_types') {
            $data['user_types'] = UserType::orderBy('name')->get();
        } 
        elseif ($table == 'items') {
            // (ใช้ Eager Loading 'images' จาก Logic ใหม่)
            $query = Item::with(['type', 'unit', 'images']);
            $data['items'] = $query->paginate(20)->withQueryString();
            $data['units'] = ItemUnit::orderBy('name')->get();
            $data['types'] = ItemType::orderBy('name')->get();
        } 
        elseif ($table == 'item_units') {
            $data['units'] = ItemUnit::orderBy('name')->get();
        } 
        elseif ($table == 'item_types') {
            $data['types'] = ItemType::orderBy('name')->get();
        }
        
        return view('manager.index', $data);
    }

    // --- ▼▼▼ START: โค้ด CRUD ใหม่ (จากไฟล์ที่ 2) ที่ถูกแก้ไขแล้ว ▼▼▼ ---

    /**
     * Logic ใหม่จากไฟล์ที่ 2
     * (แก้ไข Validation และ Redirect)
     */
    public function storeItem(Request $request)
    {
        $data = $request->validate([
             'name' => 'required|string|max:255',
             'description' => 'nullable|string',
             'price' => 'required|numeric|min:0',
             'stock' => 'required|integer|min:0',
             // (แก้ไข) ตรวจสอบกับ PK 'id' ของตารางเดิม
             'item_unit_id' => 'required|exists:item_units,id', 
             'item_type_id' => 'required|exists:item_types,id',
             'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $item = Item::create([
            'item_name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'item_unit_id' => $data['item_unit_id'],
            'item_type_id' => $data['item_type_id'],
            'status' => 'active',
        ]);
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('items', 'public');
                $item->images()->create([
                    'path' => $path,
                    'is_main' => $index === 0, 
                ]);
            }
        }
        
        // (แก้ไข) ใช้ Redirect แบบเดิม เพื่อให้ Alpine UI รีเฟรชถูกต้อง
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item created successfully.');
    }

    /**
     * Logic ใหม่จากไฟล์ที่ 2
     * (แก้ไข Validation และ Redirect)
     */
    public function updateItem(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            // (แก้ไข) ตรวจสอบกับ PK 'id' ของตารางเดิม
            'item_unit_id' => 'required|exists:item_units,id', 
            'item_type_id' => 'required|exists:item_types,id',
        ]);

        $item->update([
            'item_name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'item_unit_id' => $data['item_unit_id'],
            'item_type_id' => $data['item_type_id'],
        ]);
        
        // (แก้ไข) ใช้ Redirect แบบเดิม
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item updated successfully.');
    }

    /**
     * Logic ใหม่จากไฟล์ที่ 2
     * (แก้ไข Redirect)
     */
    public function destroyItem(Item $item)
    {
        foreach ($item->images as $img) { Storage::disk('public')->delete($img->path); }
        $item->images()->delete();
        $item->delete();
        
        // (แก้ไข) ใช้ Redirect แบบเดิม
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item deleted successfully.');
    }
    
    /**
     * Logic ใหม่จากไฟล์ที่ 2
     * (แก้ไข Redirect)
     */
    public function uploadItemImage(Request $request, Item $item) 
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        $path = $request->file('image')->store('items', 'public');
        $isFirstImage = $item->images()->count() == 0;

        $item->images()->create([
            'path' => $path,
            'is_main' => $isFirstImage,
        ]);
        
        // (แก้ไข) ใช้ Redirect แบบเดิม
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Image uploaded successfully.');
    }

    /**
     * Logic ใหม่จากไฟล์ที่ 2
     * (แก้ไข Redirect)
     */
    public function destroyItemImage(ItemImage $image) 
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();
        $item = $image->item;
        if ($image->is_main && $item->images()->count() > 0) {
            $newMainImage = $item->images()->first();
            $newMainImage->is_main = true;
            $newMainImage->save();
        }
        
        // (แก้ไข) ใช้ Redirect แบบเดิม
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Image deleted successfully.');
    }

    /**
     * Logic ใหม่จากไฟล์ที่ 2
     * (แก้ไข Redirect)
     */
    public function setMainImage(ItemImage $image) 
    {
        $item = $image->item;
        $item->images()->update(['is_main' => false]);
        $image->is_main = true;
        $image->save();
        
        // (แก้ไข) ใช้ Redirect แบบเดิม
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Main image has been set.');
    }
    
    // --- ▲▲▲ END: โค้ด CRUD ใหม่ ---


    // --- CRUD สำหรับ Item Units (โค้ดเดิมของคุณ) ---
    public function storeUnit(Request $request)
    {
        // (แก้ไข) เปลี่ยน 'des' เป็น 'description' ให้ตรงกับไฟล์เดิม
        $request->validate(['name' => 'required|string|max:255|unique:item_units,name', 'description' => 'nullable|string']);
        ItemUnit::create(['name' => $request->name, 'description' => $request->description]);
        return redirect()->route('manager.index', ['table' => 'item_units'])->with('status', 'Unit created successfully.');
    }

    public function updateUnit(Request $request, ItemUnit $unit)
    {
        // (แก้ไข) เปลี่ยน 'des' เป็น 'description' และ KeyName ให้ตรงกับไฟล์เดิม
        $request->validate(['name' => 'required|string|max:255|unique:item_units,name,' . $unit->getKey() . ',' . $unit->getKeyName(), 'description' => 'nullable|string']);
        $unit->update(['name' => $request->name, 'description' => $request->description]);
        return redirect()->route('manager.index', ['table' => 'item_units'])->with('status', 'Unit updated successfully.');
    }

    public function destroyUnit(ItemUnit $unit)
    {
        $unit->delete();
        return redirect()->route('manager.index', ['table' => 'item_units'])->with('status', 'Unit deleted successfully.');
    }

    // --- CRUD สำหรับ Item Types (โค้ดเดิมของคุณ) ---
    public function storeType(Request $request)
    {
        // (แก้ไข) เปลี่ยน 'des' เป็น 'description'
        $request->validate(['name' => 'required|string|max:255|unique:item_types,name', 'description' => 'nullable|string']);
        ItemType::create(['name' => $request->name, 'description' => $request->description]);
        return redirect()->route('manager.index', ['table' => 'item_types'])->with('status', 'Type created successfully.');
    }

    public function updateType(Request $request, ItemType $type)
    {
        // (แก้ไข) เปลี่ยน 'des' เป็น 'description' และ KeyName ให้ตรงกับไฟล์เดิม
        $request->validate(['name' => 'required|string|max:255|unique:item_types,name,' . $type->getKey() . ',' . $type->getKeyName(), 'description' => 'nullable|string']);
        $type->update(['name' => $request->name, 'description' => $request->description]);
        return redirect()->route('manager.index', ['table' => 'item_types'])->with('status', 'Type updated successfully.');
    }

    public function destroyType(ItemType $type)
    {
        $type->delete();
        return redirect()->route('manager.index', ['table' => 'item_types'])->with('status', 'Type deleted successfully.');
    }

    // --- ฟังก์ชันสำหรับจัดการ User Type (โค้ดเดิมของคุณ) ---
    public function storeUserType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:user_types,name',
            'description' => 'nullable|string'
        ]);
        UserType::create($request->all());
        return redirect()->route('manager.index', ['table' => 'user_types'])->with('status', 'User Type created successfully.');
    }

    public function updateUserType(Request $request, UserType $user_type)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('user_types')->ignore($user_type->getKey())],
            'description' => 'nullable|string'
        ]);
        $user_type->update($request->all());
        return redirect()->route('manager.index', ['table' => 'user_types'])->with('status', 'User Type updated successfully.');
    }

    public function destroyUserType(UserType $user_type)
    {
        if ($user_type->users()->count() > 0) {
            return back()->with('error', 'Cannot delete this user type, it is currently in use.');
        }
        $user_type->delete();
        return redirect()->route('manager.index', ['table' => 'user_types'])->with('status', 'User Type deleted successfully.');
    }

    // --- ฟังก์ชันสำหรับจัดการ User (โค้ดเดิมของคุณ) ---
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            // (แก้ไข) ใช้ user_id และ PK ที่ถูกต้อง (จากไฟล์เดิม)
            'username' => ['required', 'string', 'max:50', Rule::unique('user_accounts')], 
            'email' => ['required', 'email', 'max:255', Rule::unique('user_accounts')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'user_type_id' => 'required|exists:user_types,user_type_id', // (อันนี้ดูเหมือนจะเป็น PK ของ user_types เดิม)
            'status' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'tel' => $data['tel'],
            'user_type_id' => $data['user_type_id'],
            'status' => $data['status'],
            'password' => Hash::make($data['password']),
        ]);
        
        return redirect()->route('manager.index', ['table' => 'user'])->with('status', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            // (แก้ไข) ใช้ PK 'user_id' ที่ถูกต้อง (จากไฟล์เดิม)
            'username' => ['required', 'string', 'max:50', Rule::unique('user_accounts')->ignore($user->user_id, 'user_id')], 
            'email' => ['required', 'email', 'max:255', Rule::unique('user_accounts')->ignore($user->user_id, 'user_id')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'user_type_id' => 'required|exists:user_types,user_type_id', // (PK เดิม)
            'status' => 'required|string',
            'password' => 'nullable|string|min:8', 
        ]);

        $updateData = $request->only(['username', 'email', 'first_name', 'last_name', 'tel', 'user_type_id', 'status']);

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
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
}
