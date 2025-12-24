<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // เพิ่มการชำระเงิน (เช่น จ่ายส่วนที่เหลือตอนคืนของ)
    public function store(Request $request)
    {
        $request->validate([
            'rental_id' => 'required|exists:rentals,rental_id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'type' => 'required|string', // remaining, fine
        ]);

        Payment::create([
            'rental_id' => $request->rental_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        return back()->with('status', 'บันทึกการชำระเงินเรียบร้อย');
    }

    // แก้ไข
    public function update(Request $request, Payment $payment)
    {
        $payment->update($request->only('amount', 'payment_method', 'type'));
        return back()->with('status', 'แก้ไขข้อมูลสำเร็จ');
    }

    // ลบ
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return back()->with('status', 'ลบรายการสำเร็จ');
    }
}