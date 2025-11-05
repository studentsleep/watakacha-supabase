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
        } 
        elseif ($table == 'user_types') {
            $data['user_types'] = UserType::orderBy('name')->get();
        } 
        elseif ($table == 'items') {
            $query = Item::with(['type', 'unit', 'images']);
            $data['items'] = $query->orderBy('item_name')->paginate(20)->withQueryString();
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
            'item_name' => $data['name'], 'description' => $data['description'],
            'price' => $data['price'], 'stock' => $data['stock'],
            'id' => $data['id'], 'id' => $data['id'],
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
            'item_name' => $data['name'], 'description' => $data['description'],
            'price' => $data['price'], 'stock' => $data['stock'],
            'id' => $data['id'], 'id' => $data['id'],
        ]);
        return redirect()->route('manager.index', ['table' => 'items'])->with('status', 'Item updated successfully.');
    }
    
    // (ฟังก์ชันรูปภาพ Items - ถูกต้องแล้ว)
    public function uploadItemImage(Request $request, Item $item) { /* (Logic เดิม) */ }
    public function destroyItemImage(ItemImage $image) { /* (Logic เดิม) */ }
    public function setMainImage(ItemImage $image) { /* (Logic เดิม) */ }
    public function destroyItem(Item $item) { /* (Logic เดิม) */ }


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
            'first_name' => 'required|string|max:255', 'last_name' => 'required|string|max:255',
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
            'first_name' => 'required|string|max:255', 'last_name' => 'required|string|max:255',
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
}