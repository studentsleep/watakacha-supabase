<x-app-layout>
    {{-- ✅ SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Print Styles */
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

            .shadow-2xl,
            .bg-gray-800 {
                box-shadow: none !important;
            }

            /* -webkit-print-color-adjust: exact;
            print-color-adjust: exact; */
        }

        /* Custom Scrollbar */
        .modal-body-scroll {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>

    {{-- Header --}}
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-100 rounded-xl text-indigo-600 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                    <path d="M12 18V6" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    จัดการต้นทุนบริการ (Service Costs)
                </h2>
                <p class="text-xs text-gray-500">บันทึกค่าใช้จ่ายจริงสำหรับช่างแต่งหน้าและช่างภาพ</p>
            </div>
        </div>
    </x-slot>

    {{-- Main Content --}}
    <div class="py-8" x-data="serviceCostSystem()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🟢 Tabs Navigation --}}
            <div class="flex p-1 mb-6 bg-gray-100 rounded-2xl shadow-inner w-fit">
                <button @click="activeTab = 'pending'"
                    :class="activeTab === 'pending' ? 'bg-white text-indigo-700 shadow-md ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'"
                    class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    รอลงบันทึก
                    @if($pending->count() > 0)
                    <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full shadow-sm ml-1">{{ $pending->count() }}</span>
                    @endif
                </button>

                <button @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'bg-white text-green-600 shadow-md ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'"
                    class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    ประวัติการจ่าย
                </button>
            </div>

            {{-- 📦 Content Area --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 min-h-[400px]">

                {{-- Tab 1: Pending (รอลงบันทึก) --}}
                <div x-show="activeTab === 'pending'" x-transition:enter="transition ease-out duration-300">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">รหัสการเช่า</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">ลูกค้า (Customer)</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">วันที่คืน</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">รายการช่าง (Service)</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($pending as $item)
                                <tr class="hover:bg-indigo-50/30 transition group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded border border-indigo-100">#{{ $item->rental_id }}</span>
                                    </td>

                                    {{-- ลูกค้า --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-9 w-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                                                {{ mb_substr($item->member ? $item->member->first_name : ($item->description ? 'G' : '?'), 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-bold text-gray-100">{{ $item->member ? $item->member->first_name . ' ' . $item->member->last_name : 'ลูกค้าทั่วไป' }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->member ? $item->member->tel : ($item->description ?? '-') }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($item->return_date)->locale('th')->translatedFormat('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            @if($item->makeupArtist)
                                            <div class="flex items-center gap-2 text-sm text-gray-700 bg-pink-50 px-2 py-1 rounded-lg border border-pink-100 w-fit">
                                                <span class="text-pink-500">💄</span>
                                                <span class="font-bold">{{ $item->makeupArtist->first_name }}</span>
                                                <span class="text-xs text-gray-500">(ระบบ: {{ number_format($item->makeupArtist->price) }})</span>
                                            </div>
                                            @endif
                                            @if($item->photographer)
                                            <div class="flex items-center gap-2 text-sm text-gray-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-100 w-fit">
                                                <span class="text-blue-500">📷</span>
                                                <span class="font-bold">{{ $item->photographer->first_name }}</span>
                                                @if($item->photographerPackage)
                                                <span class="text-xs text-gray-500">
                                                    (ระบบ: {{ number_format($item->photographerPackage->price) }})
                                                </span>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button @click="openModal({{ Js::from($item) }})"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            บันทึกค่าใช้จ่าย
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                                        <div class="bg-gray-50 p-4 rounded-full inline-block mb-3"><svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg></div>
                                        <p>ไม่มีรายการค้างจัดการ</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab 2: History (ประวัติการจ่าย) --}}
                <div x-show="activeTab === 'history'" style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">รหัสการเช่า</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">ลูกค้า (Customer)</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">ค่าแต่งหน้า (จริง)</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">ค่าช่างภาพ (จริง)</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">รวมต้นทุน</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">วันที่บันทึก</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">จัดการ</th> {{-- ✅ เพิ่มคอลัมน์จัดการ --}}
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($history as $item)
                                <tr class="hover:bg-green-50/30 transition">
                                    {{-- รหัสการเช่า --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono text-sm font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">#{{ $item->rental_id }}</span>
                                    </td>

                                    {{-- ลูกค้า --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-9 w-9 rounded-full bg-gradient-to-tr from-green-500 to-teal-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                                                {{ mb_substr($item->member ? $item->member->first_name : 'G', 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-bold text-gray-100">{{ $item->member ? $item->member->first_name . ' ' . $item->member->last_name : 'ลูกค้าทั่วไป' }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->member ? $item->member->tel : '-' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        @if($item->makeup_cost > 0)
                                        <div class="inline-flex flex-col items-end">
                                            <span class="text-pink-600 font-bold">฿{{ number_format($item->makeup_cost, 0) }}</span>
                                            <span class="text-[10px] text-pink-600 bg-pink-100 px-1.5 py-0.5 rounded flex items-center gap-1 mt-0.5">
                                                💄 {{ $item->makeupArtist->first_name ?? 'ช่าง' }}
                                            </span>
                                        </div>
                                        @else
                                        <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($item->photographer_cost > 0)
                                        <div class="inline-flex flex-col items-end">
                                            <span class="text-blue-600 font-bold">฿{{ number_format($item->photographer_cost, 0) }}</span>
                                            <span class="text-[10px] text-blue-600 bg-blue-100 px-1.5 py-0.5 rounded flex items-center gap-1 mt-0.5">
                                                📷 {{ $item->photographer->first_name ?? 'ช่าง' }}
                                            </span>

                                        </div>
                                        @else
                                        <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-mono font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded border border-red-100">
                                            ฿{{ number_format($item->makeup_cost + $item->photographer_cost, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="text-xs text-gray-500 flex flex-col items-center">
                                            <span class="font-medium text-gray-700">{{ $item->updated_at->format('d M Y') }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $item->updated_at->format('H:i น.') }}</span>
                                        </div>
                                    </td>

                                    {{-- ✅ ปุ่มใบเสร็จ --}}
                                    <td class="px-6 py-4 text-center">
                                        <button @click="openReceiptModal({{ Js::from($item) }})" class="inline-flex items-center px-3 py-1.5 border border-gray-200 text-xs font-bold rounded-lg text-gray-600 bg-white hover:bg-gray-50 hover:text-indigo-600 transition shadow-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            ใบเสร็จ
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">ไม่มีประวัติรายการ</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($history->hasPages())
                    <div class="p-4 bg-gray-50 border-t border-gray-200">
                        {{ $history->links() }}
                    </div>
                    @endif
                </div>

            </div>

        </div>

        {{-- ✏️ Modal: บันทึกค่าใช้จ่าย --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showModal = false"></div>
                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl relative z-50">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="bg-indigo-100 p-1.5 rounded-lg text-indigo-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg></span>
                            บันทึกต้นทุนบริการ (Cost)
                        </h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>

                    <div class="space-y-5">
                        <template x-if="form.has_makeup">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">
                                    💄 ค่าจ้างช่างแต่งหน้า
                                    <span class="text-xs font-normal text-gray-400 ml-1">(แนะนำ: <span x-text="new Intl.NumberFormat().format(form.suggested_makeup)"></span>)</span>
                                </label>
                                <div class="relative">
                                    <input type="number" x-model="form.makeup_cost" class="w-full rounded-xl border-gray-300 pr-10 text-right font-bold text-gray-900 h-11 focus:ring-indigo-500 focus:border-indigo-500">
                                    <span class="absolute right-4 top-2.5 text-gray-400 font-bold">฿</span>
                                </div>
                            </div>
                        </template>

                        <template x-if="form.has_photographer">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">
                                    📷 ค่าจ้างช่างภาพ
                                    {{-- ✅ แสดงชื่อแพ็กเกจและราคาในระบบ --}}
                                    <span x-show="form.package_name" class="text-xs font-normal text-gray-400 ml-1">
                                        (<span x-text="form.package_name"></span>: <span x-text="new Intl.NumberFormat().format(form.suggested_photographer)"></span>)
                                    </span>
                                </label>
                                <div class="relative">
                                    <input type="number" x-model="form.photographer_cost" class="w-full rounded-xl border-gray-300 pr-10 text-right font-bold text-gray-900 h-11 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
                                    <span class="absolute right-4 top-2.5 text-gray-400 font-bold">฿</span>
                                </div>
                            </div>
                        </template>

                        <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100 flex gap-2">
                            <svg class="w-5 h-5 text-yellow-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs text-yellow-800">กรุณาระบุยอดเงินที่ทางร้าน <b>"จ่ายจริง"</b> ให้กับช่าง เพื่อใช้คำนวณกำไรสุทธิ</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button @click="showModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">ยกเลิก</button>
                        <button @click="submitCost" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">บันทึกข้อมูล</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🧾 RECEIPT MODAL --}}
        <div x-show="showReceipt" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showReceipt = false"></div>
                <div id="receipt-modal" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200">

                    {{-- Header --}}
                    <div class="bg-gray-900 text-white p-6 relative overflow-hidden">
                        <!-- <div class="absolute top-0 right-0 p-4 opacity-10"><svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            </svg></div> -->
                        <button @click="showReceipt=false" class="absolute top-4 right-4 text-white opacity-70 hover:opacity-100 no-print transition">✕</button>
                        <div class="relative z-10">
                            <center>
                                <h3 class="text-xl font-bold tracking-wide mb-1">ใบเสร็จรับเงิน (ต้นฉบับ)</h3>
                                <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em]">Rental Receipt</p>
                            </center>
                            <div class="mt-6 border-t border-gray-700 pt-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-sm text-white">Watakacha Wedding & Studio</h4>
                                        <p class="text-xs text-gray-400 mt-1">499/130 หมู่บ้านรุ่งเรือง<br>ซ. 8 อำเภอสันทราย เชียงใหม่ 50210</p>
                                        <p class="text-xs text-gray-400 mt-1">โทร. 082-280-6989</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray-400 uppercase mb-0.5">Reference No.</p>
                                        <div class="bg-white/10 px-2 py-1 rounded text-sm font-mono font-bold tracking-wider">#<span x-text="receiptData?.rental_id"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-8 bg-white relative">
                        {{-- Watermark --}}
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none">
                            <img src="{{ asset('images/logo.png') }}" class="w-64 h-64 object-contain grayscale">
                        </div>

                        <div class="flex justify-between mb-6 pb-4 border-b border-gray-100">
                            <div>
                                <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">ลูกค้า (Customer)</p>
                                <p class="font-bold text-gray-900 text-sm" x-text="receiptData?.member ? receiptData.member.first_name + ' ' + receiptData.member.last_name : 'ลูกค้าทั่วไป (Guest)'"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="receiptData?.member ? receiptData.member.tel : (receiptData?.description || '-')"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">วันที่เช่า (Rental Date)</p>
                                <p class="text-gray-900 text-sm font-medium" x-text="receiptData ? new Date(receiptData.rental_date).toLocaleDateString('th-TH') : '-'"></p>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="mb-6">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-gray-400 border-b border-gray-200">
                                        <th class="text-left py-2 font-medium text-[10px] uppercase">รายการ</th>
                                        <th class="text-right py-2 font-medium text-[10px] uppercase w-20">รวม</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    <template x-for="item in receiptData?.items || []">
                                        <tr class="border-b border-gray-50 last:border-0">
                                            <td class="py-2.5">
                                                <div class="font-bold text-xs text-gray-800" x-text="item.item?.item_name"></div>
                                                <div class="text-[10px] text-gray-400" x-text="'@' + formatNumber(item.price) + ' x ' + item.quantity"></div>
                                            </td>
                                            <td class="text-right py-2.5 font-medium" x-text="formatNumber(item.price * item.quantity)"></td>
                                        </tr>
                                    </template>
                                    <template x-for="acc in receiptData?.accessories || []">
                                        <tr class="border-b border-gray-50 last:border-0">
                                            <td class="py-2.5">
                                                <div class="font-medium text-xs text-gray-600" x-text="acc.name"></div>
                                                <div class="text-[10px] text-orange-400">อุปกรณ์เสริม x<span x-text="acc.pivot.quantity"></span></div>
                                            </td>
                                            <td class="text-right py-2.5 text-gray-500" x-text="formatNumber(acc.pivot.price * acc.pivot.quantity)"></td>
                                        </tr>
                                    </template>
                                    {{-- เพิ่มบริการเสริมในใบเสร็จ --}}
                                    <template x-if="receiptData?.makeup_artist">
                                        <tr class="border-b border-gray-50 last:border-0">
                                            <td class="py-2.5">
                                                <div class="font-medium text-xs text-gray-600">บริการแต่งหน้า</div>
                                                <div class="text-[10px] text-pink-400" x-text="'ช่าง: ' + receiptData.makeup_artist.first_name"></div>
                                            </td>
                                            <td class="text-right py-2.5 text-gray-500" x-text="formatNumber(receiptData.makeup_artist.price)"></td>
                                        </tr>
                                    </template>
                                    <template x-if="receiptData?.photographer_package">
                                        <tr class="border-b border-gray-50 last:border-0">
                                            <td class="py-2.5">
                                                <div class="font-medium text-xs text-gray-600">บริการถ่ายภาพ</div>
                                                <div class="text-[10px] text-blue-400" x-text="receiptData.photographer_package.package_name"></div>
                                            </td>
                                            <td class="text-right py-2.5 text-gray-500" x-text="formatNumber(receiptData.photographer_package.price)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- Summary --}}
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-2">
                            <div class="flex justify-between text-base font-bold text-gray-900">
                                <span>ยอดรวมสุทธิ</span>
                                <span x-text="formatNumber(receiptData?.total_amount)"></span>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="mt-8 pt-4 border-t border-dashed border-gray-200">
                            <div class="flex justify-between items-end">
                                <div class="text-left">
                                    <p class="text-[10px] text-gray-400 uppercase mb-1">ผู้ทำรายการ (Cashier)</p>
                                    <p class="text-xs font-bold text-gray-800">{{ Auth::user()->first_name }}</p>
                                </div>
                                <div class="text-center">
                                    <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent('https://watakacha-supabase.onrender.com/')}`"
                                        class="w-16 h-16 mix-blend-multiply opacity-80 border p-1 rounded bg-white shadow-sm">
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <p class="font-bold text-gray-800 text-xs">ขอบคุณที่ใช้บริการ</p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="bg-gray-50 px-6 py-4 no-print flex gap-3 border-t border-gray-100 rounded-b-xl">
                        <button @click="window.print()" class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            พิมพ์ใบเสร็จ
                        </button>
                        <button @click="showReceipt = false" class="flex-1 py-2.5 bg-gray-900 text-white font-bold rounded-xl shadow-lg hover:bg-black transition">ปิดหน้าต่าง</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function serviceCostSystem() {
            return {
                activeTab: 'pending',
                showModal: false,
                showReceipt: false,
                receiptData: null,
                form: {
                    id: null,
                    has_makeup: false,
                    has_photographer: false,
                    makeup_cost: 0,
                    photographer_cost: 0,
                    suggested_makeup: 0,
                    suggested_photographer: 0,
                    package_name: ''
                },
                openModal(item) {
                    this.form.id = item.rental_id;
                    this.form.has_makeup = !!item.makeup_artist;
                    this.form.suggested_makeup = item.makeup_artist ? parseFloat(item.makeup_artist.price) : 0;
                    this.form.makeup_cost = this.form.suggested_makeup;
                    this.form.has_photographer = !!item.photographer;
                    this.form.suggested_photographer = item.photographer_package ? parseFloat(item.photographer_package.price) : 0;
                    this.form.package_name = item.photographer_package ? item.photographer_package.package_name : '';
                    this.form.photographer_cost = this.form.suggested_photographer;
                    this.showModal = true;
                },
                openReceiptModal(item) {
                    this.receiptData = item;
                    this.showReceipt = true;
                },
                async submitCost() {
                    try {
                        const res = await fetch(`/admin/service-costs/${this.form.id}/update`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                makeup_cost: this.form.makeup_cost,
                                photographer_cost: this.form.photographer_cost
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'บันทึกสำเร็จ',
                                text: 'ข้อมูลต้นทุนถูกบันทึกเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => window.location.reload());
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่'
                        });
                    }
                },
                formatNumber(num) {
                    return new Intl.NumberFormat('th-TH', {
                        minimumFractionDigits: 2
                    }).format(num || 0);
                }
            }
        }
    </script>
</x-app-layout>