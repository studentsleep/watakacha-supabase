<x-app-layout>
    <style>
        @media print {
            body * { visibility: hidden; }
            #receipt-modal, #receipt-modal * { visibility: visible; }
            #receipt-modal {
                position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0;
                background: white; box-shadow: none !important;
            }
            .no-print, button { display: none !important; }
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            บริการเช่าชุด (Rental Service)
        </h2>
    </x-slot>

    <div class="py-6" x-data="rentalSystem({
        promotions: {{ Js::from($promotions) }},
        makeupArtists: {{ Js::from($makeup_artists) }},
        packages: {{ Js::from($photo_packages) }},
        photographers: {{ Js::from($photographers) }},
        accessoriesData: {{ Js::from($accessories) }}
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT COLUMN --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- 1. Member Info --}}
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                                <span class="bg-gray-200 text-gray-700 w-8 h-8 rounded-full flex items-center justify-center text-sm shadow-inner">1</span>
                                ข้อมูลสมาชิก
                            </h3>
                            <label class="flex items-center space-x-2 text-sm cursor-pointer select-none group bg-white px-3 py-1.5 rounded-full border border-gray-200 shadow-sm hover:bg-gray-100 transition">
                                <input type="checkbox" x-model="isGuest" @change="toggleGuest" class="rounded border-gray-300 text-gray-600 shadow-sm focus:ring-gray-500">
                                <span class="text-gray-600 font-semibold group-hover:text-gray-800 transition">ลูกค้าทั่วไป (Guest)</span>
                            </label>
                        </div>

                        {{-- Search --}}
                        <div x-show="!isGuest && !member" class="flex gap-2 transition-all duration-300">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                </div>
                                <input type="text" x-model="memberQuery" @keydown.enter.prevent="checkMember" class="w-full pl-10 rounded-lg border-gray-300 bg-white shadow-sm focus:border-gray-500 focus:ring-gray-500 text-gray-800 placeholder-gray-400" placeholder="เบอร์โทร, รหัสสมาชิก หรือชื่อ...">
                            </div>
                            <button @click="checkMember" class="px-5 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 shadow transition font-medium">ค้นหา</button>
                        </div>
                        <p x-show="memberError" class="text-red-600 text-sm mt-2 flex items-center gap-1 bg-red-50 p-2 rounded border border-red-100 font-medium">
                            <span x-text="memberErrorMsg"></span>
                        </p>

                        {{-- Member Card --}}
                        <div x-show="member && !isGuest" x-transition class="mt-4 p-4 rounded-xl border flex items-start gap-4 shadow-sm bg-white border-green-200">
                            <div class="p-3 rounded-full shrink-0 bg-green-100 text-green-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                            </div>
                            <div class="flex-grow">
                                <h4 class="font-bold text-lg text-gray-800" x-text="member?.first_name + ' ' + member?.last_name"></h4>
                                <div class="text-sm text-gray-600 mt-1 space-y-1">
                                    <p>ID: <span class="font-mono text-gray-800 font-bold" x-text="member?.member_id"></span> | Tel: <span x-text="member?.tel"></span></p>
                                    <p class="inline-flex items-center gap-1 bg-green-50 px-2 py-0.5 rounded text-xs font-bold text-green-800 border border-green-100">Points: <span x-text="member?.points"></span></p>
                                </div>
                            </div>
                            <button @click="resetMember" class="text-gray-400 hover:text-red-500 transition p-2 hover:bg-gray-100 rounded-full">✕</button>
                        </div>

                        {{-- Guest Card --}}
                        <div x-show="isGuest" x-transition class="mt-4 p-4 rounded-xl border flex flex-col gap-3 shadow-sm bg-white border-gray-300">
                            <div class="flex items-start gap-4">
                                <div class="p-3 rounded-full shrink-0 bg-gray-200 text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-bold text-lg text-gray-800">ลูกค้าทั่วไป (Guest)</h4>
                                    <p class="text-xs text-gray-500 mb-2">กรุณากรอกข้อมูลติดต่อ (สำหรับบันทึกและใบเสร็จ)</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <input type="text" x-model="guestName" class="text-sm rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500 w-full" placeholder="ชื่อลูกค้า (เช่น คุณสมชาย)">
                                        <input type="text" x-model="guestPhone" class="text-sm rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500 w-full" placeholder="เบอร์โทรศัพท์">
                                    </div>
                                </div>
                                <button @click="resetMember" class="text-gray-400 hover:text-red-500 transition p-2 hover:bg-gray-100 rounded-full">✕</button>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Items Selection --}}
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-opacity duration-200"
                         :class="(member || isGuest) ? 'opacity-100' : 'opacity-50 pointer-events-none'">
                        
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                                <span class="bg-gray-200 text-gray-700 w-8 h-8 rounded-full flex items-center justify-center text-sm shadow-inner">2</span>
                                เลือกชุดและอุปกรณ์
                            </h3>
                        </div>

                        {{-- 2.1 Main Items --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2 border-l-4 border-blue-500 pl-2">ชุดและสินค้าหลัก</label>
                            <div class="relative">
                                <input type="text" x-model="itemQuery" @input.debounce.300ms="searchItems" @focus="showItemsDropdown = true; searchItems()" @click.away="showItemsDropdown = false"
                                       class="w-full pl-4 pr-10 py-2.5 rounded-lg border-gray-300 bg-white shadow-sm focus:border-gray-500 focus:ring-gray-500 placeholder-gray-400 text-gray-800" 
                                       placeholder="พิมพ์ชื่อชุด... (คลิกเพื่อดูรายการแนะนำ)">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <span x-show="!isLoadingItems">🔍</span>
                                    <span x-show="isLoadingItems" class="animate-spin">⏳</span>
                                </div>
                                <div x-show="showItemsDropdown" class="absolute z-20 w-full bg-white border border-gray-200 mt-2 rounded-lg shadow-xl max-h-60 overflow-y-auto" style="display: none;">
                                    <div x-show="isLoadingItems" class="px-4 py-3 text-center text-gray-500 text-sm">กำลังค้นหาข้อมูล...</div>
                                    <ul x-show="!isLoadingItems && items.length > 0">
                                        <template x-for="item in items" :key="item.id">
                                            <li @click="addToCart(item); showItemsDropdown = false" class="px-4 py-3 hover:bg-gray-100 cursor-pointer flex justify-between items-center border-b border-gray-100 last:border-0 transition group">
                                                <div>
                                                    <span class="font-bold text-gray-700 group-hover:text-gray-900 block" x-text="item.item_name"></span>
                                                    <span class="text-xs text-gray-500" x-text="'ID: ' + item.id + ' | Stock: ' + item.stock"></span>
                                                </div>
                                                <span class="text-gray-600 font-bold bg-gray-200 px-2 py-1 rounded text-sm" x-text="formatPrice(item.price)"></span>
                                            </li>
                                        </template>
                                    </ul>
                                    <div x-show="!isLoadingItems && items.length === 0" class="px-4 py-3 text-center text-gray-400 text-sm">ไม่พบสินค้า หรือสินค้าหมดช่วงนี้</div>
                                </div>
                            </div>
                        </div>

                        {{-- 2.2 Accessories --}}
                        <div class="mb-6 bg-orange-50 p-4 rounded-lg border border-orange-200">
                            <label class="block text-sm font-bold text-orange-800 mb-2 flex items-center gap-2">🎧 เพิ่มอุปกรณ์เสริม (Accessories)</label>
                            <div class="relative">
                                <input type="text" x-model="accessoryQuery" @focus="showAccessoryDropdown = true" @click.away="showAccessoryDropdown = false"
                                       class="w-full pl-4 pr-10 py-2.5 rounded-lg border-orange-300 focus:border-orange-500 focus:ring-orange-500 text-gray-700 placeholder-orange-300"
                                       placeholder="พิมพ์ค้นหา หรือเลื่อนดูอุปกรณ์เสริม...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-orange-400">🔍</div>
                                <div x-show="showAccessoryDropdown" class="absolute z-20 w-full bg-white border border-gray-200 mt-2 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                    <ul>
                                        <template x-for="acc in filteredAccessories" :key="acc.id">
                                            <li @click="addAccessoryToCart(acc); showAccessoryDropdown = false; accessoryQuery = ''" class="px-4 py-3 hover:bg-orange-50 cursor-pointer flex justify-between items-center border-b border-gray-100 last:border-0 transition group">
                                                <div>
                                                    <span class="font-bold text-gray-700 group-hover:text-orange-700 block" x-text="acc.name"></span>
                                                    <span class="text-xs text-gray-500" x-text="'Stock: ' + acc.stock"></span>
                                                </div>
                                                <span class="text-gray-600 font-bold bg-gray-100 px-2 py-1 rounded text-sm" x-text="formatPrice(acc.price)"></span>
                                            </li>
                                        </template>
                                        <li x-show="filteredAccessories.length === 0" class="px-4 py-3 text-gray-400 text-center text-sm">ไม่พบข้อมูลอุปกรณ์เสริม</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Cart Table --}}
                        <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">รายการ</th>
                                        <th class="px-4 py-3 text-right font-semibold">ราคา</th>
                                        <th class="px-4 py-3 text-center font-semibold">จำนวน</th>
                                        <th class="px-4 py-3 text-right font-semibold">รวม</th>
                                        <th class="px-4 py-3 text-center w-16">ลบ</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, index) in cart" :key="'item-'+item.id">
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-800" x-text="item.item_name"></div>
                                                <span class="text-[10px] bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded border border-blue-200">ชุดหลัก</span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono" x-text="formatPrice(item.price)"></td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button @click="decreaseQty(index)" class="w-6 h-6 rounded bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-700 font-bold">-</button>
                                                    <span class="w-6 text-center font-bold text-gray-800" x-text="item.quantity"></span>
                                                    <button @click="increaseQty(index)" class="w-6 h-6 rounded bg-gray-800 hover:bg-gray-700 flex items-center justify-center text-white font-bold">+</button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold" x-text="formatPrice(item.price * item.quantity)"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button @click="askDeleteItem('main', index)" class="text-red-400 hover:text-red-600 p-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
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
                                                <div class="flex items-center justify-center gap-2">
                                                    <button @click="decreaseAccQty(index)" class="w-6 h-6 rounded bg-orange-200 hover:bg-orange-300 flex items-center justify-center text-orange-800 font-bold">-</button>
                                                    <span class="w-6 text-center font-bold text-gray-800" x-text="acc.quantity"></span>
                                                    <button @click="increaseAccQty(index)" class="w-6 h-6 rounded bg-orange-500 hover:bg-orange-600 flex items-center justify-center text-white font-bold">+</button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold" x-text="formatPrice(acc.price * acc.quantity)"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button @click="askDeleteItem('acc', index)" class="text-red-400 hover:text-red-600 p-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="cart.length === 0 && accessoryCart.length === 0">
                                        <td colspan="5" class="px-4 py-10 text-center text-gray-400"><span class="font-medium">ยังไม่มีสินค้าในตะกร้า</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 3. Extra Services --}}
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-opacity duration-200"
                         :class="(cart.length > 0 || accessoryCart.length > 0) ? 'opacity-100' : 'opacity-50 pointer-events-none'">
                        
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                                <span class="bg-gray-200 text-gray-700 w-8 h-8 rounded-full flex items-center justify-center text-sm shadow-inner">3</span>
                                บริการเสริมอื่นๆ & โปรโมชั่น
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2 bg-yellow-50 p-5 rounded-xl border border-yellow-200">
                                <x-input-label value="เลือกโปรโมชั่น (Promotion)" class="mb-2 text-yellow-800 font-bold" />
                                <div class="relative">
                                    <select x-model="selectedPromotionId" class="w-full rounded-lg border-yellow-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 bg-white text-gray-700 py-2.5">
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
                                <x-input-label value="บริการช่างแต่งหน้า" class="text-gray-600 font-medium" />
                                <select x-model="selectedMakeupId" class="w-full mt-2 rounded-lg border-gray-300 bg-white shadow-sm focus:border-gray-500 focus:ring-gray-500 text-gray-700">
                                    <option value="">-- ไม่รับบริการ --</option>
                                    <template x-for="artist in makeupArtists" :key="artist.makeup_id">
                                        <option :value="artist.makeup_id" x-text="artist.first_name + ' ' + artist.last_name + ' (' + formatPrice(artist.price) + ')'"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <x-input-label value="แพ็กเกจถ่ายภาพ" class="text-gray-600 font-medium" />
                                <select x-model="selectedPackageId" class="w-full mt-2 rounded-lg border-gray-300 bg-white shadow-sm focus:border-gray-500 focus:ring-gray-500 text-gray-700">
                                    <option value="">-- ไม่รับบริการ --</option>
                                    <template x-for="pkg in packages" :key="pkg.package_id">
                                        <option :value="pkg.package_id" x-text="pkg.package_name + ' (' + formatPrice(pkg.price) + ')'"></option>
                                    </template>
                                </select>
                                <div x-show="selectedPackageId" x-transition class="mt-3 bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                                    <x-input-label value="ระบุช่างภาพ" class="text-xs text-gray-500 mb-1" />
                                    <select x-model="selectedPhotographerId" class="w-full text-sm rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-gray-700">
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

                {{-- RIGHT COLUMN: Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-200 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-5 border-b flex items-center gap-2">
                            <span class="bg-gray-200 p-1.5 rounded text-gray-600">🧾</span> สรุปรายการเช่า
                        </h3>
                        
                        <div class="space-y-5">
                            <div class="bg-white p-4 rounded-lg border border-gray-200 space-y-3 shadow-sm">
                                <div><span class="text-xs text-gray-500 font-bold uppercase tracking-wider">วันที่เช่า</span><input type="date" x-model="rentalDate" @change="updateReturnDate" class="block w-full mt-1 rounded-md border-gray-300 text-sm"></div>
                                <div><span class="text-xs text-gray-500 font-bold uppercase tracking-wider">กำหนดคืน</span><input type="date" x-model="returnDate" class="block w-full mt-1 rounded-md border-gray-300 text-sm"></div>
                            </div>

                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <span class="text-xs text-gray-500 font-bold uppercase tracking-wider block mb-1">วิธีชำระเงินมัดจำ</span>
                                <select x-model="payment_method" class="block w-full rounded-md border-gray-300 text-sm focus:border-gray-500 focus:ring-gray-500">
                                    <option value="cash">💵 เงินสด (Cash)</option>
                                    <option value="transfer">📱 โอนเงิน (Transfer)</option>
                                    <option value="credit_card">💳 บัตรเครดิต (Credit Card)</option>
                                </select>
                            </div>

                            <div class="text-sm space-y-3 text-gray-600 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex justify-between items-center py-1"><span>ค่าชุดหลัก <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full border border-gray-200" x-text="cartItemCount"></span></span><span class="font-bold text-gray-800" x-text="formatPrice(cartTotal)"></span></div>
                                <div class="flex justify-between items-center py-1" x-show="accessoryCart.length > 0"><span>อุปกรณ์เสริม <span class="text-xs bg-orange-100 text-orange-500 px-2 py-0.5 rounded-full border border-orange-200" x-text="accessoryItemCount"></span></span><span class="font-bold text-gray-800" x-text="formatPrice(accessoryTotal)"></span></div>
                                <template x-if="makeupPrice > 0"><div class="flex justify-between items-center bg-gray-50 p-2.5 rounded-lg border border-gray-200"><div class="flex flex-col"><span class="font-semibold text-gray-700">ค่าแต่งหน้า</span><span class="text-xs text-gray-500" x-text="getMakeupName()"></span></div><span class="font-bold text-gray-700" x-text="formatPrice(makeupPrice)"></span></div></template>
                                <template x-if="packagePrice > 0"><div class="flex justify-between items-center bg-gray-50 p-2.5 rounded-lg border border-gray-200"><div class="flex flex-col"><span class="font-semibold text-gray-700">ค่าถ่ายภาพ</span><span class="text-xs text-gray-500" x-text="getPackageName()"></span></div><span class="font-bold text-gray-700" x-text="formatPrice(packagePrice)"></span></div></template>
                                <template x-if="discountAmount > 0"><div class="flex justify-between items-center bg-green-50 p-2.5 rounded-lg border border-green-100"><div class="flex flex-col"><span class="font-bold text-green-700 flex items-center gap-1">🏷️ ส่วนลด</span><span class="text-xs text-green-600" x-text="getPromotionName()"></span></div><span class="font-bold text-green-700" x-text="'-' + formatPrice(discountAmount)"></span></div></template>
                            </div>

                            <div class="border-t-2 border-dashed border-gray-300 pt-4 mt-2 space-y-3">
                                <div class="flex justify-between items-end"><span class="text-gray-500 font-medium pb-1">ยอดรวมทั้งสิ้น</span><span class="text-xl font-bold text-gray-800" x-text="formatPrice(grandTotal)"></span></div>
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200"><div class="flex justify-between items-center mb-1"><span class="text-blue-800 font-bold flex items-center gap-2">มัดจำ (50%)</span><span class="text-2xl font-extrabold text-blue-700" x-text="formatPrice(depositAmount)"></span></div></div>
                            </div>

                            <div class="pt-2">
                                <button @click="openConfirmModal" class="w-full py-3.5 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl shadow-lg transform transition hover:-translate-y-0.5 flex justify-center items-center gap-2" :disabled="(!member && !isGuest) || (cart.length === 0 && accessoryCart.length === 0) || isSubmitting"><span x-text="isSubmitting ? 'กำลังบันทึก...' : 'ชำระมัดจำ & ยืนยันการเช่า'"></span></button>
                            </div>
                        </div>
                    </div>
                </div> {{-- END RIGHT COLUMN --}}
            </div>

            {{-- MODALS --}}
            <div x-show="showConfirm" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 text-center"><div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50" @click="showConfirm = false"></div><div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50"><h3 class="text-xl font-bold leading-6 text-gray-900 mb-4">ยืนยันการเช่า?</h3><div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4 space-y-2"><div class="flex justify-between text-gray-600"><span>ยอดรวม:</span><span class="font-bold" x-text="formatPrice(grandTotal)"></span></div><div class="flex justify-between text-blue-700 font-bold text-lg border-t pt-2 border-gray-200"><span>มัดจำที่ต้องจ่าย:</span><span x-text="formatPrice(depositAmount)"></span></div></div><div class="mt-6 flex justify-end gap-3"><button @click="showConfirm = false" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">ยกเลิก</button><button @click="processSubmission" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-bold shadow">ยืนยันและบันทึก</button></div></div></div>
            </div>

            <div x-show="showAlert" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 text-center"><div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50"></div><div class="inline-block w-full max-w-sm p-6 my-8 overflow-hidden text-center align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50"><div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" :class="alertType === 'success' ? 'bg-green-100' : 'bg-red-100'"><span x-show="alertType === 'success'" class="text-2xl">✅</span><span x-show="alertType === 'error'" class="text-2xl">❌</span></div><h3 class="text-lg font-medium leading-6 text-gray-900" x-text="alertTitle"></h3><div class="mt-2"><p class="text-sm text-gray-500" x-text="alertMessage"></p></div><div class="mt-5"><button @click="closeAlert" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-gray-800 border border-transparent rounded-md shadow-sm hover:bg-gray-700 focus:outline-none sm:text-sm">ตกลง</button></div></div></div>
            </div>

            <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 text-center"><div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50" @click="showDeleteModal = false"></div><div class="inline-block w-full max-w-sm p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative z-50"><div class="flex items-center gap-3 mb-4 text-red-600"><div class="bg-red-100 p-2 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></div><h3 class="text-lg font-bold">ยืนยันการลบ?</h3></div><p class="text-gray-600 mb-6">คุณต้องการลบรายการนี้ออกจากตะกร้าใช่หรือไม่?</p><div class="flex justify-end gap-3"><button @click="showDeleteModal = false" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">ยกเลิก</button><button @click="confirmDelete" class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 font-bold">ลบรายการ</button></div></div></div>
            </div>

            {{-- 🟢 MODAL 4: ใบเสร็จ (Receipt / Slip) --}}
            <div x-show="showReceipt" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-70" @click="closeReceipt"></div>
                    <div id="receipt-modal" class="inline-block w-full max-w-sm overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-sm relative z-[70] my-8 font-mono text-sm">
                        <div class="bg-gray-800 text-white p-6 text-center relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
                            <h2 class="text-2xl font-bold tracking-wider uppercase mb-1">ใบเสร็จรับเงิน</h2>
                            <p class="text-gray-400 text-xs tracking-widest">RENTAL RECEIPT</p>
                            <div class="mt-4 border-t border-gray-600 pt-4">
                                <h3 class="text-lg font-semibold text-white">ร้านเช่าชุด Watakacha</h3>
                                <p class="text-gray-400 text-xs">สาขาลำพูน โทร. 081-234-5678</p>
                            </div>
                        </div>
                        <div class="p-6 bg-white relative">
                            <div class="absolute top-0 left-0 w-full -mt-2 h-4 bg-repeat-x text-white" style="background-image: radial-gradient(circle, transparent 25%, currentColor 26%); background-size: 10px 10px; height: 5px;"></div>
                            <div class="space-y-2 mb-4 border-b border-dashed border-gray-300 pb-4">
                                <div class="flex justify-between"><span class="text-gray-500">เลขที่รายการ:</span><span class="font-bold text-gray-800">#<span x-text="receiptData.rental_id"></span></span></div>
                                <div class="flex justify-between"><span class="text-gray-500">วันที่:</span><span class="text-gray-800" x-text="new Date().toLocaleString('th-TH')"></span></div>
                                <div class="flex justify-between items-start">
                                    <span class="text-gray-500 shrink-0">ลูกค้า:</span>
                                    <div class="text-right">
                                        <template x-if="member"><div><span class="font-bold text-gray-800 block" x-text="member.first_name + ' ' + member.last_name"></span><span class="text-gray-500 text-[10px] block" x-text="'Tel: ' + member.tel"></span></div></template>
                                        <template x-if="!member"><div><span class="font-bold text-gray-800 block">ลูกค้าทั่วไป (Guest)</span><span class="text-gray-600 text-[10px] block" x-text="'คุณ' + guestName + ' โทร ' + guestPhone"></span></div></template>
                                    </div>
                                </div>
                                <div class="flex justify-between"><span class="text-gray-500">พนักงานขาย:</span><span class="text-gray-800" x-text="receiptData.staff_name"></span></div>
                            </div>
                            <div class="mb-4 border-b border-dashed border-gray-300 pb-4">
                                <table class="w-full text-xs">
                                    <thead><tr class="text-gray-500 border-b border-gray-100"><th class="text-left py-1">รายการ</th><th class="text-center py-1">จำนวน</th><th class="text-right py-1">รวม</th></tr></thead>
                                    <tbody class="text-gray-700">
                                        <template x-for="item in cart" :key="'r-item-'+item.id"><tr><td class="py-1" x-text="item.item_name"></td><td class="text-center py-1" x-text="item.quantity"></td><td class="text-right py-1" x-text="formatPrice(item.price * item.quantity)"></td></tr></template>
                                        <template x-for="acc in accessoryCart" :key="'r-acc-'+acc.id"><tr><td class="py-1" x-text="acc.name + ' (Acc)'"></td><td class="text-center py-1" x-text="acc.quantity"></td><td class="text-right py-1" x-text="formatPrice(acc.price * acc.quantity)"></td></tr></template>
                                        <tr x-show="makeupPrice > 0"><td class="py-1">แต่งหน้า</td><td class="text-center py-1">1</td><td class="text-right py-1" x-text="formatPrice(makeupPrice)"></td></tr>
                                        <tr x-show="packagePrice > 0"><td class="py-1">ถ่ายภาพ</td><td class="text-center py-1">1</td><td class="text-right py-1" x-text="formatPrice(packagePrice)"></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="space-y-2 text-xs mb-6">
                                <div class="flex justify-between text-gray-500" x-show="discountAmount > 0"><span>ส่วนลด</span><span class="text-red-500" x-text="'-' + formatPrice(discountAmount)"></span></div>
                                <div class="flex justify-between text-lg font-bold text-gray-900 border-t border-gray-200 pt-2"><span>ยอดรวมทั้งสิ้น</span><span x-text="formatPrice(grandTotal)"></span></div>
                                <div class="flex justify-between items-center bg-gray-100 p-2 rounded"><span class="font-bold text-gray-600">ชำระมัดจำ (Paid)</span><span class="font-bold text-gray-900 text-base" x-text="formatPrice(depositAmount)"></span></div>
                                <div class="flex justify-between text-gray-500 pt-1"><span>ค้างชำระ (Balance)</span><span x-text="formatPrice(remainingAmount)"></span></div>
                                <div class="flex justify-between text-gray-400 text-[10px] mt-1"><span>ช่องทางชำระ:</span><span class="uppercase" x-text="payment_method"></span></div>
                            </div>
                            <div class="text-center border-t border-dashed border-gray-300 pt-6">
                                <div class="flex justify-center mb-4"><img :src="`https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent('RentalID:' + receiptData.rental_id)}`" alt="QR Code" class="w-24 h-24 mix-blend-multiply opacity-80"></div>
                                <p class="font-bold text-gray-800 text-xs">ขอบคุณที่ใช้บริการ</p>
                            </div>
                        </div>
                        <div class="bg-gray-100 p-4 border-t border-gray-200 no-print flex gap-3">
                            <button @click="window.print()" class="flex-1 py-3 bg-white hover:bg-gray-50 text-gray-800 font-bold rounded border border-gray-300 shadow-sm transition text-xs uppercase flex items-center justify-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg> พิมพ์ใบเสร็จ</button>
                            <button @click="closeReceipt" class="flex-1 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded shadow-lg transition text-xs uppercase">ปิด / รายการใหม่</button>
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
                returnDate: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],

                isSubmitting: false,
                showConfirm: false,
                showAlert: false,
                alertTitle: '',
                alertMessage: '',
                alertType: 'success',
                
                payment_method: 'cash',
                showReceipt: false,
                receiptData: { rental_id: '-', staff_name: '-' },

                // Delete Modal
                showDeleteModal: false,
                deleteType: '', // 'main' or 'acc'
                deleteIndex: -1,

                get filteredAccessories() {
                    if (this.accessoryQuery === '') return this.accessoriesList;
                    return this.accessoriesList.filter(acc => acc.name.toLowerCase().includes(this.accessoryQuery.toLowerCase()));
                },

                toggleGuest() { if (this.isGuest) { this.member = null; this.memberQuery = ''; this.memberError = false; } },
                resetMember() { this.member = null; this.isGuest = false; this.cart = []; this.accessoryCart = []; this.selectedPromotionId = ''; this.selectedMakeupId = ''; this.selectedPackageId = ''; this.selectedPhotographerId = ''; this.guestName = ''; this.guestPhone = ''; },
                
                async checkMember() { 
                    if(!this.memberQuery) return;
                    this.memberError = false;
                    try {
                        const res = await fetch(`{{ route('reception.checkMember') }}?q=${this.memberQuery}`);
                        const data = await res.json();
                        if(data.success) { this.member = data.member; this.memberQuery = ''; } 
                        else { this.member = null; this.memberError = true; this.memberErrorMsg = 'ไม่พบข้อมูลสมาชิกในระบบ'; }
                    } catch (e) { this.triggerAlert('Error', 'Connection Error', 'error'); }
                },

                async searchItems() { 
                    this.isLoadingItems = true; this.items = [];
                    try {
                        const params = new URLSearchParams({ q: this.itemQuery, rental_date: this.rentalDate, return_date: this.returnDate });
                        const res = await fetch(`{{ route('reception.searchItems') }}?${params.toString()}`);
                        this.items = await res.json();
                    } catch (e) { console.error(e); } finally { this.isLoadingItems = false; }
                },

                addToCart(item) { 
                    let existingItem = this.cart.find(i => i.id === item.id);
                    if (existingItem) { if (existingItem.quantity < item.stock) existingItem.quantity++; else this.triggerAlert('แจ้งเตือน', 'สินค้าหมดสต็อก'); } 
                    else { if (item.stock > 0) this.cart.push({ ...item, quantity: 1 }); else this.triggerAlert('แจ้งเตือน', 'สินค้าหมดสต็อก'); }
                    this.items = []; this.itemQuery = '';
                },
                increaseQty(index) { let item = this.cart[index]; if (item.quantity < item.stock) item.quantity++; else this.triggerAlert('แจ้งเตือน', 'สินค้าหมดสต็อก'); },
                decreaseQty(index) { let item = this.cart[index]; if (item.quantity > 1) item.quantity--; else this.askDeleteItem('main', index); },
                removeFromCart(index) { this.cart.splice(index, 1); },

                updateReturnDate() { let d = new Date(this.rentalDate); d.setDate(d.getDate() + 7); this.returnDate = d.toISOString().split('T')[0]; },

                addAccessoryToCart(acc) {
                    if (!acc) return;
                    let existing = this.accessoryCart.find(a => a.id == acc.id);
                    if (existing) { if (existing.quantity < acc.stock) existing.quantity++; else this.triggerAlert('แจ้งเตือน', 'อุปกรณ์เสริมหมดสต็อกแล้ว', 'error'); } 
                    else { if (acc.stock > 0) this.accessoryCart.push({ ...acc, quantity: 1 }); else this.triggerAlert('แจ้งเตือน', 'อุปกรณ์เสริมหมดสต็อก', 'error'); }
                },
                increaseAccQty(index) {
                    let acc = this.accessoryCart[index];
                    const original = this.accessoriesList.find(a => a.id == acc.id);
                    if (acc.quantity < original.stock) acc.quantity++; else this.triggerAlert('แจ้งเตือน', 'สินค้าหมดสต็อก', 'error');
                },
                decreaseAccQty(index) {
                    let acc = this.accessoryCart[index];
                    if (acc.quantity > 1) acc.quantity--; else this.askDeleteItem('acc', index);
                },
                removeAccessory(index) { this.accessoryCart.splice(index, 1); },

                askDeleteItem(type, index) { this.deleteType = type; this.deleteIndex = index; this.showDeleteModal = true; },
                confirmDelete() { if (this.deleteType === 'main') this.removeFromCart(this.deleteIndex); else if (this.deleteType === 'acc') this.removeAccessory(this.deleteIndex); this.showDeleteModal = false; },

                getMakeupName() { const m = this.makeupArtists.find(m => m.makeup_id == this.selectedMakeupId); return m ? m.first_name + ' ' + m.last_name : ''; },
                getPackageName() { const p = this.packages.find(pk => pk.package_id == this.selectedPackageId); return p ? p.package_name : ''; },
                getPromotionName() { const p = this.promotions.find(pr => pr.promotion_id == this.selectedPromotionId); return p ? p.promotion_name : ''; },

                get cartItemCount() { return this.cart.reduce((s, i) => s + i.quantity, 0); },
                get cartTotal() { return this.cart.reduce((s, i) => s + (parseFloat(i.price) * i.quantity), 0); },
                get accessoryItemCount() { return this.accessoryCart.reduce((s, a) => s + a.quantity, 0); },
                get accessoryTotal() { return this.accessoryCart.reduce((s, a) => s + (parseFloat(a.price) * a.quantity), 0); },
                get makeupPrice() { const m = this.makeupArtists.find(m => m.makeup_id == this.selectedMakeupId); return m ? parseFloat(m.price) : 0; },
                get packagePrice() { const p = this.packages.find(pk => pk.package_id == this.selectedPackageId); return p ? parseFloat(p.price) : 0; },

                get discountAmount() {
                    if (!this.selectedPromotionId) return 0;
                    const promo = this.promotions.find(p => p.promotion_id == this.selectedPromotionId);
                    if (!promo) return 0;
                    const itemTotal = this.cartTotal;
                    if (itemTotal === 0) return 0;
                    let discount = 0;
                    if (promo.discount_type === 'percentage') { discount = (itemTotal * parseFloat(promo.discount_value)) / 100; } 
                    else { discount = parseFloat(promo.discount_value); }
                    return discount > itemTotal ? itemTotal : discount;
                },

                get grandTotal() { let total = this.cartTotal + this.accessoryTotal - this.discountAmount + this.makeupPrice + this.packagePrice; return total < 0 ? 0 : total; },
                get depositAmount() { return this.grandTotal * 0.5; },
                get remainingAmount() { return this.grandTotal - this.depositAmount; },
                formatPrice(price) { return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(price); },

                openConfirmModal() { this.showConfirm = true; },
                triggerAlert(title, message, type = 'success') { this.alertTitle = title; this.alertMessage = message; this.alertType = type; this.showAlert = true; },
                closeAlert() { this.showAlert = false; if (this.alertType === 'success') window.location.reload(); },

                async processSubmission() {
                    if (this.isGuest && (!this.guestName || !this.guestPhone)) { this.triggerAlert('ข้อมูลไม่ครบ', 'กรุณากรอกชื่อและเบอร์โทรศัพท์ลูกค้า Guest', 'error'); return; }
                    this.showConfirm = false; this.isSubmitting = true;
                    try {
                        const res = await fetch(`{{ route('reception.storeRental') }}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                member_id: this.isGuest ? null : this.member?.member_id,
                                guest_name: this.guestName, guest_phone: this.guestPhone,
                                payment_method: this.payment_method, rental_date: this.rentalDate, return_date: this.returnDate,
                                items: this.cart, accessories: this.accessoryCart,
                                promotion_id: this.selectedPromotionId, makeup_id: this.selectedMakeupId,
                                package_id: this.selectedPackageId, photographer_id: this.selectedPhotographerId,
                                total_amount: this.grandTotal, deposit_amount: this.depositAmount,
                                remaining_amount: this.remainingAmount, is_deposit_paid: true
                            })
                        });
                        const result = await res.json();
                        if (result.success) { 
                            this.receiptData.rental_id = result.rental_id;
                            this.receiptData.staff_name = result.staff_name || 'Admin';
                            this.showReceipt = true; 
                        } else { this.triggerAlert('เกิดข้อผิดพลาด', JSON.stringify(result.errors || result.message), 'error'); }
                    } catch (e) { console.error(e); this.triggerAlert('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error'); } 
                    finally { this.isSubmitting = false; }
                },
                closeReceipt() { this.showReceipt = false; window.location.reload(); }
            }
        }
    </script>
</x-app-layout>