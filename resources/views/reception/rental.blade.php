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

            .shadow-2xl,
            .bg-gray-800 {
                box-shadow: none !important;
            }
        }
    </style>
    {{-- CSS และ JS ของ Flatpickr (ปฏิทิน) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
    {{-- ปรับแต่งสีปฏิทินให้เข้ากับธีม Indigo --}}
    <style>
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange,
        .flatpickr-day.selected:focus,
        .flatpickr-day.startRange:focus,
        .flatpickr-day.endRange:focus,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover,
        .flatpickr-day.selected.prevMonthDay,
        .flatpickr-day.startRange.prevMonthDay,
        .flatpickr-day.endRange.prevMonthDay,
        .flatpickr-day.selected.nextMonthDay,
        .flatpickr-day.startRange.nextMonthDay,
        .flatpickr-day.endRange.nextMonthDay {
            background: #4f46e5;
            /* Indigo-600 */
            border-color: #4f46e5;
        }
    </style>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3h18v18H3zM9 9h6M9 15h6M15 9l-6 6M9 9l6 6" />
                </svg>
            </div>
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                บริการเช่าชุด (Rental Service)
            </h2>
        </div>
    </x-slot>

    {{-- เริ่ม x-data --}}
    <div class="py-8" x-data="rentalSystem({
        promotions: {{ Js::from($promotions) }},
        makeupArtists: {{ Js::from($makeup_artists) }},
        packages: {{ Js::from($photo_packages) }},
        photographers: {{ Js::from($photographers) }},
        accessoriesData: {{ Js::from($accessories) }}
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ================= LEFT COLUMN: Forms (2/3) ================= --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- 1. ข้อมูลสมาชิก --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3">
                                <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm shadow-md">1</span>
                                ข้อมูลสมาชิก
                            </h3>
                            <label class="flex items-center space-x-2 text-sm cursor-pointer select-none group bg-gray-50 px-4 py-2 rounded-full border border-gray-200 hover:bg-gray-100 transition">
                                <input type="checkbox" x-model="isGuest" @change="toggleGuest" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="text-gray-600 font-bold group-hover:text-gray-900 transition">ลูกค้าทั่วไป (Guest)</span>
                            </label>
                        </div>

                        {{-- Search --}}
                        <div x-show="!isGuest && !member" class="flex gap-3 transition-all duration-300">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8" />
                                        <path d="m21 21-4.3-4.3" />
                                    </svg>
                                </div>
                                <input type="text" x-model="memberQuery" @keydown.enter.prevent="checkMember" class="w-full pl-11 rounded-xl border-gray-300 bg-gray-50 focus:bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-12 transition-all" placeholder="ค้นหาเบอร์โทร, รหัสสมาชิก หรือชื่อ...">
                            </div>
                            <button @click="checkMember" class="px-6 h-12 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-transform active:scale-95">ค้นหา</button>
                        </div>
                        <p x-show="memberError" class="text-red-600 text-sm mt-3 flex items-center gap-2 bg-red-50 p-3 rounded-lg border border-red-100 font-medium animate-pulse">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span x-text="memberErrorMsg"></span>
                        </p>

                        {{-- Member Card --}}
                        <div x-show="member && !isGuest" x-transition class="mt-4 p-5 rounded-2xl border flex items-start gap-4 shadow-sm bg-green-50 border-green-200">
                            <div class="p-3 rounded-full shrink-0 bg-white text-green-600 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <polyline points="16 11 18 13 22 9" />
                                </svg>
                            </div>
                            <div class="flex-grow">
                                <h4 class="font-bold text-xl text-gray-900" x-text="member?.first_name + ' ' + member?.last_name"></h4>
                                <div class="text-sm text-gray-600 mt-1 flex flex-wrap gap-3">
                                    <span class="bg-white px-2 py-1 rounded border border-green-100">ID: <span class="font-mono font-bold" x-text="member?.member_id"></span></span>
                                    <span class="bg-white px-2 py-1 rounded border border-green-100">Tel: <span x-text="member?.tel"></span></span>
                                </div>
                                <div class="mt-2 inline-flex items-center gap-1 bg-green-200 px-3 py-1 rounded-full text-xs font-bold text-green-800 shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Points: <span x-text="member?.points"></span>
                                </div>
                            </div>
                            <button @click="resetMember" class="text-gray-400 hover:text-red-500 transition p-2 hover:bg-white rounded-full">✕</button>
                        </div>

                        {{-- Guest Card --}}
                        <div x-show="isGuest" x-transition class="mt-4 p-5 rounded-2xl border flex flex-col gap-4 shadow-sm bg-gray-50 border-gray-200">
                            <div class="flex items-start gap-4">
                                <div class="p-3 rounded-full shrink-0 bg-white text-gray-500 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-bold text-lg text-gray-800">ลูกค้าทั่วไป (Guest)</h4>
                                    <p class="text-xs text-gray-500 mb-3">กรุณากรอกข้อมูลติดต่อเพื่อบันทึกในใบเสร็จ</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <input type="text" x-model="guestName" class="text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 w-full h-10" placeholder="ชื่อลูกค้า (เช่น คุณสมชาย)">
                                        <input type="text" x-model="guestPhone" class="text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 w-full h-10" placeholder="เบอร์โทรศัพท์">
                                    </div>
                                </div>
                                <button @click="resetMember" class="text-gray-400 hover:text-red-500 transition p-2 hover:bg-white rounded-full">✕</button>
                            </div>
                        </div>
                    </div>

                    {{-- 2. เลือกสินค้า --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-opacity duration-200"
                        :class="(member || isGuest) ? 'opacity-100' : 'opacity-50 pointer-events-none'">

                        <div class="border-b border-gray-100 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3">
                                <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm shadow-md">2</span>
                                เลือกชุดและอุปกรณ์
                            </h3>
                        </div>

                        {{-- 2.1 Main Items --}}
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">ชุดและสินค้าหลัก</label>
                            <div class="relative">
                                <input type="text" x-model="itemQuery" @input.debounce.300ms="searchItems" @focus="showItemsDropdown = true; searchItems()" @click.away="showItemsDropdown = false"
                                    class="w-full pl-11 pr-10 py-3 rounded-xl border-gray-300 bg-gray-50 focus:bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all placeholder-gray-400 text-gray-800"
                                    placeholder="พิมพ์ชื่อชุด... (คลิกเพื่อดูรายการแนะนำ)">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                                    <span x-show="!isLoadingItems"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg></span>
                                    <span x-show="isLoadingItems" class="animate-spin"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg></span>
                                </div>

                                {{-- Dropdown --}}
                                <div x-show="showItemsDropdown" class="absolute z-20 w-full bg-white border border-gray-200 mt-2 rounded-xl shadow-2xl max-h-60 overflow-y-auto" style="display: none;">
                                    <div x-show="isLoadingItems" class="px-4 py-3 text-center text-gray-500 text-sm">กำลังค้นหาข้อมูล...</div>
                                    <ul x-show="!isLoadingItems && items.length > 0">
                                        <template x-for="item in items" :key="item.id">
                                            <li @click="addToCart(item); showItemsDropdown = false" class="px-4 py-3 hover:bg-indigo-50 cursor-pointer flex justify-between items-center border-b border-gray-50 last:border-0 transition group">
                                                <div>
                                                    <span class="font-bold text-gray-800 group-hover:text-indigo-700 block text-sm" x-text="item.item_name"></span>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="text-xs text-gray-400 bg-gray-100 px-1.5 rounded" x-text="'#' + item.id"></span>
                                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border"
                                                            :class="item.available_stock > 0 ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
                                                            x-text="'ว่าง: ' + item.available_stock + ' / ' + item.stock">
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="text-indigo-600 font-bold bg-indigo-50 px-3 py-1 rounded-lg text-sm" x-text="formatPrice(item.price)"></span>
                                            </li>
                                        </template>
                                    </ul>
                                    <div x-show="!isLoadingItems && items.length === 0" class="px-4 py-3 text-center text-gray-400 text-sm">ไม่พบสินค้า หรือสินค้าหมดช่วงนี้</div>
                                </div>
                            </div>
                        </div>

                        {{-- 2.2 Accessories --}}
                        <div class="mb-6 bg-orange-50 p-5 rounded-xl border border-orange-100">
                            <label class="block text-xs font-bold text-orange-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                เพิ่มอุปกรณ์เสริม
                            </label>
                            <div class="relative">
                                <input type="text" x-model="accessoryQuery" @focus="showAccessoryDropdown = true" @click.away="showAccessoryDropdown = false"
                                    class="w-full pl-11 pr-10 py-2.5 rounded-lg border-orange-200 focus:border-orange-500 focus:ring-orange-500 text-gray-700 placeholder-orange-300 bg-white"
                                    placeholder="พิมพ์ค้นหา หรือเลื่อนดูอุปกรณ์เสริม...">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-orange-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <div x-show="showAccessoryDropdown" class="absolute z-20 w-full bg-white border border-gray-200 mt-2 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                    <ul>
                                        <template x-for="acc in filteredAccessories" :key="acc.id">
                                            <li @click="addAccessoryToCart(acc); showAccessoryDropdown = false; accessoryQuery = ''" class="px-4 py-3 hover:bg-orange-50 cursor-pointer flex justify-between items-center border-b border-gray-50 last:border-0 transition group">
                                                <div>
                                                    <span class="font-bold text-gray-700 group-hover:text-orange-700 block text-sm" x-text="acc.name"></span>
                                                    <span class="text-xs text-gray-400">Stock: <span x-text="acc.stock"></span></span>
                                                </div>
                                                <span class="text-gray-600 font-bold bg-gray-100 px-2 py-1 rounded text-xs" x-text="formatPrice(acc.price)"></span>
                                            </li>
                                        </template>
                                        <li x-show="filteredAccessories.length === 0" class="px-4 py-3 text-gray-400 text-center text-sm">ไม่พบข้อมูลอุปกรณ์เสริม</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Cart Table --}}
                        <div class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 font-bold">รายการ</th>
                                        <th class="px-4 py-3 text-right font-bold">ราคา</th>
                                        <th class="px-4 py-3 text-center font-bold">จำนวน</th>
                                        <th class="px-4 py-3 text-right font-bold">รวม</th>
                                        <th class="px-4 py-3 text-center w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, index) in cart" :key="'item-'+item.id">
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-gray-800" x-text="item.item_name"></div>
                                                <span class="text-[10px] bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-100">ชุดหลัก</span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono" x-text="formatPrice(item.price)"></td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-1 bg-gray-100 rounded-lg p-1 w-fit mx-auto">
                                                    <button @click="decreaseQty(index)" class="w-6 h-6 rounded bg-white hover:bg-gray-200 shadow-sm flex items-center justify-center text-gray-600 font-bold transition">-</button>
                                                    <span class="w-8 text-center font-bold text-gray-800" x-text="item.quantity"></span>
                                                    <button @click="increaseQty(index)" class="w-6 h-6 rounded bg-indigo-600 hover:bg-indigo-700 shadow-sm flex items-center justify-center text-white font-bold transition">+</button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-gray-900" x-text="formatPrice(item.price * item.quantity)"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button @click="askDeleteItem('main', index)" class="text-gray-400 hover:text-red-500 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(acc, index) in accessoryCart" :key="'acc-'+acc.id">
                                        <tr class="hover:bg-orange-50 transition bg-orange-50/30">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-800" x-text="acc.name"></div>
                                                <span class="text-[10px] bg-orange-100 text-orange-800 px-1.5 py-0.5 rounded border border-orange-200">อุปกรณ์เสริม</span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono" x-text="formatPrice(acc.price)"></td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-1 bg-white/50 rounded-lg p-1 w-fit mx-auto border border-orange-100">
                                                    <button @click="decreaseAccQty(index)" class="w-6 h-6 rounded bg-white hover:bg-orange-100 shadow-sm flex items-center justify-center text-orange-800 font-bold transition">-</button>
                                                    <span class="w-8 text-center font-bold text-gray-800" x-text="acc.quantity"></span>
                                                    <button @click="increaseAccQty(index)" class="w-6 h-6 rounded bg-orange-500 hover:bg-orange-600 shadow-sm flex items-center justify-center text-white font-bold transition">+</button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-gray-900" x-text="formatPrice(acc.price * acc.quantity)"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button @click="askDeleteItem('acc', index)" class="text-gray-400 hover:text-red-500 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="cart.length === 0 && accessoryCart.length === 0">
                                        <td colspan="5" class="px-4 py-12 text-center text-gray-400 flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                            <span>ยังไม่มีสินค้าในรายการ</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 3. Extra Services --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-opacity duration-200"
                        :class="(cart.length > 0 || accessoryCart.length > 0) ? 'opacity-100' : 'opacity-50 pointer-events-none'">

                        <div class="border-b border-gray-100 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3">
                                <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm shadow-md">3</span>
                                บริการเสริมอื่นๆ & โปรโมชั่น
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2 bg-yellow-50 p-5 rounded-2xl border border-yellow-200">
                                <x-input-label value="เลือกโปรโมชั่น (Promotion)" class="mb-2 text-yellow-800 font-bold" />
                                <div class="relative">
                                    <select x-model="selectedPromotionId" class="w-full rounded-xl border-yellow-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 bg-white text-gray-800 py-3 cursor-pointer">
                                        <option value="">-- ไม่ใช้โปรโมชั่น --</option>
                                        <template x-for="promo in promotions" :key="promo.promotion_id">
                                            <option :value="promo.promotion_id">
                                                <span x-text="promo.promotion_name"></span>
                                                <span x-text="promo.discount_type === 'percentage' ? '(ลด ' + promo.discount_value + '%)' : '(ลด ' + promo.discount_value + ' บาท)'"></span>
                                            </option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <x-input-label value="บริการช่างแต่งหน้า" class="text-gray-600 font-bold mb-2 text-xs uppercase tracking-wide" />
                                <select x-model="selectedMakeupId" class="w-full rounded-xl border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-700 py-2.5">
                                    <option value="">-- ไม่รับบริการ --</option>
                                    <template x-for="artist in makeupArtists" :key="artist.makeup_id">
                                        <option :value="artist.makeup_id" x-text="artist.first_name + ' ' + artist.last_name + ' (' + formatPrice(artist.price) + ')'"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <x-input-label value="แพ็กเกจถ่ายภาพ" class="text-gray-600 font-bold mb-2 text-xs uppercase tracking-wide" />
                                <select x-model="selectedPackageId" class="w-full rounded-xl border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-700 py-2.5">
                                    <option value="">-- ไม่รับบริการ --</option>
                                    <template x-for="pkg in packages" :key="pkg.package_id">
                                        <option :value="pkg.package_id" x-text="pkg.package_name + ' (' + formatPrice(pkg.price) + ')'"></option>
                                    </template>
                                </select>
                                <div x-show="selectedPackageId" x-transition class="mt-3 bg-gray-50 p-3 rounded-xl border border-gray-200">
                                    <x-input-label value="ระบุช่างภาพ" class="text-xs text-gray-500 mb-1" />
                                    <select x-model="selectedPhotographerId" class="w-full text-sm rounded-lg border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-700">
                                        <option value="">-- ไม่ระบุ (ร้านจัดให้) --</option>
                                        <template x-for="pg in photographers" :key="pg.photographer_id">
                                            <option :value="pg.photographer_id" x-text="pg.first_name + ' ' + pg.last_name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> {{-- END LEFT COLUMN --}}

                {{-- ================= RIGHT COLUMN: Summary (1/3) ================= --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-6 border-b border-gray-100 pb-4 flex items-center gap-2">
                            <span class="bg-gray-900 text-white p-1.5 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </span>
                            สรุปรายการ
                        </h3>

                        <div class="space-y-6">
                            {{-- Date Section --}}
                            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 space-y-4">
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">วันที่เช่า (เริ่ม 7 วัน)</span>

                                    {{-- Flatpickr Date Input (Thai) --}}
                                    <div class="relative">
                                        <input type="text" x-ref="picker"
                                            class="block w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-indigo-700 bg-white pl-10 cursor-pointer h-10"
                                            placeholder="เลือกวันที่..."
                                            x-init="
                                flatpickr($refs.picker, {
                                    locale: 'th',
                                    dateFormat: 'Y-m-d',
                                    altInput: true,
                                    altFormat: 'j F Y',
                                    defaultDate: rentalDate,
                                    disableMobile: 'true',
                                    onChange: function(selectedDates, dateStr) {
                                        rentalDate = dateStr;
                                        updateReturnDate();
                                    }
                                })
                            ">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">กำหนดคืนอัตโนมัติ</span>
                                    <div class="block w-full px-4 py-2.5 bg-indigo-50 rounded-xl border border-indigo-100 text-sm text-indigo-800 font-bold flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span x-text="new Date(new Date(rentalDate).getTime() + (6 * 24 * 60 * 60 * 1000)).toLocaleDateString('th-TH', {day: 'numeric', month: 'long', year: 'numeric'})"></span>
                                    </div>
                                </div>

                                <div class="bg-orange-50 p-3 rounded-xl border border-orange-100 flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div class="text-xs text-orange-800 font-medium">
                                        ระบบล็อกชุดเพิ่ม 3 วันเพื่อซัก/ซ่อม
                                    </div>
                                </div>
                            </div>

                            {{-- Payment Method --}}


                            {{-- Price Detail --}}
                            <div class="text-sm space-y-3 pt-4 border-t border-gray-100">
                                <div class="flex justify-between items-center text-gray-600"><span>ค่าชุดหลัก <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full" x-text="cartItemCount"></span></span><span class="font-bold text-gray-900" x-text="formatPrice(cartTotal)"></span></div>
                                <div class="flex justify-between items-center text-gray-600" x-show="accessoryCart.length > 0"><span>อุปกรณ์เสริม <span class="text-xs bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full" x-text="accessoryItemCount"></span></span><span class="font-bold text-gray-900" x-text="formatPrice(accessoryTotal)"></span></div>
                                <template x-if="makeupPrice > 0">
                                    <div class="flex justify-between items-center text-gray-600"><span>ค่าแต่งหน้า</span><span class="font-bold text-gray-900" x-text="formatPrice(makeupPrice)"></span></div>
                                </template>
                                <template x-if="packagePrice > 0">
                                    <div class="flex justify-between items-center text-gray-600"><span>ค่าถ่ายภาพ</span><span class="font-bold text-gray-900" x-text="formatPrice(packagePrice)"></span></div>
                                </template>
                                <template x-if="discountAmount > 0">
                                    <div class="flex justify-between items-center text-green-600 bg-green-50 p-2 rounded-lg"><span>ส่วนลดโปรโมชั่น</span><span class="font-bold" x-text="'-' + formatPrice(discountAmount)"></span></div>
                                </template>
                            </div>

                            {{-- Points Redemption --}}
                            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100" x-show="member">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-indigo-700 uppercase flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        ใช้แต้มแลกส่วนลด
                                    </span>
                                    <span class="text-[10px] bg-white text-indigo-600 px-2 py-0.5 rounded-full font-bold shadow-sm border border-indigo-100">
                                        มี <span x-text="member?.points"></span> แต้ม
                                    </span>
                                </div>

                                <div class="relative">
                                    <input type="number" x-model="pointsToUse"
                                        class="w-full text-sm rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 text-right pr-12 h-10"
                                        :max="member?.points" min="0">
                                    <span class="absolute right-3 top-2.5 text-xs text-indigo-400 font-bold">แต้ม</span>
                                </div>

                                <div class="flex justify-between items-center mt-2 text-xs text-indigo-800" x-show="pointsDiscountValue > 0">
                                    <span>ลดเพิ่ม:</span>
                                    <span class="font-bold text-sm bg-white px-2 rounded" x-text="'-' + formatPrice(pointsDiscountValue)"></span>
                                </div>
                                <p class="text-[10px] text-indigo-400 mt-2 text-center bg-white/50 rounded py-1">100 แต้ม = 1 บาท</p>
                            </div>

                            {{-- Grand Total --}}
                            <div class="pt-4 border-t-2 border-dashed border-gray-200">
                                <div class="flex justify-between items-end mb-4">
                                    <span class="text-green-600 font-bold text-sm">ยอดรวมสุทธิ</span>
                                    {{-- แก้สีตรงนี้ --}}
                                    <span class="text-3xl font-black text-green-600 tracking-tight" x-text="formatPrice(grandTotal)"></span>
                                </div>
                                <div class="bg-gray-900 text-white p-4 rounded-xl shadow-lg flex justify-between items-center">
                                    <div>
                                        <p class="text-xs text-gray-400 font-bold uppercase">ยอดมัดจำ (50%)</p>
                                        <p class="text-xs text-gray-500 mt-0.5">ต้องชำระวันนี้</p>
                                    </div>
                                    <span class="text-2xl font-bold" x-text="formatPrice(depositAmount)"></span>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button @click="openConfirmModal" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transform transition hover:-translate-y-0.5 flex justify-center items-center gap-2 text-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="(!member && !isGuest) || (cart.length === 0 && accessoryCart.length === 0) || isSubmitting">
                                <span x-show="!isSubmitting">ยืนยันการเช่า (ชำระเงินภายหลัง)</span>
                                <span x-show="isSubmitting" class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    กำลังบันทึก...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- MODALS --}}
                <div x-show="showConfirm" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 text-center">
                        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="showConfirm = false"></div>
                        <div class="inline-block w-full max-w-md p-8 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl relative z-50">
                            <h3 class="text-2xl font-bold leading-6 text-gray-900 mb-6 text-center">ยืนยันการเช่า?</h3>
                            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 mb-6 space-y-3">
                                <div class="flex justify-between text-gray-600 text-sm"><span>ยอดรวมสุทธิ</span><span class="font-bold text-gray-900" x-text="formatPrice(grandTotal)"></span></div>
                                <div class="border-t border-gray-200 my-2"></div>
                                <div class="flex justify-between items-center"><span class="text-gray-600 font-bold">มัดจำที่ต้องจ่าย</span><span class="text-2xl font-bold text-indigo-600" x-text="formatPrice(depositAmount)"></span></div>
                            </div>
                            <div class="flex gap-3"><button @click="showConfirm = false" class="flex-1 px-4 py-3 text-gray-700 bg-white border border-gray-300 font-bold rounded-xl hover:bg-gray-50 transition">ยกเลิก</button><button @click="processSubmission" class="flex-1 px-4 py-3 text-white bg-indigo-600 font-bold rounded-xl hover:bg-indigo-700 shadow-lg transition">ยืนยัน</button></div>
                        </div>
                    </div>
                </div>

                <div x-show="showAlert" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 text-center">
                        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50"></div>
                        <div class="inline-block w-full max-w-sm p-6 my-8 overflow-hidden text-center align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" :class="alertType === 'success' ? 'bg-green-100' : 'bg-red-100'"><span x-show="alertType === 'success'" class="text-2xl">✅</span><span x-show="alertType === 'error'" class="text-2xl">❌</span></div>
                            <h3 class="text-lg font-bold leading-6 text-gray-900" x-text="alertTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="alertMessage"></p>
                            </div>
                            <div class="mt-6"><button @click="closeAlert" class="inline-flex justify-center w-full px-4 py-2.5 text-sm font-bold text-white bg-gray-900 border border-transparent rounded-xl hover:bg-black focus:outline-none">ตกลง</button></div>
                        </div>
                    </div>
                </div>

                <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 text-center">
                        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50" @click="showDeleteModal = false"></div>
                        <div class="inline-block w-full max-w-sm p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50">
                            <div class="flex items-center gap-3 mb-4 text-red-600">
                                <div class="bg-red-100 p-2 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18" />
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                    </svg></div>
                                <h3 class="text-lg font-bold">ยืนยันการลบ?</h3>
                            </div>
                            <p class="text-gray-600 mb-6">คุณต้องการลบรายการนี้ออกจากตะกร้าใช่หรือไม่?</p>
                            <div class="flex justify-end gap-3"><button @click="showDeleteModal = false" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">ยกเลิก</button><button @click="confirmDelete" class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 font-bold">ลบรายการ</button></div>
                        </div>
                    </div>
                </div>

                {{-- 🧾 RECEIPT MODAL (ดีไซน์ใหม่ เหมือนหน้า History) --}}
                <div x-show="showReceipt" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">

                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="closeReceipt"></div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div id="receipt-modal" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">

                            {{-- 🟢 ส่วนหัวใบเสร็จ --}}
                            <div class="bg-gray-900 text-white p-6 relative">
                                <div class="absolute top-4 right-4 cursor-pointer opacity-70 hover:opacity-100 no-print" @click="closeReceipt">
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
                                            #<span x-text="receiptData.rental_id"></span>
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
                                        <p class="text-sm font-medium" x-text="new Date().toLocaleDateString('th-TH')"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- 🟢 เนื้อหาใบเสร็จ --}}
                            <div class="p-6 bg-white relative">
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-[0.3] pointer-events-none">
                                    <img src="{{ asset('images/logo.png') }}" class="w-80 h-80 object-contain grayscale">
                                </div>

                                {{-- ข้อมูลลูกค้า --}}
                                <div class="flex justify-between mb-6 pb-4 border-b border-gray-100">
                                    <div class="text-sm">
                                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">ลูกค้า (Customer)</p>
                                        <template x-if="member">
                                            <div>
                                                <p class="font-bold text-gray-800" x-text="member.first_name + ' ' + member.last_name"></p>
                                                <p class="text-gray-500 text-xs mt-0.5" x-text="'Tel: ' + member.tel"></p>
                                            </div>
                                        </template>
                                        <template x-if="!member">
                                            <div>
                                                <p class="font-bold text-gray-800">ลูกค้าทั่วไป</p>
                                                <p class="text-gray-500 text-xs mt-0.5" x-text="'คุณ' + guestName + ' โทร ' + guestPhone"></p>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="text-right text-sm">
                                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">ระยะเวลาเช่า</p>
                                        <p class="text-gray-800"><span x-text="new Date(rentalDate).toLocaleDateString('th-TH', {day:'numeric', month:'short'})"></span> - <span x-text="new Date(returnDate).toLocaleDateString('th-TH', {day:'numeric', month:'short', year:'2-digit'})"></span></p>
                                        <p class="text-xs text-indigo-600 font-medium mt-0.5">7 วัน</p>
                                    </div>
                                </div>
                                <div class="flex justify-between mb-4">
                                    <span class="text-xs text-gray-500 font-bold uppercase">พนักงานขาย</span>
                                    <span class="text-sm text-gray-800" x-text="receiptData.staff_name"></span>
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
                                            <template x-for="item in cart" :key="'r-item-'+item.id">
                                                <tr class="border-b border-gray-50">
                                                    <td class="py-2 pr-2">
                                                        <div class="font-medium" x-text="item.item_name"></div>
                                                        <div class="text-[10px] text-gray-400">รหัส: <span x-text="item.id"></span></div>
                                                    </td>
                                                    <td class="text-center py-2 align-top" x-text="item.quantity"></td>
                                                    <td class="text-right py-2 align-top font-medium" x-text="formatPrice(item.price * item.quantity)"></td>
                                                </tr>
                                            </template>
                                            <template x-for="acc in accessoryCart" :key="'r-acc-'+acc.id">
                                                <tr class="border-b border-gray-50 bg-gray-50/50">
                                                    <td class="py-2 pr-2 pl-2">
                                                        <div class="font-medium text-gray-600" x-text="acc.name"></div>
                                                        <div class="text-[10px] text-orange-400">อุปกรณ์เสริม</div>
                                                    </td>
                                                    <td class="text-center py-2 align-top" x-text="acc.quantity"></td>
                                                    <td class="text-right py-2 align-top text-gray-600" x-text="formatPrice(acc.price * acc.quantity)"></td>
                                                </tr>
                                            </template>
                                            <tr x-show="makeupPrice > 0">
                                                <td class="py-2">แต่งหน้า</td>
                                                <td class="text-center py-2">1</td>
                                                <td class="text-right py-2" x-text="formatPrice(makeupPrice)"></td>
                                            </tr>
                                            <tr x-show="packagePrice > 0">
                                                <td class="py-2">ถ่ายภาพ</td>
                                                <td class="text-center py-2">1</td>
                                                <td class="text-right py-2" x-text="formatPrice(packagePrice)"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- สรุปยอดเงิน --}}
                                <div class="flex justify-end mb-6">
                                    <div class="w-1/2 space-y-2">
                                        <template x-if="discountAmount > 0">
                                            <div class="flex justify-between text-xs text-green-600"><span>ส่วนลดโปรโมชั่น</span><span x-text="'-' + formatPrice(discountAmount)"></span></div>
                                        </template>
                                        <template x-if="pointsDiscountValue > 0">
                                            <div class="flex justify-between text-xs text-indigo-600"><span>ใช้แต้มแลก</span><span x-text="'-' + formatPrice(pointsDiscountValue)"></span></div>
                                        </template>

                                        <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-200">
                                            <span>ยอดสุทธิ</span>
                                            <span x-text="formatPrice(grandTotal)"></span>
                                        </div>
                                        <div class="flex justify-between items-center bg-green-50 p-2 rounded text-xs text-green-700 border border-green-100">
                                            <span>ชำระแล้ว (มัดจำ)</span>
                                            <span class="font-bold" x-text="formatPrice(depositAmount)"></span>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 pt-1">
                                            <span>ค้างชำระ</span>
                                            <span x-text="formatPrice(remainingAmount)"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <div class="text-center pt-4 border-t border-dashed border-gray-200">
                                    <div class="flex justify-center mb-3">
                                        <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${encodeURIComponent('RentalID:' + receiptData.rental_id)}`"
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
                                <button @click="closeReceipt" class="flex-1 py-2.5 bg-gray-900 text-white font-bold rounded-lg shadow hover:bg-black transition">
                                    ปิด / รายการใหม่
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <script>
            function rentalSystem(initData) {
                return {
                    promotions: initData.promotions || [],
                    makeupArtists: initData.makeupArtists || [],
                    packages: initData.packages || [],
                    photographers: initData.photographers || [],
                    accessoriesList: initData.accessoriesData || [],

                    memberQuery: '',
                    member: null,
                    memberError: false,
                    memberErrorMsg: '',
                    isGuest: false,
                    guestName: '',
                    guestPhone: '',
                    pointsToUse: 0,

                    // Main Items
                    itemQuery: '',
                    items: [],
                    showItemsDropdown: false,
                    isLoadingItems: false,

                    // Accessories
                    cart: [],
                    accessoryCart: [],
                    accessoryQuery: '',
                    showAccessoryDropdown: false,

                    selectedPromotionId: '',
                    selectedMakeupId: '',
                    selectedPackageId: '',
                    selectedPhotographerId: '',

                    rentalDate: new Date().toISOString().split('T')[0],
                    // [ปรับแก้] ตรงนี้ก็ต้อง +6 วันเช่นกันเพื่อให้ตรงกับ updateReturnDate
                    returnDate: new Date(Date.now() + 6 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],

                    isSubmitting: false,
                    showConfirm: false,
                    showAlert: false,
                    alertTitle: '',
                    alertMessage: '',
                    alertType: 'success',

                    payment_method: 'cash',
                    showReceipt: false,
                    receiptData: {
                        rental_id: '-',
                        staff_name: '-'
                    },

                    // Delete Modal
                    showDeleteModal: false,
                    deleteType: '', // 'main' or 'acc'
                    deleteIndex: -1,

                    get filteredAccessories() {
                        if (this.accessoryQuery === '') return this.accessoriesList;
                        return this.accessoriesList.filter(acc => acc.name.toLowerCase().includes(this.accessoryQuery.toLowerCase()));
                    },

                    toggleGuest() {
                        if (this.isGuest) {
                            this.member = null;
                            this.memberQuery = '';
                            this.memberError = false;
                        }
                    },
                    resetMember() {
                        this.member = null;
                        this.isGuest = false;
                        this.cart = [];
                        this.accessoryCart = [];
                        this.selectedPromotionId = '';
                        this.selectedMakeupId = '';
                        this.selectedPackageId = '';
                        this.selectedPhotographerId = '';
                        this.guestName = '';
                        this.guestPhone = '';
                        this.pointsToUse = 0;
                    },

                    async checkMember() {
                        if (!this.memberQuery) return;
                        this.memberError = false;
                        try {
                            const res = await fetch(`{{ route('reception.checkMember') }}?q=${this.memberQuery}`);
                            const data = await res.json();
                            if (data.success) {
                                this.member = data.member;
                                this.memberQuery = '';
                            } else {
                                this.member = null;
                                this.memberError = true;
                                this.memberErrorMsg = 'ไม่พบข้อมูลสมาชิกในระบบ';
                            }
                        } catch (e) {
                            this.triggerAlert('Error', 'Connection Error', 'error');
                        }
                    },

                    async searchItems() {
                        this.isLoadingItems = true;
                        this.items = [];
                        try {
                            const params = new URLSearchParams({
                                q: this.itemQuery,
                                rental_date: this.rentalDate,
                                return_date: this.returnDate
                            });
                            const res = await fetch(`{{ route('reception.searchItems') }}?${params.toString()}`);
                            this.items = await res.json();
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.isLoadingItems = false;
                        }
                    },

                    addToCart(item) {
                        let existingItem = this.cart.find(i => i.id === item.id);
                        if (existingItem) {
                            if (existingItem.quantity < item.stock) existingItem.quantity++;
                            else this.triggerAlert('แจ้งเตือน', 'สินค้าหมดสต็อก');
                        } else {
                            if (item.stock > 0) this.cart.push({
                                ...item,
                                quantity: 1
                            });
                            else this.triggerAlert('แจ้งเตือน', 'สินค้าหมดสต็อก');
                        }
                        this.items = [];
                        this.itemQuery = '';
                    },
                    increaseQty(index) {
                        let item = this.cart[index];
                        if (item.quantity < item.stock) item.quantity++;
                        else this.triggerAlert('แจ้งเตือน', 'สินค้าหมดสต็อก');
                    },
                    decreaseQty(index) {
                        let item = this.cart[index];
                        if (item.quantity > 1) item.quantity--;
                        else this.askDeleteItem('main', index);
                    },
                    removeFromCart(index) {
                        this.cart.splice(index, 1);
                    },

                    updateReturnDate() {
                        let d = new Date(this.rentalDate);
                        d.setDate(d.getDate() + 6);
                        this.returnDate = d.toISOString().split('T')[0];
                    },

                    addAccessoryToCart(acc) {
                        if (!acc) return;
                        let existing = this.accessoryCart.find(a => a.id == acc.id);
                        if (existing) {
                            if (existing.quantity < acc.stock) existing.quantity++;
                            else this.triggerAlert('แจ้งเตือน', 'อุปกรณ์เสริมหมดสต็อกแล้ว', 'error');
                        } else {
                            if (acc.stock > 0) this.accessoryCart.push({
                                ...acc,
                                quantity: 1
                            });
                            else this.triggerAlert('แจ้งเตือน', 'อุปกรณ์เสริมหมดสต็อก', 'error');
                        }
                    },
                    increaseAccQty(index) {
                        let acc = this.accessoryCart[index];
                        const original = this.accessoriesList.find(a => a.id == acc.id);
                        if (acc.quantity < original.stock) acc.quantity++;
                        else this.triggerAlert('แจ้งเตือน', 'สินค้าหมดสต็อก', 'error');
                    },
                    decreaseAccQty(index) {
                        let acc = this.accessoryCart[index];
                        if (acc.quantity > 1) acc.quantity--;
                        else this.askDeleteItem('acc', index);
                    },
                    removeAccessory(index) {
                        this.accessoryCart.splice(index, 1);
                    },

                    askDeleteItem(type, index) {
                        this.deleteType = type;
                        this.deleteIndex = index;
                        this.showDeleteModal = true;
                    },
                    confirmDelete() {
                        if (this.deleteType === 'main') this.removeFromCart(this.deleteIndex);
                        else if (this.deleteType === 'acc') this.removeAccessory(this.deleteIndex);
                        this.showDeleteModal = false;
                    },

                    getMakeupName() {
                        const m = this.makeupArtists.find(m => m.makeup_id == this.selectedMakeupId);
                        return m ? m.first_name + ' ' + m.last_name : '';
                    },
                    getPackageName() {
                        const p = this.packages.find(pk => pk.package_id == this.selectedPackageId);
                        return p ? p.package_name : '';
                    },
                    getPromotionName() {
                        const p = this.promotions.find(pr => pr.promotion_id == this.selectedPromotionId);
                        return p ? p.promotion_name : '';
                    },

                    get cartItemCount() {
                        return this.cart.reduce((s, i) => s + i.quantity, 0);
                    },
                    get cartTotal() {
                        return this.cart.reduce((s, i) => s + (parseFloat(i.price) * i.quantity), 0);
                    },
                    get accessoryItemCount() {
                        return this.accessoryCart.reduce((s, a) => s + a.quantity, 0);
                    },
                    get accessoryTotal() {
                        return this.accessoryCart.reduce((s, a) => s + (parseFloat(a.price) * a.quantity), 0);
                    },
                    get makeupPrice() {
                        const m = this.makeupArtists.find(m => m.makeup_id == this.selectedMakeupId);
                        return m ? parseFloat(m.price) : 0;
                    },
                    get packagePrice() {
                        const p = this.packages.find(pk => pk.package_id == this.selectedPackageId);
                        return p ? parseFloat(p.price) : 0;
                    },

                    get discountAmount() {
                        if (!this.selectedPromotionId) return 0;
                        const promo = this.promotions.find(p => p.promotion_id == this.selectedPromotionId);
                        if (!promo) return 0;
                        const itemTotal = this.cartTotal;
                        if (itemTotal === 0) return 0;
                        let discount = 0;
                        if (promo.discount_type === 'percentage') {
                            discount = (itemTotal * parseFloat(promo.discount_value)) / 100;
                        } else {
                            discount = parseFloat(promo.discount_value);
                        }
                        return discount > itemTotal ? itemTotal : discount;
                    },

                    get pointsDiscountValue() {
                        if (!this.pointsToUse || this.pointsToUse < 0) return 0;
                        if (this.member && this.pointsToUse > this.member.points) {
                            this.pointsToUse = this.member.points;
                        }
                        return Math.floor(this.pointsToUse / 100);
                    },

                    get grandTotal() {
                        let total = this.cartTotal + this.accessoryTotal - this.discountAmount + this.makeupPrice + this.packagePrice - this.pointsDiscountValue;
                        return total < 0 ? 0 : total;
                    },
                    get depositAmount() {
                        return this.grandTotal * 0.5;
                    },
                    get remainingAmount() {
                        return this.grandTotal - this.depositAmount;
                    },
                    formatPrice(price) {
                        return new Intl.NumberFormat('th-TH', {
                            style: 'currency',
                            currency: 'THB'
                        }).format(price);
                    },

                    openConfirmModal() {
                        this.showConfirm = true;
                    },
                    triggerAlert(title, message, type = 'success') {
                        this.alertTitle = title;
                        this.alertMessage = message;
                        this.alertType = type;
                        this.showAlert = true;
                    },
                    closeAlert() {
                        this.showAlert = false;
                        if (this.alertType === 'success') window.location.reload();
                    },

                    async processSubmission() {
                        if (this.isGuest && (!this.guestName || !this.guestPhone)) {
                            this.triggerAlert('ข้อมูลไม่ครบ', 'กรุณากรอกชื่อและเบอร์โทรศัพท์ลูกค้า Guest', 'error');
                            return;
                        }
                        this.showConfirm = false;
                        this.isSubmitting = true;
                        try {
                            const res = await fetch(`{{ route('reception.storeRental') }}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    member_id: this.isGuest ? null : this.member?.member_id,
                                    guest_name: this.guestName,
                                    guest_phone: this.guestPhone,
                                    payment_method: this.payment_method,
                                    rental_date: this.rentalDate,
                                    return_date: this.returnDate,
                                    items: this.cart,
                                    accessories: this.accessoryCart,
                                    promotion_id: this.selectedPromotionId,
                                    makeup_id: this.selectedMakeupId,
                                    package_id: this.selectedPackageId,
                                    photographer_id: this.selectedPhotographerId,
                                    total_amount: this.grandTotal,
                                    deposit_amount: this.depositAmount,
                                    remaining_amount: this.remainingAmount,
                                    points_used: this.pointsToUse,
                                    is_deposit_paid: true
                                })
                            });
                            const result = await res.json();
                            if (result.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'จองสำเร็จ!',
                                    text: 'กำลังนำทางไปหน้ายืนยันการชำระเงิน...',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = result.redirect_url;
                                });
                            } else {
                                this.triggerAlert('เกิดข้อผิดพลาด', JSON.stringify(result.errors || result.message), 'error');
                            }
                        } catch (e) {
                            console.error(e);
                            this.triggerAlert('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                        } finally {
                            this.isSubmitting = false;
                        }
                    },
                    closeReceipt() {
                        this.showReceipt = false;
                        window.location.reload();
                    }
                }
            }
        </script>
</x-app-layout>