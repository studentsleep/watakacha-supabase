<x-app-layout>
    {{-- ✅ SweetAlert2 --}}

    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    {{-- Header --}}
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                </svg>
            </div>
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                จัดการซัก-ซ่อม (Maintenance)
            </h2>
        </div>
    </x-slot>

    {{-- Main Content --}}
    <div class="py-8" x-data="maintenanceSystem()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🟢 Tabs Navigation --}}
            <div class="flex space-x-1 rounded-xl bg-gray-100 p-1 mb-6 w-fit">
                <button @click="activeTab = 'pending'"
                    :class="activeTab === 'pending' ? 'bg-white text-gray-800 shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="w-32 rounded-lg py-2.5 text-sm font-bold leading-5 transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    รอส่งร้าน
                    @if($pending->count() > 0)
                    <span class="ml-1 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs">{{ $pending->count() }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'in_progress'"
                    :class="activeTab === 'in_progress' ? 'bg-white text-blue-600 shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="w-32 rounded-lg py-2.5 text-sm font-bold leading-5 transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    อยู่ที่ร้าน
                    @if($inProgress->count() > 0)
                    <span class="ml-1 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs">{{ $inProgress->count() }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'bg-white text-green-600 shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="w-32 rounded-lg py-2.5 text-sm font-bold leading-5 transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    ประวัติ
                </button>
            </div>

            {{-- 📦 Content Area --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 min-h-[400px]">

                {{-- TAB 1: Pending --}}
                <div x-show="activeTab === 'pending'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">สินค้า (Item)</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">อาการ/สาเหตุ</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">ที่มา</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($pending as $mt)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            {{-- ✅ ส่วนรูปภาพ --}}
                                            <div class="h-12 w-12 rounded-lg bg-gray-100 overflow-hidden shrink-0 border border-gray-200">
                                                @if($mt->item && $mt->item->images && $mt->item->images->count() > 0)
                                                @php $path = $mt->item->images->first()->path; @endphp
                                                <img src="{{ Str::startsWith($path, 'http') ? $path : asset('storage/' . $path) }}"
                                                    alt="Img" class="w-full h-full object-cover">

                                                @elseif($mt->accessory && $mt->accessory->path)
                                                {{-- กรณี Accessory ก็เช็คเหมือนกัน --}}
                                                @php $path = $mt->accessory->path; @endphp
                                                <img src="{{ Str::startsWith($path, 'http') ? $path : asset('storage/' . $path) }}"
                                                    alt="Acc" class="w-full h-full object-cover">

                                                @else
                                                <div class="w-full h-full flex items-center justify-center bg-orange-100 text-orange-600 font-bold">
                                                    {{ mb_substr($mt->item ? $mt->item->item_name : ($mt->accessory ? $mt->accessory->name : '?'), 0, 1) }}
                                                </div>
                                                @endif
                                            </div>

                                            <div>
                                                <div class="font-bold text-gray-800 dark:text-gray-100">
                                                    {{ $mt->item ? $mt->item->item_name : ($mt->accessory ? $mt->accessory->name . ' (อุปกรณ์)' : 'ไม่ทราบชื่อ') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    รหัส: {{ $mt->item_id ?? $mt->accessory_id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                            {{ $mt->damage_description }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-400">
                                                {{ $mt->rental && $mt->rental->member ? $mt->rental->member->first_name : 'ลูกค้าทั่วไป' }}
                                            </span>
                                            <span class="text-xs text-gray-400">จาก การเช่า #{{ $mt->rental_id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button @click="openSendModal({{ Js::from($mt) }}, '{{ $mt->item ? $mt->item->item_name : ($mt->accessory ? $mt->accessory->name : '') }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            ส่งซ่อม/ซัก
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <p>ไม่มีรายการรอซ่อม</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: In Progress --}}
                <div x-show="activeTab === 'in_progress'" x-transition:enter="transition ease-out duration-300" style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">สินค้า</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">ร้านที่ส่ง / ประเภท</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">วันที่ส่ง</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($inProgress as $mt)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-12 w-12 rounded-lg bg-gray-100 overflow-hidden shrink-0 border border-gray-200">
                                                @if($mt->item && $mt->item->images && $mt->item->images->count() > 0)
                                                <img src="{{ asset('storage/' . $mt->item->images->first()->path) }}" class="w-full h-full object-cover">
                                                @elseif($mt->accessory && $mt->accessory->path)
                                                <img src="{{ asset('storage/' . $mt->accessory->path) }}" class="w-full h-full object-cover">
                                                @else
                                                <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-600 font-bold">
                                                    {{ mb_substr($mt->item ? $mt->item->item_name : ($mt->accessory ? $mt->accessory->name : '?'), 0, 1) }}
                                                </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 dark:text-gray-100">
                                                    {{ $mt->item ? $mt->item->item_name : ($mt->accessory ? $mt->accessory->name : '') }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-0.5 bg-gray-100 inline-block px-1.5 rounded border border-gray-200">
                                                    จาก การเช่า #{{ $mt->rental_id }}
                                                </div>
                                                <div class="text-xs text-red-500 mt-1">อาการ: {{ $mt->damage_description }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-blue-600">{{ $mt->careShop->care_name ?? '-' }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200 mt-1">
                                            @if($mt->type == 'laundry') ซักรีด @elseif($mt->type == 'repair') ซ่อมแซม @else ซัก+ซ่อม @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($mt->sent_at)->locale('th')->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button @click="openReceiveModal({{ Js::from($mt) }})" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            รับคืน/เสร็จสิ้น
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">ไม่มีรายการที่ร้าน</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 3: History --}}
                <div x-show="activeTab === 'history'" x-transition:enter="transition ease-out duration-300" style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">สินค้า</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">ร้าน / ประเภท</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">ค่าใช้จ่าย</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">วันที่เสร็จ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($history as $mt)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-12 w-12 rounded-lg bg-gray-100 overflow-hidden shrink-0 border border-gray-200">
                                                @if($mt->item && $mt->item->images && $mt->item->images->count() > 0)
                                                <img src="{{ asset('storage/' . $mt->item->images->first()->path) }}" class="w-full h-full object-cover">
                                                @elseif($mt->accessory && $mt->accessory->path)
                                                <img src="{{ asset('storage/' . $mt->accessory->path) }}" class="w-full h-full object-cover">
                                                @else
                                                <div class="w-full h-full flex items-center justify-center bg-green-100 text-green-600 font-bold">
                                                    {{ mb_substr($mt->item ? $mt->item->item_name : ($mt->accessory ? $mt->accessory->name : '?'), 0, 1) }}
                                                </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 dark:text-gray-100">
                                                    {{ $mt->item ? $mt->item->item_name : ($mt->accessory ? $mt->accessory->name : '') }}
                                                </div>
                                                <div class="text-xs text-gray-400 mt-0.5">รหัสการเช่า #{{ $mt->rental_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $mt->careShop->care_name ?? '-' }}
                                        <span class="text-xs text-gray-400">
                                            @if($mt->type == 'laundry') (ซักรีด) @elseif($mt->type == 'repair') (ซ่อมแซม) @else (ซักและซ่อม) @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-mono font-bold text-red-500">{{ number_format($mt->actual_cost, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-green-600 font-bold">
                                        {{ \Carbon\Carbon::parse($mt->received_at)->locale('th')->translatedFormat('d M Y') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">ไม่พบประวัติ</td>
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

        {{-- 🚚 MODAL: ส่งร้าน --}}
        <div x-show="showSendModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showSendModal = false"></div>
                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl relative z-50">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">ส่งร้านซัก/ซ่อม</h3>
                        <button @click="showSendModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-orange-50 p-3 rounded-lg border border-orange-100">
                            <p class="text-xs text-orange-800 font-bold mb-1">สินค้า:</p>
                            <p class="text-sm text-gray-900 font-medium" x-text="currentItemName"></p>
                            <p class="text-xs text-red-500 mt-1">อาการ: <span x-text="currentItem?.damage_description"></span></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">เลือกร้าน (Care Shop)</label>
                            <select x-model="sendForm.care_shop_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-11">
                                <option value="">-- กรุณาเลือก --</option>
                                @foreach($shops as $shop)
                                <option value="{{ $shop->care_shop_id }}">{{ $shop->care_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">ประเภทงาน</label>
                            <select x-model="sendForm.type" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-11">
                                <option value="repair">🛠️ ซ่อมแซม (Repair)</option>
                                <option value="laundry">🧺 ซักรีด (Laundry)</option>
                                <option value="both">✨ ทั้งซักและซ่อม</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button @click="showSendModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">ยกเลิก</button>
                        <button @click="submitSend" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">ยืนยันส่ง</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ MODAL: รับของคืน --}}
        <div x-show="showReceiveModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showReceiveModal = false"></div>
                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl relative z-50">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">รับของคืน & บันทึกค่าใช้จ่าย</h3>
                        <button @click="showReceiveModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">ค่าจ้างร้าน (ต้นทุน)</label>
                            <div class="relative">
                                <input type="number" x-model="receiveForm.shop_cost" class="w-full rounded-xl border-gray-300 pr-10 text-right font-bold text-gray-900 h-11 text-lg" placeholder="0.00">
                                <span class="absolute right-4 top-2.5 text-gray-400 font-bold"></span>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">* ระบุจำนวนเงินที่จ่ายให้ร้านซัก/ซ่อม</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button @click="showReceiveModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">ยกเลิก</button>
                        <button @click="submitReceive" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">ยืนยันรับคืน</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function maintenanceSystem() {
            return {
                activeTab: 'pending',
                showSendModal: false,
                showReceiveModal: false,
                currentItem: null,
                currentItemName: '',

                sendForm: {
                    care_shop_id: '',
                    type: 'repair'
                },
                receiveForm: {
                    shop_cost: ''
                },

                // ปรับฟังก์ชันให้รับชื่อสินค้ามาด้วยเพื่อความสะดวก
                openSendModal(item, name) {
                    this.currentItem = item;
                    this.currentItemName = name;
                    this.sendForm = {
                        care_shop_id: '',
                        type: 'repair'
                    };
                    this.showSendModal = true;
                },

                async submitSend() {
                    if (!this.sendForm.care_shop_id) {
                        return Swal.fire({
                            icon: 'warning',
                            title: 'แจ้งเตือน',
                            text: 'กรุณาเลือกร้านซัก/ซ่อม',
                            confirmButtonColor: '#d97706'
                        });
                    }

                    try {
                        const res = await fetch(`{{ url('admin/maintenance') }}/${this.currentItem.id}/send`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.sendForm)
                        });
                        const data = await res.json();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'ส่งซ่อมเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => window.location.reload());
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
                        });
                    }
                },

                openReceiveModal(item) {
                    this.currentItem = item;
                    this.receiveForm.shop_cost = '';
                    this.showReceiveModal = true;
                },

                async submitReceive() {
                    if (this.receiveForm.shop_cost === '') {
                        return Swal.fire({
                            icon: 'warning',
                            title: 'แจ้งเตือน',
                            text: 'กรุณาระบุค่าใช้จ่าย (ใส่ 0 หากไม่มี)',
                            confirmButtonColor: '#d97706'
                        });
                    }

                    try {
                        // เปลี่ยนชื่อ parameter จาก shop_cost เป็น actual_cost ตาม Controller ใหม่
                        const res = await fetch(`{{ url('admin/maintenance') }}/${this.currentItem.id}/receive`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                actual_cost: this.receiveForm.shop_cost
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'บันทึกรับคืนเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => window.location.reload());
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
                        });
                    }
                }
            }
        }
    </script>
</x-app-layout>