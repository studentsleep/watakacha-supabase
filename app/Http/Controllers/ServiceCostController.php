<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use Carbon\Carbon;

class ServiceCostController extends Controller
{
    public function index()
    {
        // 1. รายการรอลงบันทึก (คืนชุดแล้ว + ยังไม่ได้จ่ายค่าช่าง)
        // เช็คจาก service_cost_status ไม่ใช่ 'paid'
        $pending = Rental::with(['member', 'makeupArtist', 'photographer', 'photographerPackage'])
            ->where('status', 'returned') // ต้องคืนของแล้ว
            ->where(function ($q) {
                $q->whereNotNull('makeup_id')->orWhereNotNull('photographer_id'); // มีการจ้างช่าง
            })
            ->where(function ($q) {
                // ✅ แก้ไข: เช็คสถานะ หรือ เช็คว่ายังไม่มีการบันทึกต้นทุน
                $q->where('service_cost_status', '!=', 'paid')
                    ->orWhereNull('service_cost_status');
            })
            ->orderBy('return_date', 'desc')
            ->get();

        // 2. ประวัติการจ่ายเงินช่าง (สถานะ paid)
        $history = Rental::with(['member', 'makeupArtist', 'photographer', 'photographerPackage']) // ✅ เพิ่ม photographerPackage เข้ามาด้วย
            ->where('status', 'returned')
            ->where('service_cost_status', 'paid') // ✅ แก้ไข: เช็คสถานะ paid โดยตรง
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('manager.service_costs.index', compact('pending', 'history'));
    }

    public function updateCost(Request $request, $id)
    {
        $request->validate([
            'makeup_cost' => 'nullable|numeric|min:0',
            'photographer_cost' => 'nullable|numeric|min:0',
        ]);

        $rental = Rental::findOrFail($id);

        // ✅ อัปเดตข้อมูล (สำคัญ: Model Rental ต้องมี fillable ครบ)
        $rental->update([
            'makeup_cost' => $request->makeup_cost ?? 0,
            'photographer_cost' => $request->photographer_cost ?? 0,
            'service_cost_status' => 'paid' // ปรับสถานะเป็นจ่ายแล้ว
        ]);

        return response()->json(['success' => true, 'message' => 'บันทึกต้นทุนบริการเรียบร้อยแล้ว']);
    }
}
