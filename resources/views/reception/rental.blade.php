<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            บริการเช่าชุด (Rental Service)
        </h2>
    </x-slot>

    <div class="py-6" x-data="rentalSystem()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- LEFT COLUMN: Forms --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- 1. ส่วนข้อมูลสมาชิก --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2 flex justify-between items-center">
                            <span>1. ข้อมูลสมาชิก</span>
                            {{-- Checkbox ไม่มีสมาชิก --}}
                            <label class="flex items-center space-x-2 text-sm cursor-pointer">
                                <input type="checkbox" x-model="isGuest" @change="toggleGuest" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-gray-600 dark:text-gray-400">ลูกค้าทั่วไป (ไม่เป็นสมาชิก)</span>
                            </label>
                        </h3>
                        
                        {{-- ช่องค้นหา (ซ่อนเมื่อเป็น Guest หรือ เจอสมาชิกแล้ว) --}}
                        <div x-show="!isGuest && !member" class="flex gap-2 transition-all">
                            <div class="flex-grow">
                                <x-input-label for="member_search" value="ค้นหาสมาชิก (ID, Username, เบอร์โทร)" />
                                <div class="flex mt-1">
                                    <x-text-input id="member_search" x-model="memberQuery" @keydown.enter.prevent="checkMember" class="w-full rounded-r-none" placeholder="กรอกรหัสหรือเบอร์โทร..." />
                                    <button @click="checkMember" class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700">
                                        <i data-lucide="search" class="w-5 h-5"></i>
                                    </button>
                                </div>
                                <p x-show="memberError" class="text-red-500 text-sm mt-1" x-text="memberErrorMsg"></p>
                            </div>
                        </div>

                        {{-- Member Card (แสดงเมื่อเจอ หรือ เป็น Guest) --}}
                        <div x-show="member || isGuest" class="mt-4 p-4 border rounded-md flex items-start transition"
                             :class="isGuest ? 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600' : 'bg-green-50 dark:bg-green-900 border-green-200'">
                            
                            <div class="p-2 rounded-full mr-3" :class="isGuest ? 'bg-gray-200 text-gray-600' : 'bg-green-100 text-green-600'">
                                <i data-lucide="user" class="w-6 h-6" x-show="isGuest"></i>
                                <i data-lucide="user-check" class="w-6 h-6" x-show="!isGuest"></i>
                            </div>
                            
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-gray-100" x-text="isGuest ? 'ลูกค้าทั่วไป (Guest)' : (member?.first_name + ' ' + member?.last_name)"></h4>
                                <template x-if="!isGuest">
                                    <div>
                                        <p class="text-sm text-green-700 dark:text-green-200">ID: <span x-text="member?.member_id"></span> | Tel: <span x-text="member?.tel"></span></p>
                                        <p class="text-sm text-green-700 dark:text-green-200">Points: <span x-text="member?.points"></span></p>
                                    </div>
                                </template>
                                <template x-if="isGuest">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">ไม่ได้บันทึกสะสมแต้ม</p>
                                </template>
                            </div>

                            {{-- ปุ่ม Reset --}}
                            <button @click="resetMember" class="ml-auto text-gray-400 hover:text-red-500" title="ยกเลิก">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    {{-- 2. ส่วนเลือกสินค้า (เหมือนเดิม) --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow opacity-50 pointer-events-none" 
                         :class="(member || isGuest) ? 'opacity-100 pointer-events-auto' : ''">
                        {{-- ... (เนื้อหาการเลือกสินค้าเหมือนเดิม) ... --}}
                         <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">
                            2. เลือกสินค้า
                        </h3>

                        <div class="relative">
                            <x-input-label for="item_search" value="ค้นหาสินค้า (ชื่อ หรือ ID)" />
                            <x-text-input id="item_search" x-model="itemQuery" @input.debounce.500ms="searchItems" class="w-full mt-1" placeholder="พิมพ์ชื่อชุด..." />
                            
                            {{-- Dropdown ผลการค้นหา --}}
                            <div x-show="items.length > 0" class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-200 mt-1 rounded-md shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                                <ul>
                                    <template x-for="item in items" :key="item.id">
                                        <li @click="addToCart(item)" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex justify-between items-center border-b last:border-0">
                                            <div>
                                                <span class="font-bold text-gray-800 dark:text-gray-200" x-text="item.item_name"></span>
                                                <span class="text-xs text-gray-500 block" x-text="'ID: ' + item.id + ' | Stock: ' + item.stock"></span>
                                            </div>
                                            <span class="text-blue-600 font-bold" x-text="formatPrice(item.price)"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        {{-- ตารางรายการที่เลือก (Cart) --}}
                        <div class="mt-6">
                            <table class="w-full text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-4 py-2">สินค้า</th>
                                        <th class="px-4 py-2 text-right">ราคา (7 วัน)</th>
                                        <th class="px-4 py-2 text-center">ลบ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in cart" :key="index">
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white" x-text="item.item_name"></td>
                                            <td class="px-4 py-3 text-right" x-text="formatPrice(item.price)"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button @click="removeFromCart(index)" class="text-red-600 hover:text-red-800">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="cart.length === 0">
                                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">ยังไม่มีสินค้าในรายการ</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow sticky top-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">
                            สรุปรายการเช่า
                        </h3>
                        {{-- ... (ส่วนสรุปยอดเหมือนเดิม) ... --}}
                         <div class="space-y-4">
                            <div>
                                <x-input-label value="วันที่ยืม" />
                                <x-text-input type="date" x-model="rentalDate" class="w-full mt-1 bg-gray-100 cursor-not-allowed" readonly />
                            </div>
                            <div>
                                <x-input-label value="กำหนดคืน (7 วัน)" />
                                <x-text-input type="date" x-model="returnDate" class="w-full mt-1" />
                                <p class="text-xs text-red-500 mt-1">* คืนล่าช้าปรับ 100 บาท/วัน/ชุด</p>
                            </div>

                            <div class="border-t pt-4 mt-4">
                                <div class="flex justify-between items-center text-lg font-bold text-gray-900 dark:text-white">
                                    <span>ยอดรวม</span>
                                    <span x-text="formatPrice(totalPrice)"></span>
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-500 text-center mt-2">
                                ผู้ทำรายการ: {{ Auth::user()->name }}
                            </div>

                            {{-- [แก้ไข] ปุ่มกดได้เมื่อมี Member หรือ Guest --}}
                            <button @click="submitRental" 
                                class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="(!member && !isGuest) || cart.length === 0 || isSubmitting">
                                <span x-show="!isSubmitting">ยืนยันการเช่า</span>
                                <span x-show="isSubmitting">กำลังบันทึก...</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function rentalSystem() {
            return {
                // ... (ตัวแปรเดิม) ...
                memberQuery: '',
                member: null,
                memberError: false,
                memberErrorMsg: '',
                isGuest: false, // [ใหม่] ตัวแปรเช็คว่าเป็น Guest ไหม
                
                itemQuery: '',
                items: [],
                cart: [],
                
                rentalDate: new Date().toISOString().split('T')[0],
                returnDate: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                isSubmitting: false,

                // [ใหม่] ฟังก์ชันสลับโหมด Guest
                toggleGuest() {
                    if(this.isGuest) {
                        this.member = null; // ล้างข้อมูลสมาชิกเก่าถ้ามี
                        this.memberQuery = '';
                        this.memberError = false;
                    }
                },

                // [แก้ไข] ล้างข้อมูล
                resetMember() {
                    this.member = null;
                    this.isGuest = false; // Reset Guest ด้วย
                    this.cart = [];
                    this.items = [];
                    this.memberQuery = '';
                },

                async checkMember() {
                    if(!this.memberQuery) return;
                    this.memberError = false;
                    this.isGuest = false; // ถ้าค้นหา ต้องไม่ใช่ Guest
                    
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
                    } catch (e) {
                        console.error(e);
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                    }
                },

                // ... (searchItems, addToCart, removeFromCart, totalPrice, formatPrice เหมือนเดิม) ...
                async searchItems() {
                    if(this.itemQuery.length < 2) {
                        this.items = [];
                        return;
                    }
                    
                    try {
                        const res = await fetch(`{{ route('reception.searchItems') }}?q=${this.itemQuery}`);
                        this.items = await res.json();
                    } catch (e) {
                        console.error(e);
                    }
                },

                addToCart(item) {
                    this.cart.push(item);
                    this.items = [];
                    this.itemQuery = '';
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                get totalPrice() {
                    return this.cart.reduce((sum, item) => sum + parseFloat(item.price), 0);
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(price);
                },

                async submitRental() {
                    if(!confirm('ยืนยันการทำรายการเช่า?')) return;
                    
                    this.isSubmitting = true;

                    // [แก้ไข] เตรียมข้อมูลส่ง
                    // ถ้าเป็น Guest ให้ส่ง member_id เป็น null
                    const memberIdToSend = this.isGuest ? null : this.member.member_id;

                    try {
                        const res = await fetch(`{{ route('reception.storeRental') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                member_id: memberIdToSend, // ส่งค่า null หรือ ID
                                is_guest: this.isGuest,    // ส่ง flag ไปบอกหลังบ้านด้วย (เผื่อใช้)
                                rental_date: this.rentalDate,
                                return_date: this.returnDate,
                                items: this.cart
                            })
                        });

                        const result = await res.json();

                        if(result.success) {
                            alert('บันทึกข้อมูลเรียบร้อย!');
                            window.location.href = "{{ route('manager.index', ['table' => 'rentals']) }}";
                        } else {
                            alert('เกิดข้อผิดพลาด: ' + JSON.stringify(result.errors || result.message));
                        }
                    } catch (e) {
                        console.error(e);
                        alert('เกิดข้อผิดพลาดร้ายแรง');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>