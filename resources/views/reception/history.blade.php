<x-app-layout>
    {{-- CSS สำหรับการพิมพ์ใบเสร็จ --}}
    <style>
        @media print {

            /* ซ่อนทุกอย่างในหน้าเว็บ */
            body * {
                visibility: hidden;
            }

            /* โชว์เฉพาะ Modal ใบเสร็จ และลูกๆ ของมัน */
            #receipt-modal,
            #receipt-modal * {
                visibility: visible;
            }

            /* จัดตำแหน่งให้ใบเสร็จอยู่ตรงกลางกระดาษ */
            #receipt-modal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                background: white;
                box-shadow: none !important;
                /* ลบเงา */
            }

            /* ซ่อนปุ่มกดและ Element ที่ไม่ต้องการ */
            button,
            .no-print {
                display: none !important;
            }
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ประวัติการเช่า (Rental History)
        </h2>
    </x-slot>

    {{-- เปิด x-data ตรงนี้ เพื่อให้คลุมทั้งตารางและ Modal --}}
    <div class="py-12" x-data="{ showModal: false, selectedRental: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filters --}}
            <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm no-print">
                <form action="{{ route('reception.history') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-grow">
                        <label class="text-sm text-gray-500 dark:text-gray-400">ค้นหา</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ID หรือ ชื่อลูกค้า" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm h-10">
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">สถานะ</label>
                        <select name="status" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm h-10">
                            <option value="all">ทั้งหมด</option>
                            <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>กำลังเช่า (Rented)</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>คืนแล้ว (Returned)</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 h-10">
                        กรองข้อมูล
                    </button>
                </form>
            </div>

            {{-- Main Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg no-print">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ลูกค้า</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">วันที่เช่า</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ยอดรวม</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">สถานะ</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                                @foreach($rentals as $rental)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4">#{{ $rental->rental_id }}</td>
                                    <td class="px-6 py-4">
                                        {{ $rental->member ? $rental->member->first_name . ' ' . $rental->member->last_name : 'Guest' }}
                                        <div class="text-xs text-gray-500">{{ $rental->member ? $rental->member->tel : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($rental->rental_date)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($rental->total_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($rental->status == 'rented')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">กำลังเช่า</span>
                                        @elseif($rental->status == 'returned')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">คืนแล้ว</span>
                                        @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $rental->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{-- ปุ่มดูรายละเอียด --}}
                                        <button @click="showModal = true; selectedRental = {{ Js::from($rental) }}"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium flex items-center justify-center gap-1 mx-auto">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
                                                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                                                <path d="M12 17V7" />
                                            </svg>
                                            ดูใบเสร็จ
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
                </div>
            </div>
        </div>

        {{-- 🟢 MODAL ใบเสร็จย้อนหลัง --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-70" @click="showModal = false"></div>

                {{-- กล่องใบเสร็จ (ต้องมี id="receipt-modal") --}}
                <div id="receipt-modal" class="inline-block w-full max-w-sm overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-sm relative z-[60] my-8 font-mono text-sm">

                    {{-- Header --}}
                    <div class="bg-gray-800 text-white p-6 text-center relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
                        <div class="absolute top-4 right-4 cursor-pointer text-gray-400 hover:text-white no-print" @click="showModal = false">✕</div>
                        <h2 class="text-2xl font-bold tracking-wider uppercase mb-1">ใบเสร็จย้อนหลัง</h2>
                        <p class="text-gray-400 text-xs tracking-widest">COPY RECEIPT</p>
                        <div class="mt-4 border-t border-gray-600 pt-4">
                            <h3 class="text-lg font-semibold text-white">ร้านเช่าชุด Watakacha</h3>
                            <p class="text-gray-400 text-xs">สาขาลำพูน โทร. 081-234-5678</p>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 bg-white relative">
                        {{-- รอยหยัก --}}
                        <div class="absolute top-0 left-0 w-full -mt-2 h-4 bg-repeat-x text-white" style="background-image: radial-gradient(circle, transparent 25%, currentColor 26%); background-size: 10px 10px; height: 5px;"></div>

                        {{-- 1. ข้อมูลทั่วไป (Info) --}}
                        <div class="space-y-2 mb-4 border-b border-dashed border-gray-300 pb-4">
                            <div class="flex justify-between">
                                <span class="text-gray-500">เลขที่รายการ:</span>
                                <span class="font-bold text-gray-800">#<span x-text="selectedRental?.rental_id"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">วันที่เช่า:</span>
                                <span class="text-gray-800" x-text="selectedRental ? new Date(selectedRental.rental_date).toLocaleDateString('th-TH') : ''"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">กำหนดคืน:</span>
                                <span class="text-gray-800" x-text="selectedRental ? new Date(selectedRental.return_date).toLocaleDateString('th-TH') : ''"></span>
                            </div>
                            {{-- ส่วนแสดงข้อมูลลูกค้า (แก้ไขใหม่) --}}
                            <div class="flex justify-between items-start">
                                <span class="text-gray-500 shrink-0">ลูกค้า:</span>
                                <div class="text-right">
                                    {{-- กรณี 1: สมาชิก --}}
                                    <template x-if="selectedRental?.member">
                                        <div>
                                            <span class="font-bold text-gray-800 block" x-text="selectedRental.member.first_name + ' ' + selectedRental.member.last_name"></span>
                                            <span class="text-gray-500 text-[10px] block" x-text="'Tel: ' + selectedRental.member.tel"></span>
                                        </div>
                                    </template>

                                    {{-- กรณี 2: Guest (ดึงจาก Description ที่บันทึกไว้) --}}
                                    <template x-if="!selectedRental?.member">
                                        <div>
                                            <span class="font-bold text-gray-800 block">ลูกค้าทั่วไป (Guest)</span>
                                            {{-- โชว์ข้อความ "คุณ... โทร..." ที่บันทึกไว้ใน description --}}
                                            <span class="text-gray-600 text-[10px] block" x-text="selectedRental?.description || '-'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">พนักงานขาย:</span>
                                {{-- ชื่อพนักงาน --}}
                                <span class="text-gray-800" x-text="selectedRental?.user?.first_name || selectedRental?.user?.last_name || '-'"></span>
                            </div>
                        </div>

                        {{-- 2. ตารางรายการสินค้า (Items & Accessories) --}}
                        <div class="mb-4 border-b border-dashed border-gray-300 pb-4">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="text-gray-500 border-b border-gray-100">
                                        <th class="text-left py-1">รายการ</th>
                                        <th class="text-center py-1">จำนวน</th>
                                        <th class="text-right py-1">ราคา</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    {{-- สินค้าหลัก --}}
                                    <template x-if="selectedRental?.items">
                                        <template x-for="rItem in selectedRental.items" :key="'hi-'+rItem.id">
                                            <tr>
                                                <td class="py-1">
                                                    <span x-text="rItem.item ? rItem.item.item_name : 'สินค้า #' + rItem.item_id"></span>
                                                </td>
                                                <td class="text-center py-1" x-text="rItem.quantity"></td>
                                                <td class="text-right py-1" x-text="new Intl.NumberFormat().format(rItem.price * rItem.quantity)"></td>
                                            </tr>
                                        </template>
                                    </template>

                                    {{-- อุปกรณ์เสริม (ใช้ pivot) --}}
                                    <template x-if="selectedRental?.accessories">
                                        <template x-for="rAcc in selectedRental.accessories" :key="'ha-'+rAcc.id">
                                            <tr>
                                                <td class="py-1">
                                                    <span x-text="(rAcc.name || 'Acc #' + rAcc.id) + ' (Acc)'"></span>
                                                </td>
                                                {{-- ต้องใช้ pivot เพราะเป็น belongsToMany --}}
                                                <td class="text-center py-1" x-text="rAcc.pivot.quantity"></td>
                                                <td class="text-right py-1" x-text="new Intl.NumberFormat().format(rAcc.pivot.price * rAcc.pivot.quantity)"></td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- 3. สถานะปัจจุบัน --}}
                        <div class="mb-4 text-center">
                            <span class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wide border"
                                :class="{
                                      'bg-yellow-100 text-yellow-800 border-yellow-200': selectedRental?.status === 'rented',
                                      'bg-green-100 text-green-800 border-green-200': selectedRental?.status === 'returned',
                                      'bg-gray-100 text-gray-800 border-gray-200': selectedRental?.status === 'cancelled'
                                  }"
                                x-text="selectedRental?.status"></span>
                        </div>

                        {{-- 4. ยอดเงิน --}}
                        <div class="space-y-2 text-xs mb-6 bg-gray-50 p-4 rounded border border-gray-100">
                            <div class="flex justify-between text-lg font-bold text-gray-900">
                                <span>ยอดรวมทั้งสิ้น</span>
                                <span x-text="selectedRental ? new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(selectedRental.total_amount) : 0"></span>
                            </div>

                            {{-- Loop ประวัติการจ่ายเงิน --}}
                            <div class="pt-2 mt-2 border-t border-gray-200">
                                <p class="font-bold text-gray-500 mb-1">ประวัติการชำระ:</p>
                                <template x-if="selectedRental?.payments && selectedRental.payments.length > 0">
                                    <template x-for="pay in selectedRental.payments" :key="pay.payment_id">
                                        <div class="flex justify-between text-gray-600 mb-1">
                                            <span>
                                                <span class="text-[10px] bg-gray-200 px-1 rounded mr-1 uppercase" x-text="pay.type"></span>
                                                <span x-text="new Date(pay.payment_date).toLocaleDateString('th-TH')"></span>
                                            </span>
                                            <span class="font-medium" x-text="new Intl.NumberFormat().format(pay.amount)"></span>
                                        </div>
                                    </template>
                                </template>
                                <template x-if="!selectedRental?.payments || selectedRental.payments.length === 0">
                                    <p class="text-gray-400 italic text-xs">- ไม่พบข้อมูลการชำระเงิน -</p>
                                </template>
                            </div>
                        </div>

                        {{-- 5. QR Code --}}
                        <div class="text-center border-t border-dashed border-gray-300 pt-6">
                            <div class="flex justify-center mb-4">
                                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent('RentalID:' + selectedRental?.rental_id)}`"
                                    alt="QR Code" class="w-24 h-24 mix-blend-multiply opacity-80">
                            </div>
                            <p class="font-bold text-gray-800 text-xs">OFFICIAL RECEIPT</p>
                        </div>
                    </div>

                    <div class="bg-gray-100 p-4 border-t border-gray-200 no-print">
                        <button @click="window.print()" class="w-full py-2 bg-white hover:bg-gray-50 text-gray-800 font-bold rounded border border-gray-300 shadow-sm transition text-xs uppercase flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            พิมพ์หน้านี้
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>