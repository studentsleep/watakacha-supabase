<x-app-layout>
    {{-- ✅ เพิ่ม SweetAlert2 --}}

    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 rounded-lg text-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4" />
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                </svg>
            </div>
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                รับคืนชุด (Return Service)
            </h2>
        </div>
    </x-slot>

    <div class="py-8" x-data="returnSystem()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🔍 Search Box --}}
            <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form action="{{ route('reception.return') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:flex-grow">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">ค้นหารายการที่รอคืน</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="พิมพ์ ID, ชื่อลูกค้า หรือเบอร์โทร..."
                                class="w-full pl-10 rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 h-11 transition-all">
                        </div>
                    </div>
                    <button type="submit" class="w-full md:w-auto px-6 h-11 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        ค้นหา
                    </button>
                </form>
            </div>

            {{-- 📋 Table --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    @if($rentals->isEmpty())
                    <div class="text-center py-16 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-medium">ไม่พบรายการที่ต้องคืน</p>
                        <p class="text-sm">ลองค้นหาด้วยคำอื่น หรือคืนครบหมดแล้ว</p>
                    </div>
                    @else
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reference ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ลูกค้า</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">กำหนดคืน</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach($rentals as $rental)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150 group">

                                {{-- Reference ID --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">#{{ $rental->rental_id }}</span>
                                </td>

                                {{-- ลูกค้า (Avatar Style) --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-tr from-green-400 to-teal-500 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                            {{ mb_substr($rental->member ? $rental->member->first_name : ($rental->description ? 'G' : '?'), 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $rental->member ? $rental->member->first_name . ' ' . $rental->member->last_name : 'ลูกค้าทั่วไป (Guest)' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $rental->member ? $rental->member->tel : ($rental->description ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- วันที่ --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-100">
                                            {{ \Carbon\Carbon::parse($rental->return_date)->addYears(543)->locale('th')->translatedFormat('d M Y') }}
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            เช่าเมื่อ: {{ \Carbon\Carbon::parse($rental->rental_date)->addYears(543)->locale('th')->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                </td>

                                {{-- สถานะ --}}
                                <td class="px-6 py-4 text-center">
                                    @php
                                    $diff = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($rental->return_date), false);
                                    @endphp
                                    @if($diff < 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        เกิน {{ abs(intval($diff)) }} วัน
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                            ปกติ
                                        </span>
                                        @endif
                                </td>

                                {{-- ปุ่มจัดการ --}}
                                <td class="px-6 py-4 text-center">
                                    <button @click="openModal({{ Js::from($rental) }})"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none shadow-md shadow-green-200 transition-transform hover:-translate-y-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                        รับคืน
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div class="mt-4 px-6 pb-6">
                    {{ $rentals->links() }}
                </div>
            </div>
        </div>

        {{-- ▼▼▼ MODAL ตรวจสอบรายการคืน ▼▼▼ --}}
        <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block w-full max-w-4xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-2xl rounded-2xl relative z-50">

                    {{-- Header --}}
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="p-1.5 bg-blue-100 text-blue-600 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg></span>
                            ตรวจสอบรายการคืน <span class="text-gray-500 font-normal">#<span x-text="currentRental?.rental_id"></span></span>
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">

                        {{-- รายการสินค้าและอุปกรณ์เสริม --}}
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex justify-between">
                                <span>รายการสินค้าที่ต้องคืน</span>
                                <span>(ทั้งหมด <span x-text="returnItems.length"></span> รายการ)</span>
                            </h4>

                            <template x-for="(item, index) in returnItems" :key="index">
                                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm"
                                    :class="item.is_accessory ? 'border-orange-200 bg-orange-50/30' : ''">

                                    <div class="flex justify-between items-center mb-3">
                                        <div class="flex items-center gap-3">
                                            {{-- ไอคอนลำดับ --}}
                                            <div class="h-10 w-10 rounded-lg flex items-center justify-center font-bold text-lg"
                                                :class="item.is_accessory ? 'bg-orange-100 text-orange-600' : 'bg-indigo-50 text-indigo-600'">
                                                <span x-text="index + 1"></span>
                                            </div>

                                            <div>
                                                {{-- ชื่อสินค้า --}}
                                                <div class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                                    <span x-text="item.item_name"></span>
                                                    {{-- Badge บอกว่าเป็นอุปกรณ์เสริม --}}
                                                    <template x-if="item.is_accessory">
                                                        <span class="px-2 py-0.5 rounded text-[10px] bg-orange-100 text-orange-600 border border-orange-200">Accessory</span>
                                                    </template>
                                                </div>
                                                <div class="text-xs text-gray-500">จำนวนที่ยืม: <span class="font-bold" x-text="item.rented_qty"></span></div>
                                            </div>
                                        </div>

                                        {{-- ปุ่มเพิ่มความเสียหาย --}}
                                        <button @click="addDamage(index)"
                                            :disabled="getDamagedQty(item) >= item.rented_qty"
                                            class="text-xs font-bold flex items-center gap-1 text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            แจ้งเสียหาย
                                        </button>
                                    </div>

                                    {{-- พื้นที่กรอกรายละเอียดความเสียหาย --}}
                                    <div class="space-y-2 pl-12" x-show="item.damages.length > 0">
                                        <template x-for="(dmg, dmgIndex) in item.damages" :key="dmgIndex">
                                            <div class="bg-red-50/50 border border-red-100 rounded-lg p-3 grid grid-cols-12 gap-3 items-center animate-pulse-once">

                                                {{-- 1. จำนวนที่เสียหาย --}}
                                                <div class="col-span-2">
                                                    <label class="text-[10px] text-gray-400 block mb-1">จำนวน</label>
                                                    <input type="number" x-model="dmg.qty" min="1" :max="item.rented_qty" @change="recalcSummary()" class="w-full text-xs border-gray-200 rounded text-center h-8 focus:ring-red-500 focus:border-red-500">
                                                </div>

                                                {{-- 2. สาเหตุ / รายละเอียด --}}
                                                <div class="col-span-6">
                                                    <label class="text-[10px] text-gray-400 block mb-1">สาเหตุ / รายละเอียด</label>
                                                    <div class="flex gap-2 mb-1">
                                                        <span @click="setDamageDetails(dmg, 'ขาด', 500)" class="cursor-pointer px-2 py-0.5 text-[10px] rounded border hover:bg-red-50" :class="dmg.cause==='ขาด' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-500 border-gray-200'">ขาด</span>
                                                        <span @click="setDamageDetails(dmg, 'เปื้อน', 200)" class="cursor-pointer px-2 py-0.5 text-[10px] rounded border hover:bg-yellow-50" :class="dmg.cause==='เปื้อน' ? 'bg-yellow-400 text-white border-yellow-400' : 'bg-white text-gray-500 border-gray-200'">เปื้อน</span>
                                                        <span @click="setDamageDetails(dmg, 'ชำรุด', 300)" class="cursor-pointer px-2 py-0.5 text-[10px] rounded border hover:bg-orange-50" :class="dmg.cause==='ชำรุด' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-500 border-gray-200'">ชำรุด</span>
                                                        <span @click="setDamageDetails(dmg, 'หาย', item.item_price || 0)" class="cursor-pointer px-2 py-0.5 text-[10px] rounded border hover:bg-gray-50" :class="dmg.cause==='หาย' ? 'bg-black text-white border-black' : 'bg-white text-gray-500 border-gray-200'">หาย</span>
                                                    </div>
                                                    <input type="text" x-model="dmg.note" placeholder="ระบุเพิ่มเติม..." class="w-full text-xs border-gray-200 rounded h-8 focus:ring-red-500 focus:border-red-500">
                                                </div>

                                                {{-- 3. ค่าปรับ --}}
                                                <div class="col-span-3">
                                                    <label class="text-[10px] text-red-400 block mb-1">ค่าปรับ (บาท)</label>
                                                    <input type="number" x-model="dmg.fine" min="0" @change="recalcSummary()" class="w-full text-xs border-red-200 rounded text-right h-8 font-bold text-red-600 bg-white focus:ring-red-500 focus:border-red-500">
                                                </div>

                                                {{-- 4. ปุ่มลบ --}}
                                                <div class="col-span-1 text-center pt-4">
                                                    <button @click="removeDamage(index, dmgIndex)" class="text-gray-400 hover:text-red-500 transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- แสดงจำนวนที่สภาพปกติ --}}
                                    <div class="mt-2 text-right text-xs text-gray-500" x-show="getDamagedQty(item) < item.rented_qty">
                                        สภาพปกติ: <span class="font-bold text-green-600" x-text="item.rented_qty - getDamagedQty(item)"></span> ชิ้น
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- สรุปยอดเงิน (ส่วนนี้เหมือนเดิม) --}}
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase mb-2 block">ช่องทางรับเงินส่วนต่าง</label>
                                    <select x-model="paymentMethod" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                                        <option value="cash">💵 เงินสด (Cash)</option>
                                        <option value="transfer">🏦 โอนเงิน (Transfer)</option>
                                        <option value="credit_card">💳 บัตรเครดิต</option>
                                    </select>
                                </div>
                                <div class="space-y-2 text-sm text-right">
                                    <div class="flex justify-between text-gray-600"><span>ยอดค้างชำระเดิม</span><span x-text="formatNumber(remainingAmount)"></span></div>
                                    <div class="flex justify-between text-red-600" x-show="overdueFine > 0"><span>ค่าปรับคืนช้า (<span x-text="overdueDays"></span> วัน)</span><span x-text="'+ ' + formatNumber(overdueFine)"></span></div>
                                    <div class="flex justify-between text-red-600" x-show="totalDamageFine > 0"><span>ค่าปรับเสียหาย</span><span x-text="'+ ' + formatNumber(totalDamageFine)"></span></div>
                                    <div class="border-t border-gray-200 pt-2 flex justify-between items-center text-base font-bold text-gray-900">
                                        <span>ยอดต้องรับสุทธิ</span>
                                        <span class="text-xl text-green-600" x-text="formatNumber(grandTotal)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                        <button @click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition">ยกเลิก</button>
                        <button @click="confirmReturn" class="px-6 py-2.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                            <span>ยืนยันการคืน</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function returnSystem() {
            return {
                isModalOpen: false,
                showConfirmDialog: false,
                isSubmitting: false,
                currentRental: null,
                returnItems: [],

                remainingAmount: 0,
                overdueDays: 0,
                overdueFine: 0,
                totalDamageFine: 0,
                grandTotal: 0,
                paymentMethod: 'cash',

                openModal(rental) {
                    this.currentRental = rental;

                    // 1. คำนวณยอดที่จ่ายไปแล้ว
                    let totalPaid = 0;
                    if (rental.payments && rental.payments.length > 0) {
                        totalPaid = rental.payments
                            .filter(p => p.status === 'paid')
                            .reduce((sum, p) => sum + parseFloat(p.amount), 0);
                    }
                    this.remainingAmount = Math.max(0, parseFloat(rental.total_amount) - totalPaid);

                    // 2. ดึงข้อมูลสินค้า (รวมทั้ง Item และ Accessory ในลูปเดียว)
                    this.returnItems = rental.items.map(line => {
                        let id = null;
                        let name = '';
                        let isAcc = false;

                        // ✅ แก้ไข Logic การดึงชื่อ: ถ้าเป็น accessory ให้ดึงชื่อจาก accessory
                        if (line.accessory_id) {
                            id = line.accessory_id;
                            // Check if line.accessory exists (loaded via controller)
                            let accName = line.accessory ? line.accessory.name : 'Unknown Accessory';
                            name = accName;
                            isAcc = true;
                        } else {
                            id = line.item_id;
                            name = line.item ? line.item.item_name : 'Unknown Item';
                            isAcc = false;
                        }

                        return {
                            item_id: id,
                            item_name: name,
                            rented_qty: line.quantity,
                            is_accessory: isAcc,
                            damages: []
                        };
                    });

                    this.calculateOverdue();
                    this.recalcSummary();
                    this.isModalOpen = true;
                },

                closeModal() {
                    this.isModalOpen = false;
                    this.currentRental = null;
                },

                addDamage(index) {
                    let item = this.returnItems[index];
                    if (this.getDamagedQty(item) < item.rented_qty) {
                        item.damages.push({
                            qty: 1,
                            cause: '',
                            note: '',
                            fine: 0
                        });
                        this.recalcSummary();
                    }
                },

                removeDamage(itemIndex, dmgIndex) {
                    this.returnItems[itemIndex].damages.splice(dmgIndex, 1);
                    this.recalcSummary();
                },

                setDamageDetails(damageObj, causeText, estimatedPrice) {
                    damageObj.cause = causeText;
                    damageObj.fine = estimatedPrice;
                    this.recalcSummary();
                },

                getDamagedQty(item) {
                    return item.damages.reduce((sum, d) => sum + parseInt(d.qty || 0), 0);
                },

                calculateOverdue() {
                    if (!this.currentRental.return_date) return;
                    const returnDate = new Date(this.currentRental.return_date);
                    const today = new Date();
                    returnDate.setHours(0, 0, 0, 0);
                    today.setHours(0, 0, 0, 0);
                    const diffTime = today - returnDate;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    this.overdueDays = diffDays > 0 ? diffDays : 0;
                    this.overdueFine = this.overdueDays * 100;
                },

                recalcSummary() {
                    this.totalDamageFine = this.returnItems.reduce((total, item) => {
                        return total + item.damages.reduce((sum, d) => sum + parseFloat(d.fine || 0), 0);
                    }, 0);
                    this.grandTotal = this.remainingAmount + this.overdueFine + this.totalDamageFine;
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('th-TH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(num);
                },

                confirmReturn() {
                    // ใช้ SweetAlert2 Confirmation (Optional: ถ้าอยากใช้ SweetAlert แทน Dialog ปกติ)
                    /*
                    Swal.fire({
                        title: 'ยืนยันการคืนชุด?',
                        text: `ยอดสุทธิที่ต้องรับ: ${this.formatNumber(this.grandTotal)} บาท`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'ยืนยัน',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submitFinal();
                        }
                    });
                    */
                    // แต่ในที่นี้เราใช้ Modal ซ้อน Modal (Dialog) ที่ทำไว้แล้ว
                    this.showConfirmDialog = true;
                },

                async submitFinal() {
                    this.isSubmitting = true;

                    let payloadDamages = [];

                    this.returnItems.forEach(item => {
                        if (item.damages && item.damages.length > 0) {
                            item.damages.forEach(d => {
                                let finalNote = d.cause;
                                if (d.note) finalNote += ": " + d.note;

                                payloadDamages.push({
                                    item_id: item.item_id,
                                    is_accessory: item.is_accessory,
                                    qty: d.qty,
                                    fine: d.fine,
                                    note: finalNote
                                });
                            });
                        }
                    });

                    try {
                        const url = `{{ url('admin/reception/return') }}/${this.currentRental.rental_id}`;

                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                items_damage: payloadDamages,
                                overdue_fine: this.overdueFine,
                                payment_method: this.paymentMethod
                            })
                        });

                        const data = await res.json();

                        if (data.success) {
                            // ✅ ใช้ SweetAlert2 แจ้งเตือนสำเร็จ
                            Swal.fire({
                                icon: 'success',
                                title: 'เรียบร้อย!',
                                text: 'บันทึกการคืนและรับชำระเงินสำเร็จ',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: data.message
                            });
                            this.isSubmitting = false;
                            this.showConfirmDialog = false;
                        }
                    } catch (e) {
                        console.error("Error:", e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                        });
                        this.isSubmitting = false;
                        this.showConfirmDialog = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
