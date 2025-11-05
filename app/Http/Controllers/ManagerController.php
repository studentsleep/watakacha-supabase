<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// [แก้ไข] เรียกใช้ Model ทั้งหมดจาก App\Models
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
     * หน้า Index หลักสำหรับจัดการข้อมูลทั้งหมด
     */
    public function index(Request $request)
    {
        // รับค่าตารางที่ผู้ใช้เลือกจาก URL, หากไม่มีค่าเริ่มต้นคือ 'users'
        $table = $request->input('table', 'users');

        $data = [
            'table' => $table,
        ];
        
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
            // [ดึงข้อมูลแบบใหม่]
            $query = Item::with(['type', 'unit', 'images']);
            $data['items'] = $query->orderBy('item_name')->paginate(20)->withQueryString();
            
            // (Modal ต้องใช้ Units และ Types)
            $data['units'] = ItemUnit::orderBy('name')->get();
            $data['types'] = ItemType::orderBy('name')->get();
        } 
        
        elseif ($table == 'item_units') { // [แก้ไข] ใช้ item_units (จาก Sidebar)
            $data['units'] = ItemUnit::orderBy('name')->get();
        } 
        
        elseif ($table == 'item_types') { // [แก้ไข] ใช้ item_types (จาก Sidebar)
            $data['types'] = ItemType::orderBy('name')->get();
        }

        return view('manager.index', $data);
    }

    // --- [CRUD ใหม่] สำหรับ Items ---
    public function storeItem(Request $request)
    {
        $data = $request->validate([
             'name' => 'required|string|max:255',
             'description' => 'nullable|string',
             'price' => 'required|numeric|min:0',
             'stock' => 'required|integer|min:0',
             'item_unit_id' => 'required|exists:item_units,id', // [แก้ไข] DB เดิม
             'item_type_id' => 'required|exists:item_types,id', // [แก้ไข] DB เดิม
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
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item created successfully.');
    }

    public function updateItem(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'item_unit_id' => 'required|exists:item_units,id', // [แก้ไข] DB เดิม
            'item_type_id' => 'required|exists:item_types,id', // [แก้ไข] DB เดิม
        ]);

        $item->update([
            'item_name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'item_unit_id' => $data['item_unit_id'],
            'item_type_id' => $data['item_type_id'],
        ]);
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item updated successfully.');
    }
    
    // [CRUD ใหม่] (จัดการรูปภาพ)
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

        return back()->with('status', 'Image uploaded successfully.');
    }

    public function destroyItemImage(ItemImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $item = $image->item; // ดึง item ก่อนลบ
        $isMain = $image->is_main;
        $image->delete();

        if ($isMain && $item->images()->count() > 0) {
            $newMainImage = $item->images()->first();
            if ($newMainImage) {
                $newMainImage->is_main = true;
                $newMainImage->save();
            }
        }
        return back()->with('status', 'Image deleted successfully.');
    }

    public function setMainImage(ItemImage $image)
    {
        $item = $image->item;
        $item->images()->update(['is_main' => false]);
        $image->is_main = true;
        $image->save();

        return back()->with('status', 'Main image has been set.');
    }

    public function destroyItem(Item $item)
    {
        foreach ($item->images as $img) { 
            Storage::disk('public')->delete($img->path); 
        }
        $item->images()->delete();
        $item->delete();
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item deleted successfully.');
    }

    // --- [CRUD ใหม่] สำหรับ Item Units ---
    public function storeUnit(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:item_units,name', 'description' => 'nullable|string']);
        ItemUnit::create(['name' => $request->name, 'description' => $request->description]);
        return redirect()->route('manager.index', ['table' => 'item_units'])->with('status', 'Unit created successfully.');
    }

    public function updateUnit(Request $request, ItemUnit $unit)
    {
        $request->validate(['name' => 'required|string|max:255|unique:item_units,name,' . $unit->id, 'description' => 'nullable|string']); // [แก้ไข] DB เดิม
        $unit->update(['name' => $request->name, 'description' => $request->description]);
        return redirect()->route('manager.index', ['table' => 'item_units'])->with('status', 'Unit updated successfully.');
    }

    public function destroyUnit(ItemUnit $unit)
    {
        $unit->delete();
        return redirect()->route('manager.index', ['table' => 'item_units'])->with('status', 'Unit deleted successfully.');
    }

    // --- [CRUD ใหม่] สำหรับ Item Types ---
    public function storeType(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:item_types,name', 'description' => 'nullable|string']);
        ItemType::create(['name' => $request->name, 'description' => $request->description]);
        return redirect()->route('manager.index', ['table' => 'item_types'])->with('status', 'Type created successfully.');
    }

    public function updateType(Request $request, ItemType $type)
    {
        $request->validate(['name' => 'required|string|max:255|unique:item_types,name,' . $type->id, 'description' => 'nullable|string']); // [แก้ไข] DB เดิม
        $type->update(['name' => $request->name, 'description' => $request->description]);
        return redirect()->route('manager.index', ['table' => 'item_types'])->with('status', 'Type updated successfully.');
    }

    public function destroyType(ItemType $type)
    {
        $type->delete();
        return redirect()->route('manager.index', ['table' => 'item_types'])->with('status', 'Type deleted successfully.');
    }

    // --- [CRUD ใหม่] สำหรับ User Type ---
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
            'name' => ['required', 'string', 'max:50', Rule::unique('user_types')->ignore($user_type->id)], // [แก้ไข] DB เดิม
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

    // --- [CRUD ใหม่] สำหรับ User ---
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')], 
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'user_type_id' => 'required|exists:user_types,id', // [แก้ไข] DB เดิม
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
        
        return redirect()->route('manager.index', ['table' => 'users'])->with('status', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)], // [แก้ไข] DB เดิม
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], // [แก้ไข] DB เดิม
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'user_type_id' => 'required|exists:user_types,id', // [แก้ไข] DB เดิม
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
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $user->delete();
        return redirect()->route('manager.index', ['table' => 'users'])->with('status', 'User deleted successfully.');
    }
}