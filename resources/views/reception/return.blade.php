<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            รับคืนชุด & ตรวจสอบความเสียหาย
        </h2>
    </x-slot>

    <div class="py-12" x-data="returnSystem()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Search Box --}}
            <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm flex gap-2">
                <form action="{{ route('reception.return') }}" method="GET" class="flex-grow flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="ค้นหาเลขที่บิล (Rental ID), ชื่อลูกค้า..."
                        class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        ค้นหา
                    </button>
                </form>
            </div>

            {{-- Table รายการที่รอคืน --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($rentals->isEmpty())
                    <p class="text-center text-gray-500 py-10">-- ไม่พบรายการที่ต้องคืน --</p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">ลูกค้า</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">กำหนดคืน</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">สถานะเวลา</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                @foreach($rentals as $rental)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4">#{{ $rental->rental_id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold">{{ $rental->member ? $rental->member->first_name : 'Guest' }}</div>
                                        <div class="text-xs text-gray-500">{{ $rental->member ? $rental->member->tel : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($rental->return_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                        $diff = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($rental->return_date), false);
                                        @endphp
                                        @if($diff < 0)
                                            <span class="px-2 py-1 text-xs font-bold text-red-700 bg-red-100 rounded-full">
                                            เกินกำหนด {{ abs(intval($diff)) }} วัน
                                            </span>
                                            @else
                                            <span class="px-2 py-1 text-xs font-bold text-green-700 bg-green-100 rounded-full">
                                                ปกติ
                                            </span>
                                            @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{-- ปุ่มเปิด Modal --}}
                                        <button @click="openModal({{ Js::from($rental) }})"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-lg shadow transition text-sm flex items-center justify-center mx-auto gap-2">
                                            <i data-lucide="search-check" class="w-4 h-4"></i> ตรวจสอบ & คืน
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $rentals->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ▼▼▼ MODAL ตรวจสอบรายการคืน ▼▼▼ --}}
        <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeModal"></div>

                <div class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl relative z-50">

                    {{-- Header --}}
                    <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4 -mx-6 -mt-6 mb-6 flex justify-between items-center border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="clipboard-check" class="w-6 h-6 text-blue-600"></i>
                            ตรวจสอบรายการคืน (Bill #<span x-text="currentRental?.rental_id"></span>)
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-500"><i data-lucide="x" class="w-6 h-6"></i></button>
                    </div>

                    {{-- Body --}}
                    <div class="space-y-8">

                        {{-- 1. ตารางสินค้า --}}
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300">รายการสินค้า:</h4>
                                <span class="text-xs text-gray-500">* กรุณาตรวจสอบและเพิ่มรายการความเสียหายตามจริง</span>
                            </div>

                            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase w-1/4">สินค้า</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-24">ยืมไป</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">รายงานความเสียหาย</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                        <template x-for="(item, index) in returnItems" :key="index">
                                            <tr class="align-top hover:bg-gray-50 transition">
                                                {{-- สินค้า --}}
                                                <td class="px-4 py-4">
                                                    <div class="font-bold text-gray-800 dark:text-gray-200 text-base" x-text="item.item_name"></div>
                                                    <div class="text-sm text-gray-500" x-text="item.is_accessory ? 'อุปกรณ์เสริม' : 'ชุดหลัก'"></div>
                                                </td>

                                                {{-- จำนวน --}}
                                                <td class="px-4 py-4 text-center">
                                                    <span class="px-3 py-1 bg-gray-100 rounded-lg font-mono text-lg font-bold text-gray-700" x-text="item.rented_qty"></span>
                                                </td>

                                                {{-- พื้นที่จัดการความเสียหาย --}}
                                                <td class="px-4 py-4 bg-gray-50/50 dark:bg-gray-700/20">
                                                    <div class="space-y-3">

                                                        {{-- Loop รายการเสียหายย่อย --}}
                                                        <template x-for="(dmg, dmgIndex) in item.damages" :key="dmgIndex">
                                                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-red-200 shadow-sm relative group">

                                                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                                                    {{-- จำนวนที่เสีย --}}
                                                                    <div class="md:col-span-2">
                                                                        <label class="text-[10px] font-bold text-gray-500 uppercase mb-1 block">จำนวน (ชิ้น)</label>
                                                                        <input type="number" x-model="dmg.qty" min="1" :max="item.rented_qty" @change="recalcSummary()" class="w-full text-sm border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 text-center font-bold">
                                                                    </div>

                                                                    {{-- เช็คลิสต์สาเหตุ (Checklist) --}}
                                                                    <div class="md:col-span-6">
                                                                        <label class="text-[10px] font-bold text-gray-500 uppercase mb-1 block">สาเหตุความเสียหาย</label>
                                                                        <div class="flex flex-wrap gap-2 mb-2">
                                                                            {{-- ขาด --}}
                                                                            <label class="cursor-pointer">
                                                                                <input type="radio" :name="'cause_'+index+'_'+dmgIndex" value="ขาด" class="peer sr-only" @change="setDamageDetails(dmg, 'ขาด', 500)">
                                                                                <div class="px-3 py-1.5 rounded-md border text-xs font-medium transition-all peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-300 hover:bg-gray-50 bg-white text-gray-600">
                                                                                    ขาด <span class="opacity-70">(500฿)</span>
                                                                                </div>
                                                                            </label>
                                                                            {{-- เปื้อน --}}
                                                                            <label class="cursor-pointer">
                                                                                <input type="radio" :name="'cause_'+index+'_'+dmgIndex" value="เปื้อน" class="peer sr-only" @change="setDamageDetails(dmg, 'เปื้อน', 200)">
                                                                                <div class="px-3 py-1.5 rounded-md border text-xs font-medium transition-all peer-checked:bg-yellow-100 peer-checked:text-yellow-700 peer-checked:border-yellow-300 hover:bg-gray-50 bg-white text-gray-600">
                                                                                    เปื้อน <span class="opacity-70">(200฿)</span>
                                                                                </div>
                                                                            </label>
                                                                            {{-- อื่นๆ --}}
                                                                            <label class="cursor-pointer">
                                                                                <input type="radio" :name="'cause_'+index+'_'+dmgIndex" value="อื่นๆ" class="peer sr-only" @change="setDamageDetails(dmg, 'อื่นๆ', 0)">
                                                                                <div class="px-3 py-1.5 rounded-md border text-xs font-medium transition-all peer-checked:bg-gray-200 peer-checked:text-gray-800 peer-checked:border-gray-400 hover:bg-gray-50 bg-white text-gray-600">
                                                                                    อื่นๆ <span class="opacity-70">(ประเมิน)</span>
                                                                                </div>
                                                                            </label>
                                                                            {{-- หลายรายการ (ไม่มีวงเล็บราคา) --}}
                                                                            <label class="cursor-pointer">
                                                                                <input type="radio" :name="'cause_'+index+'_'+dmgIndex" value="หลายรายการ" class="peer sr-only" @change="setDamageDetails(dmg, 'หลายรายการ', 0)">
                                                                                <div class="px-3 py-1.5 rounded-md border text-xs font-medium transition-all peer-checked:bg-purple-100 peer-checked:text-purple-700 peer-checked:border-purple-300 hover:bg-gray-50 bg-white text-gray-600 border-purple-200">
                                                                                    หลายรายการ
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                        <input type="text" x-model="dmg.note" placeholder="ระบุรายละเอียดเพิ่มเติม..." class="w-full text-xs border-gray-300 rounded-md placeholder-gray-400">
                                                                    </div>

                                                                    {{-- ค่าปรับ --}}
                                                                    <div class="md:col-span-3">
                                                                        <label class="text-[10px] font-bold text-red-600 uppercase mb-1 block">ค่าปรับ (บาท)</label>
                                                                        <div class="relative">
                                                                            <input type="number" x-model="dmg.fine" min="0" @change="recalcSummary()" class="w-full text-sm border-red-300 rounded-md focus:ring-red-500 focus:border-red-500 text-right font-bold text-red-700 bg-red-50 pr-8">
                                                                            <span class="absolute right-3 top-1.5 text-xs text-red-400">฿</span>
                                                                        </div>
                                                                    </div>

                                                                    {{-- ปุ่มลบ --}}
                                                                    <div class="md:col-span-1 flex justify-center pt-5">
                                                                        <button @click="removeDamage(index, dmgIndex)" class="text-gray-400 hover:text-red-500 transition tooltip" title="ลบรายการ">
                                                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        {{-- ปุ่มเพิ่ม --}}
                                                        <div class="flex justify-between items-center pt-2">
                                                            <div class="text-sm font-medium text-gray-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">
                                                                <span class="w-2 h-2 bg-green-500 rounded-full inline-block mr-1"></span>
                                                                สภาพดี: <span x-text="item.rented_qty - getDamagedQty(item)" class="font-bold text-green-700"></span> ชิ้น
                                                            </div>
                                                            <button @click="addDamage(index)"
                                                                :disabled="getDamagedQty(item) >= item.rented_qty"
                                                                class="text-xs font-bold flex items-center gap-1 bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50 hover:text-red-600 hover:border-red-300 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                                                <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มรายการเสียหาย
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 2. สรุปยอดเงิน --}}
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                {{-- ช่องทางชำระ --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">ช่องทางการรับชำระเงิน</label>
                                    <select x-model="paymentMethod" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="cash">💵 เงินสด (Cash)</option>
                                        <option value="transfer">🏦 โอนเงิน (Transfer)</option>
                                        <option value="credit_card">💳 บัตรเครดิต (Credit Card)</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-2">* ยอดรับชำระรวมค่าปรับและยอดค้างชำระแล้ว</p>
                                </div>

                                {{-- ตัวเลข --}}
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">ค่าเช่าคงเหลือ (Remaining):</span>
                                        <span class="font-mono font-bold text-gray-800" x-text="formatNumber(remainingAmount)"></span>
                                    </div>
                                    <div class="flex justify-between text-sm text-red-600">
                                        <span class="flex items-center gap-1"><i data-lucide="clock" class="w-4 h-4"></i> ปรับคืนช้า (<span x-text="overdueDays"></span> วัน):</span>
                                        <span class="font-mono font-bold" x-text="'+ ' + formatNumber(overdueFine)"></span>
                                    </div>
                                    <div class="flex justify-between text-sm text-red-600">
                                        <span class="flex items-center gap-1"><i data-lucide="alert-triangle" class="w-4 h-4"></i> ปรับของเสียหาย:</span>
                                        <span class="font-mono font-bold" x-text="'+ ' + formatNumber(totalDamageFine)"></span>
                                    </div>
                                    <div class="border-t border-gray-300 pt-3 flex justify-between items-center">
                                        <span class="text-base font-bold text-gray-800">ยอดสุทธิที่ต้องรับ:</span>
                                        <span class="text-2xl font-extrabold text-blue-700" x-text="formatNumber(grandTotal)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex justify-end gap-3 border-t pt-6">
                            <button @click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">ยกเลิก</button>
                            <button @click="submitReturn" :disabled="isSubmitting" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg disabled:opacity-50 flex items-center gap-2 transition transform hover:-translate-y-0.5">
                                <span x-show="isSubmitting" class="animate-spin">⏳</span>
                                <span>ยืนยันการคืน & รับเงิน</span>
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

                        // คำนวณยอดค้าง
                        let totalPaid = 0;
                        if (rental.payments && rental.payments.length > 0) {
                            totalPaid = rental.payments.filter(p => p.status === 'paid').reduce((sum, p) => sum + parseFloat(p.amount), 0);
                        }
                        this.remainingAmount = Math.max(0, parseFloat(rental.total_amount) - totalPaid);

                        // แปลงข้อมูลสินค้า
                        this.returnItems = rental.items.map(item => ({
                            item_id: item.item_id,
                            item_name: item.item ? item.item.item_name : 'Unknown',
                            rented_qty: item.quantity,
                            is_accessory: false,
                            damages: []
                        }));

                        // (ถ้ามี Accessories เพิ่ม logic ตรงนี้ได้)

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
                                cause: '', // เก็บค่า radio
                                note: '', // รายละเอียดเพิ่มเติม
                                fine: 0 // ค่าปรับ
                            });
                            this.recalcSummary();
                        }
                    },

                    removeDamage(itemIndex, dmgIndex) {
                        this.returnItems[itemIndex].damages.splice(dmgIndex, 1);
                        this.recalcSummary();
                    },

                    // ฟังก์ชันตั้งค่าเมื่อเลือก Radio
                    setDamageDetails(damageObj, causeText, estimatedPrice) {
                        damageObj.cause = causeText;
                        damageObj.fine = estimatedPrice; // ใส่ราคาประเมิน (0 ถ้าเป็น อื่นๆ หรือ หลายรายการ)
                        this.recalcSummary();
                    },

                    getDamagedQty(item) {
                        return item.damages.reduce((sum, d) => sum + parseInt(d.qty || 0), 0);
                    },

                    calculateOverdue() {
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

                    async submitReturn() {
                        if (!confirm(`ยอดรับชำระสุทธิ ${this.formatNumber(this.grandTotal)} บาท\nยืนยันการคืน?`)) return;

                        this.isSubmitting = true;

                        // เตรียมข้อมูล Flatten ส่ง Backend
                        let payloadDamages = [];
                        this.returnItems.forEach(item => {
                            item.damages.forEach(d => {
                                // รวม cause + note เข้าด้วยกันเพื่อบันทึกลง DB (description)
                                let finalNote = d.cause;
                                if (d.note) finalNote += ": " + d.note;

                                payloadDamages.push({
                                    item_id: item.item_id,
                                    qty: d.qty,
                                    fine: d.fine,
                                    note: finalNote
                                });
                            });
                        });

                        try {
                            const res = await fetch(`/reception/return/${this.currentRental.rental_id}`, {
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
                                alert('คืนชุดเรียบร้อย!');
                                window.location.reload();
                            } else {
                                alert('เกิดข้อผิดพลาด: ' + data.message);
                            }
                        } catch (e) {
                            console.error(e);
                            alert('Connection Error');
                        } finally {
                            this.isSubmitting = false;
                        }
                    }
                };
            }
        </script>
</x-app-layout>