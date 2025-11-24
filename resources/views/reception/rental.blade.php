<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            บริการเช่าชุด (Rental Service)
        </h2>
    </x-slot>

    {{-- Data Injection: ส่งข้อมูลจาก PHP เข้าสู่ Alpine.js --}}
    <div class="py-6" x-data="rentalSystem({
        promotions: {{ Js::from($promotions) }},
        makeupArtists: {{ Js::from($makeup_artists) }},
        packages: {{ Js::from($photo_packages) }},
        photographers: {{ Js::from($photographers) }}
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- LEFT COLUMN: Forms --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- 1. ส่วนข้อมูลสมาชิก --}}
                    {{-- ปรับพื้นหลังเป็นสีเทาอ่อน bg-gray-50 --}}
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
                        
                        {{-- ช่องค้นหา --}}
                        <div x-show="!isGuest && !member" class="flex gap-2 transition-all duration-300">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                {{-- Input พื้นขาวเพื่อให้ตัดกับพื้นเทา --}}
                                <input type="text" 
                                       x-model="memberQuery" 
                                       @keydown.enter.prevent="checkMember" 
                                       class="w-full pl-10 rounded-lg border-gray-300 bg-white shadow-sm focus:border-gray-500 focus:ring-gray-500 text-gray-800 placeholder-gray-400" 
                                       placeholder="เบอร์โทร, รหัสสมาชิก หรือชื่อ...">
                            </div>
                            <button @click="checkMember" class="px-5 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 shadow transition font-medium">
                                ค้นหา
                            </button>
                        </div>
                        <p x-show="memberError" class="text-red-600 text-sm mt-2 flex items-center gap-1 bg-red-50 p-2 rounded border border-red-100 font-medium">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i> <span x-text="memberErrorMsg"></span>
                        </p>

                        {{-- Member Card: กรณีเจอสมาชิก --}}
                        <div x-show="member && !isGuest" x-transition class="mt-4 p-4 rounded-xl border flex items-start gap-4 shadow-sm bg-white border-green-200">
                            <div class="p-3 rounded-full shrink-0 bg-green-100 text-green-700">
                                <i data-lucide="user-check" class="w-6 h-6"></i>
                            </div>
                            <div class="flex-grow">
                                <h4 class="font-bold text-lg text-gray-800" x-text="member?.first_name + ' ' + member?.last_name"></h4>
                                <div class="text-sm text-gray-600 mt-1 space-y-1">
                                    <p>ID: <span class="font-mono text-gray-800 font-bold" x-text="member?.member_id"></span> | Tel: <span x-text="member?.tel"></span></p>
                                    <p class="inline-flex items-center gap-1 bg-green-50 px-2 py-0.5 rounded text-xs font-bold text-green-800 border border-green-100">
                                        Points: <span x-text="member?.points"></span>
                                    </p>
                                </div>
                            </div>
                            <button @click="resetMember" class="text-gray-400 hover:text-red-500 transition p-2 hover:bg-gray-100 rounded-full">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        {{-- Guest Card: เพิ่มรายละเอียดตามที่ขอ --}}
                        <div x-show="isGuest" x-transition class="mt-4 p-4 rounded-xl border flex items-start gap-4 shadow-sm bg-white border-gray-300">
                            <div class="p-3 rounded-full shrink-0 bg-gray-200 text-gray-600">
                                <i data-lucide="user" class="w-6 h-6"></i>
                            </div>
                            <div class="flex-grow">
                                <h4 class="font-bold text-lg text-gray-800">ลูกค้าทั่วไป (Guest Customer)</h4>
                                <ul class="mt-2 space-y-1 text-sm text-gray-600">
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                        <span>ไม่ได้เป็นสมาชิกของทางร้าน</span>
                                    </li>
                                    <li class="flex items-center gap-2 text-red-600 font-medium">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                        <span>ไม่มีการสะสมแต้ม (No Points)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                        <span>ชำระเงินตามราคาปกติ</span>
                                    </li>
                                </ul>
                            </div>
                            <button @click="resetMember" class="text-gray-400 hover:text-red-500 transition p-2 hover:bg-gray-100 rounded-full">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    {{-- 2. เลือกสินค้า --}}
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-opacity duration-200"
                         :class="(member || isGuest) ? 'opacity-100' : 'opacity-50 pointer-events-none'">
                         
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                                <span class="bg-gray-200 text-gray-700 w-8 h-8 rounded-full flex items-center justify-center text-sm shadow-inner">2</span>
                                เลือกชุดและอุปกรณ์
                            </h3>
                        </div>

                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-600 mb-1">ค้นหาสินค้า</label>
                            <div class="relative">
                                <input type="text" 
                                       x-model="itemQuery" 
                                       @input.debounce.300ms="searchItems" 
                                       @focus="if(!itemQuery) searchItems()"
                                       class="w-full pl-4 pr-10 py-2.5 rounded-lg border-gray-300 bg-white shadow-sm focus:border-gray-500 focus:ring-gray-500 placeholder-gray-400 text-gray-800" 
                                       placeholder="พิมพ์ชื่อชุด... (คลิกเพื่อดูรายการแนะนำ)">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i data-lucide="search" class="w-5 h-5"></i>
                                </div>
                            </div>
                            
                            {{-- Dropdown --}}
                            <div x-show="items.length > 0" @click.away="items = []" class="absolute z-20 w-full bg-white dark:bg-gray-700 border border-gray-200 mt-2 rounded-lg shadow-xl max-h-60 overflow-y-auto" style="display: none;">
                                <ul>
                                    <template x-for="item in items" :key="item.id">
                                        <li @click="addToCart(item)" class="px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex justify-between items-center border-b border-gray-100 last:border-0 transition group">
                                            <div>
                                                <span class="font-bold text-gray-700 group-hover:text-gray-900 dark:text-gray-200 block" x-text="item.item_name"></span>
                                                <span class="text-xs text-gray-500" x-text="'ID: ' + item.id + ' | Stock: ' + item.stock"></span>
                                            </div>
                                            <span class="text-gray-600 font-bold bg-gray-200 px-2 py-1 rounded text-sm group-hover:bg-gray-300 group-hover:text-gray-800" x-text="formatPrice(item.price)"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">รายการสินค้า</th>
                                        <th class="px-4 py-3 text-right font-semibold">ราคา</th>
                                        <th class="px-4 py-3 text-center w-24">จัดการ</th> {{-- เพิ่มความกว้าง --}}
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, index) in cart" :key="index">
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-medium text-gray-800" x-text="item.item_name"></td>
                                            <td class="px-4 py-3 text-right font-mono text-gray-600" x-text="formatPrice(item.price)"></td>
                                            <td class="px-4 py-3 text-center">
                                                {{-- ปุ่มลบแบบชัดเจน --}}
                                                <button @click="removeFromCart(index)" class="inline-flex items-center px-2 py-1 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition border border-red-200">
                                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                                                    <span class="text-xs font-bold">ลบ</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="cart.length === 0">
                                        <td colspan="3" class="px-4 py-10 text-center text-gray-400 flex flex-col items-center justify-center bg-white">
                                            <div class="bg-gray-50 p-4 rounded-full mb-2">
                                                <i data-lucide="shopping-bag" class="w-8 h-8 opacity-30"></i>
                                            </div>
                                            <span class="font-medium">ยังไม่มีรายการสินค้าในตะกร้า</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 3. บริการเสริม --}}
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-opacity duration-200"
                         :class="(cart.length > 0) ? 'opacity-100' : 'opacity-50 pointer-events-none'">
                        
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                                <span class="bg-gray-200 text-gray-700 w-8 h-8 rounded-full flex items-center justify-center text-sm shadow-inner">3</span>
                                บริการเสริม & โปรโมชั่น
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- โปรโมชั่น (ใช้สีเหลืองอ่อนๆ เพื่อให้เด่น แต่คุมโทน) --}}
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

                            {{-- ช่างแต่งหน้า --}}
                            <div>
                                <x-input-label value="บริการช่างแต่งหน้า" class="text-gray-600 font-medium" />
                                <select x-model="selectedMakeupId" class="w-full mt-2 rounded-lg border-gray-300 bg-white shadow-sm focus:border-gray-500 focus:ring-gray-500 text-gray-700">
                                    <option value="">-- ไม่รับบริการ --</option>
                                    <template x-for="artist in makeupArtists" :key="artist.makeup_id">
                                        <option :value="artist.makeup_id" x-text="artist.first_name + ' ' + artist.last_name + ' (' + formatPrice(artist.price) + ')'"></option>
                                    </template>
                                </select>
                            </div>

                            {{-- แพ็กเกจถ่ายภาพ --}}
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
                </div>

                {{-- RIGHT COLUMN: Summary --}}
                <div class="lg:col-span-1">
                    {{-- Summary Card (ใช้สีเทา bg-gray-50) --}}
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-200 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-5 border-b border-gray-200 pb-3 flex items-center gap-2">
                            <div class="bg-gray-200 p-1.5 rounded text-gray-600">
                                <i data-lucide="receipt" class="w-5 h-5"></i>
                            </div>
                            สรุปรายการเช่า
                        </h3>
                        
                        <div class="space-y-5">
                            {{-- Date --}}
                            <div class="bg-white p-4 rounded-lg border border-gray-200 space-y-3 shadow-sm">
                                <div>
                                    <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">วันที่เช่า</span>
                                    <input type="date" x-model="rentalDate" @change="updateReturnDate" class="block w-full mt-1 rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm text-gray-700">
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">กำหนดคืน</span>
                                    <input type="date" x-model="returnDate" class="block w-full mt-1 rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm text-gray-700">
                                </div>
                            </div>

                            {{-- Price Detail --}}
                            <div class="text-sm space-y-3 text-gray-600 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex justify-between items-center py-1">
                                    <span>ค่าชุดและอุปกรณ์ <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full border border-gray-200" x-text="cart.length"></span></span>
                                    <span class="font-bold text-gray-800" x-text="formatPrice(cartTotal)"></span>
                                </div>

                                <template x-if="makeupPrice > 0">
                                    <div class="flex justify-between items-center bg-gray-50 p-2.5 rounded-lg border border-gray-200">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-gray-700">ค่าแต่งหน้า</span>
                                            <span class="text-xs text-gray-500" x-text="getMakeupName()"></span>
                                        </div>
                                        <span class="font-bold text-gray-700" x-text="formatPrice(makeupPrice)"></span>
                                    </div>
                                </template>

                                <template x-if="packagePrice > 0">
                                    <div class="flex justify-between items-center bg-gray-50 p-2.5 rounded-lg border border-gray-200">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-gray-700">ค่าถ่ายภาพ</span>
                                            <span class="text-xs text-gray-500" x-text="getPackageName()"></span>
                                        </div>
                                        <span class="font-bold text-gray-700" x-text="formatPrice(packagePrice)"></span>
                                    </div>
                                </template>

                                <template x-if="discountAmount > 0">
                                    <div class="flex justify-between items-center bg-green-50 p-2.5 rounded-lg border border-green-100">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-green-700 flex items-center gap-1">
                                                <i data-lucide="tag" class="w-3 h-3"></i> ส่วนลด
                                            </span>
                                            <span class="text-xs text-green-600" x-text="getPromotionName()"></span>
                                        </div>
                                        <span class="font-bold text-green-700" x-text="'-' + formatPrice(discountAmount)"></span>
                                    </div>
                                </template>
                            </div>

                            <div class="border-t-2 border-dashed border-gray-300 pt-4 mt-2">
                                <div class="flex justify-between items-end">
                                    <span class="text-gray-500 font-medium pb-1">ยอดรวมสุทธิ</span>
                                    <span class="text-3xl font-extrabold text-gray-800 tracking-tight" x-text="formatPrice(grandTotal)"></span>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="pt-2">
                                <button @click="submitRental" 
                                        class="w-full py-3.5 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl shadow-lg transform transition hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex justify-center items-center gap-2"
                                        :disabled="(!member && !isGuest) || cart.length === 0 || isSubmitting">
                                    <i data-lucide="check-circle" class="w-5 h-5" x-show="!isSubmitting"></i>
                                    <span x-text="isSubmitting ? 'กำลังบันทึก...' : 'ยืนยันการเช่า'"></span>
                                </button>
                            </div>
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

                memberQuery: '',
                member: null,
                memberError: false,
                memberErrorMsg: '',
                isGuest: false,

                itemQuery: '',
                items: [],
                cart: [],

                selectedPromotionId: '',
                selectedMakeupId: '',
                selectedPackageId: '',
                selectedPhotographerId: '',

                rentalDate: new Date().toISOString().split('T')[0],
                returnDate: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],

                isSubmitting: false,

                toggleGuest() {
                    if(this.isGuest) {
                        this.member = null;
                        this.memberQuery = '';
                        this.memberError = false;
                    }
                },
                
                resetMember() {
                    this.member = null;
                    this.isGuest = false;
                    this.cart = [];
                    this.selectedPromotionId = '';
                    this.selectedMakeupId = '';
                    this.selectedPackageId = '';
                    this.selectedPhotographerId = '';
                },

                async checkMember() {
                    if(!this.memberQuery) return;
                    this.memberError = false;
                    try {
                        const res = await fetch(`{{ route('reception.checkMember') }}?q=${this.memberQuery}`);
                        const data = await res.json();
                        if(data.success) {
                            this.member = data.member;
                            this.memberQuery = '';
                        } else {
                            this.member = null;
                            this.memberError = true;
                            this.memberErrorMsg = 'ไม่พบข้อมูลสมาชิกในระบบ';
                        }
                    } catch (e) { alert('Connection Error'); }
                },

                async searchItems() {
                    try {
                        const res = await fetch(`{{ route('reception.searchItems') }}?q=${this.itemQuery}`);
                        this.items = await res.json();
                    } catch (e) { console.error(e); }
                },

                addToCart(item) {
                    this.cart.push(item);
                    this.items = [];
                    this.itemQuery = '';
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                updateReturnDate() {
                    let d = new Date(this.rentalDate);
                    d.setDate(d.getDate() + 7);
                    this.returnDate = d.toISOString().split('T')[0];
                },

                // --- Helpers ---
                getMakeupName() {
                    const artist = this.makeupArtists.find(m => m.makeup_id == this.selectedMakeupId);
                    return artist ? (artist.first_name + ' ' + artist.last_name) : '';
                },

                getPackageName() {
                    const pkg = this.packages.find(p => p.package_id == this.selectedPackageId);
                    return pkg ? pkg.package_name : '';
                },

                getPromotionName() {
                    const promo = this.promotions.find(p => p.promotion_id == this.selectedPromotionId);
                    if(!promo) return '';
                    return promo.promotion_name + (promo.discount_type == 'percentage' ? ` (${promo.discount_value}%)` : ` (${promo.discount_value} บาท)`);
                },

                // --- Calculations ---
                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + parseFloat(item.price), 0);
                },

                get makeupPrice() {
                    const artist = this.makeupArtists.find(m => m.makeup_id == this.selectedMakeupId);
                    return artist ? parseFloat(artist.price) : 0;
                },

                get packagePrice() {
                    if (!this.selectedPackageId) return 0;
                    const pkg = this.packages.find(p => p.package_id == this.selectedPackageId);
                    return pkg ? parseFloat(pkg.price) : 0;
                },

                get discountAmount() {
                    if (!this.selectedPromotionId) return 0;
                    const promo = this.promotions.find(p => p.promotion_id == this.selectedPromotionId);
                    if (!promo) return 0;

                    const subtotal = this.cartTotal + this.makeupPrice + this.packagePrice;

                    if (promo.discount_type === 'percentage') {
                        return (subtotal * parseFloat(promo.discount_value)) / 100;
                    } else {
                        return parseFloat(promo.discount_value);
                    }
                },

                get grandTotal() {
                    let total = this.cartTotal + this.makeupPrice + this.packagePrice - this.discountAmount;
                    return total < 0 ? 0 : total;
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(price);
                },

                async submitRental() {
                    if(!confirm(`ยืนยันการเช่า ยอดรวมสุทธิ ${this.formatPrice(this.grandTotal)}?`)) return;
                    
                    this.isSubmitting = true;
                    try {
                        const res = await fetch(`{{ route('reception.storeRental') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                member_id: this.isGuest ? null : this.member.member_id,
                                rental_date: this.rentalDate,
                                return_date: this.returnDate,
                                items: this.cart,
                                promotion_id: this.selectedPromotionId,
                                makeup_id: this.selectedMakeupId,
                                package_id: this.selectedPackageId,
                                photographer_id: this.selectedPhotographerId,
                                total_amount: this.grandTotal
                            })
                        });

                        const result = await res.json();
                        if(result.success) {
                            alert('บันทึกสำเร็จ!');
                            window.location.reload();
                        } else {
                            alert('เกิดข้อผิดพลาด: ' + JSON.stringify(result.errors || result.message));
                        }
                    } catch (e) {
                        console.error(e);
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>