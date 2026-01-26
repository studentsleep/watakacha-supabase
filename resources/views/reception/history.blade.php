<x-app-layout>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #receipt-modal,
            #receipt-modal * {
                visibility: visible;
            }

            #receipt-modal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                background: white;
                box-shadow: none !important;
                border: none !important;
            }

            .no-print,
            button {
                display: none !important;
            }

            /* ซ่อนเงาและพื้นหลังตอนพิมพ์ */
            .shadow-2xl,
            .bg-gray-800 {
                box-shadow: none !important;
            }
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
            </div>
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ประวัติการเช่า (Rental History)
            </h2>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showModal: false, selectedRental: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🔍 Filter Bar --}}
            <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 no-print">
                <form action="{{ route('reception.history') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:flex-grow">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">ค้นหาข้อมูล</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="เลขที่ใบเสร็จ, ชื่อลูกค้า, หรือเบอร์โทร..."
                                class="w-full pl-10 rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-11 transition-all">
                        </div>
                    </div>
                    <div class="w-full md:w-48">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">สถานะ</label>
                        <select name="status" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm h-11 focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer">
                            <option value="all">ทั้งหมด</option>
                            <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>⏳ กำลังเช่า</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>✅ คืนแล้ว</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full md:w-auto px-6 h-11 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                        </svg>
                        กรองข้อมูล
                    </button>
                    @if(request()->has('search') || request()->has('status'))
                    <a href="{{ route('reception.history') }}" class="w-full md:w-auto px-4 h-11 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition flex items-center justify-center">ล้างค่า</a>
                    @endif
                </form>
            </div>

            {{-- 📋 Data Table --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden no-print">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reference ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ลูกค้า</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">วันที่ทำรายการ</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">ยอดสุทธิ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach($rentals as $rental)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150 group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">#{{ $rental->rental_id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center text-xs font-bold">
                                            {{ mb_substr($rental->member ? $rental->member->first_name : ($rental->description ? 'G' : '?'), 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $rental->member ? $rental->member->first_name . ' ' . $rental->member->last_name : 'Guest Customer' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $rental->member ? $rental->member->tel : ($rental->description ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex flex-col">
                                        <span class="font-medium">
                                            {{ \Carbon\Carbon::parse($rental->rental_date)->addYears(543)->locale('th')->translatedFormat('d M Y') }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            คืน: {{ \Carbon\Carbon::parse($rental->return_date)->addYears(543)->locale('th')->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-white">
                                    ฿{{ number_format($rental->total_amount, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($rental->status == 'rented')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5 animate-pulse"></span> กำลังเช่า
                                    </span>
                                    @elseif($rental->status == 'returned')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg> คืนแล้ว
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $rental->status }}
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button @click="showModal = true; selectedRental = {{ Js::from($rental) }}"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        ใบเสร็จ
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($rentals->hasPages())
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $rentals->links() }}
                </div>
                @endif
            </div>

        </div>

        {{-- 🧾 RECEIPT MODAL (ดีไซน์ใหม่) --}}
        <div x-show="showModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div id="receipt-modal" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">

                    {{-- 🟢 ส่วนหัวใบเสร็จ --}}
                    <div class="bg-gray-900 text-white p-6 relative">
                        <div class="absolute top-4 right-4 cursor-pointer opacity-70 hover:opacity-100 no-print" @click="showModal = false">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-white tracking-wide">ใบเสร็จรับเงิน</h3>
                                <p class="text-xs text-gray-400 uppercase tracking-widest mt-1">RECEIPT / TAX INVOICE</p>
                            </div>
                            <div class="text-right">
                                <div class="bg-white/10 px-2 py-1 rounded text-xs font-mono">
                                    #<span x-text="selectedRental?.rental_id"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 border-t border-gray-700 pt-4 flex justify-between items-end">
                            <div>
                                <h4 class="font-bold text-sm">Watakacha Wedding & Studio</h4>
                                <p class="text-xs text-gray-400 mt-0.5">โทร. 093-130-9899</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">วันที่ทำรายการ</p>
                                <p class="text-sm font-medium" x-text="selectedRental ? new Date(selectedRental.created_at).toLocaleDateString('th-TH') : '-'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- 🟢 เนื้อหาใบเสร็จ --}}
                    <div class="p-6 bg-white relative">
                        {{-- ลายน้ำ (ถ้าต้องการ) --}}
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-[0.3] pointer-events-none">
                            <img src="{{ asset('images/logo.png') }}" class="w-80 h-80 object-contain grayscale">
                        </div>

                        {{-- ข้อมูลลูกค้า --}}
                        <div class="flex justify-between mb-6 pb-4 border-b border-gray-100">
                            <div class="text-sm">
                                <p class="text-xs text-gray-500 font-bold uppercase mb-1">ลูกค้า (Customer)</p>
                                <template x-if="selectedRental?.member">
                                    <div>
                                        <p class="font-bold text-gray-800" x-text="selectedRental.member.first_name + ' ' + selectedRental.member.last_name"></p>
                                        <p class="text-gray-500 text-xs mt-0.5" x-text="selectedRental.member.tel"></p>
                                    </div>
                                </template>
                                <template x-if="!selectedRental?.member">
                                    <div>
                                        <p class="font-bold text-gray-800">ลูกค้าทั่วไป</p>
                                        <p class="text-gray-500 text-xs mt-0.5" x-text="selectedRental?.description || '-'"></p>
                                    </div>
                                </template>
                            </div>
                            <div class="text-right text-sm">
                                <p class="text-xs text-gray-500 font-bold uppercase mb-1">ระยะเวลาเช่า</p>
                                <p class="text-gray-800"><span x-text="selectedRental ? new Date(selectedRental.rental_date).toLocaleDateString('th-TH', {day:'numeric', month:'short'}) : ''"></span> - <span x-text="selectedRental ? new Date(selectedRental.return_date).toLocaleDateString('th-TH', {day:'numeric', month:'short', year:'2-digit'}) : ''"></span></p>
                                <p class="text-xs text-indigo-600 font-medium mt-0.5">7 วัน</p>
                            </div>
                        </div>

                        {{-- ตารางรายการ --}}
                        <div class="mb-6">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-gray-500 border-b border-gray-200">
                                        <th class="text-left py-2 font-medium text-xs uppercase">รายการ</th>
                                        <th class="text-center py-2 font-medium text-xs uppercase w-12">จำนวน</th>
                                        <th class="text-right py-2 font-medium text-xs uppercase w-20">รวม</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    <template x-for="item in selectedRental?.items || []" :key="'i-'+item.id">
                                        <tr class="border-b border-gray-50">
                                            <td class="py-2 pr-2">
                                                <div class="font-medium" x-text="item.item?.item_name || 'Item #'+item.item_id"></div>
                                                <div class="text-[10px] text-gray-400">รหัส: <span x-text="item.item_id"></span></div>
                                            </td>
                                            <td class="text-center py-2 align-top" x-text="item.quantity"></td>
                                            <td class="text-right py-2 align-top font-medium" x-text="new Intl.NumberFormat().format(item.price * item.quantity)"></td>
                                        </tr>
                                    </template>
                                    <template x-for="acc in selectedRental?.accessories || []" :key="'a-'+acc.id">
                                        <tr class="border-b border-gray-50 bg-gray-50/50">
                                            <td class="py-2 pr-2 pl-2">
                                                <div class="font-medium text-gray-600" x-text="(acc.name || 'Acc #'+acc.id)"></div>
                                                <div class="text-[10px] text-orange-400">อุปกรณ์เสริม</div>
                                            </td>
                                            <td class="text-center py-2 align-top" x-text="acc.pivot.quantity"></td>
                                            <td class="text-right py-2 align-top text-gray-600" x-text="new Intl.NumberFormat().format(acc.pivot.price * acc.pivot.quantity)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- สรุปยอดเงิน --}}
                        <div class="flex justify-end mb-6">
                            <div class="w-1/2 space-y-2">
                                {{-- <div class="flex justify-between text-xs text-gray-500">
                                    <span>รวมเป็นเงิน</span>
                                    <span>0.00</span>
                                </div> --}}
                                <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-200">
                                    <span>ยอดสุทธิ</span>
                                    <span x-text="selectedRental ? new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(selectedRental.total_amount) : 0"></span>
                                </div>
                                <div class="flex justify-between items-center bg-green-50 p-2 rounded text-xs text-green-700 border border-green-100">
                                    <span>ชำระแล้ว (มัดจำ)</span>
                                    <span class="font-bold" x-text="selectedRental?.payments?.[0] ? new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(selectedRental.payments[0].amount) : '0.00'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="text-center pt-4 border-t border-dashed border-gray-200">
                            <div class="flex justify-center mb-3">
                                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${encodeURIComponent('RentalID:' + selectedRental?.rental_id)}`"
                                    alt="QR Code" class="w-16 h-16 mix-blend-multiply opacity-80 border p-1 rounded bg-white">
                            </div>
                            <p class="font-bold text-gray-800 text-xs">ขอบคุณที่ใช้บริการ</p>
                            <p class="text-[10px] text-gray-400 mt-1">เอกสารนี้ออกโดยระบบอัตโนมัติ</p>
                        </div>
                    </div>

                    {{-- ปุ่มกด (ไม่พิมพ์) --}}
                    <div class="bg-gray-50 px-6 py-4 flex gap-3 no-print border-t border-gray-100">
                        <button @click="window.print()" class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg shadow-sm hover:bg-gray-50 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            พิมพ์ใบเสร็จ
                        </button>
                        <button @click="showModal = false" class="flex-1 py-2.5 bg-gray-900 text-white font-bold rounded-lg shadow hover:bg-black transition">
                            ปิดหน้าต่าง
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>