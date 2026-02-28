<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Rental;

class MemberProfileController extends Controller
{
    // 1. แสดงหน้าแก้ไขข้อมูลส่วนตัว
    public function edit()
    {
        $member = Auth::guard('member')->user();
        return view('member.profile', compact('member'));
    }

    // 2. บันทึกการอัปเดตข้อมูลส่วนตัว
    public function update(Request $request)
    {
        /** @var \App\Models\MemberAccount $member */
        $member = Auth::guard('member')->user();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'tel'        => 'required|string|max:15|unique:member_accounts,tel,' . $member->member_id . ',member_id',
            'email'      => 'nullable|email|max:150|unique:member_accounts,email,' . $member->member_id . ',member_id',
            'password'   => 'nullable|string|min:8|confirmed', // ใส่รหัสใหม่เฉพาะตอนอยากเปลี่ยน
        ]);

        $member->first_name = $request->first_name;
        $member->last_name = $request->last_name;
        $member->tel = $request->tel;
        $member->email = $request->email;

        // ถ้ามีการกรอกรหัสผ่านใหม่ ให้เปลี่ยนรหัสด้วย
        if ($request->filled('password')) {
            $member->password = Hash::make($request->password);
        }

        $member->save();

        return back()->with('success', 'อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }

    // 3. แสดงหน้าประวัติการเช่า
    public function history()
    {
        $member = Auth::guard('member')->user();

        // ดึงประวัติการเช่าของตัวเอง พร้อมรายการสินค้าที่เช่า
        $rentals = Rental::with('items.item')
            ->where('member_id', $member->member_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('member.history', compact('rentals'));
    }
}
