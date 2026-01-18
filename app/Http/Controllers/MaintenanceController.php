<?php

namespace App\Http\Controllers;

use App\Models\ItemMaintenance;
use App\Models\CareShop;
use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    public function index()
    {
        // 1. รายการรอส่ง (Pending)
        $pending = ItemMaintenance::with(['item', 'rental.member'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. รายการกำลังซ่อม/ซัก (In Progress)
        $inProgress = ItemMaintenance::with(['item', 'careShop'])
            ->where('status', 'in_progress')
            ->orderBy('sent_at', 'desc')
            ->get();

        // 3. ประวัติ (History)
        $history = ItemMaintenance::with(['item', 'careShop'])
            ->where('status', 'completed')
            ->orderBy('received_at', 'desc')
            ->paginate(10);

        // ดึงรายชื่อร้านค้า (สำหรับ Dropdown) - ใช้ PK: care_shop_id
        $shops = CareShop::where('status', 'active')->get();

        return view('maintenance.index', compact('pending', 'inProgress', 'history', 'shops'));
    }

    // ส่งร้าน
    public function sendToShop(Request $request, $id)
    {
        $request->validate([
            'care_shop_id' => 'required|exists:care_shops,care_shop_id', // เช็ค FK ให้ถูก
            'type' => 'required',
        ]);

        $maintenance = ItemMaintenance::findOrFail($id);
        $maintenance->update([
            'care_shop_id' => $request->care_shop_id,
            'type' => $request->type,
            'status' => 'in_progress',
            'sent_at' => Carbon::now(),
        ]);

        return response()->json(['success' => true, 'message' => 'ส่งร้านเรียบร้อยแล้ว']);
    }

    // รับของคืน
    public function receiveFromShop(Request $request, $id)
    {
        $request->validate([
            'shop_cost' => 'required|numeric|min:0',
        ]);

        $maintenance = ItemMaintenance::findOrFail($id);

        // อัปเดตสถานะ Maintenance
        $maintenance->update([
            'status' => 'completed',
            'shop_cost' => $request->shop_cost,
            'received_at' => Carbon::now(),
        ]);

        // อัปเดตสถานะ Item กลับเป็นพร้อมเช่า
        $item = Item::find($maintenance->item_id);
        if ($item) {
            $item->status = 'active'; // หรือสถานะว่างตามระบบคุณ (ใน DB น่าจะใช้ active)
            $item->save();
        }

        return response()->json(['success' => true, 'message' => 'รับของคืนเรียบร้อยแล้ว']);
    }
}
