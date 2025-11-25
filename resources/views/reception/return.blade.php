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
        <div x-show="isModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeModal"></div>

            {{-- Modal Content --}}
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                    
                    {{-- Header --}}
                    <div class="bg-gray-100 dark:bg-gray-700 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                            ตรวจสอบรายการคืน (Bill #<span x-text="currentRental?.rental_id"></span>)
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-4 py-5 sm:p-6">
                        
                        {{-- รายการสินค้า --}}
                        <h4 class="text-sm font-bold text-gray-700 mb-2">รายการสินค้าที่เช่า:</h4>
                        <div class="border rounded-lg overflow-hidden mb-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">สินค้า</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">สภาพสินค้า</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">ค่าปรับ</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(item, index) in currentItems" :key="index">
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-800">
                                                <div x-text="item.item.item_name"></div>
                                                <div class="text-xs text-gray-500" x-text="'ID: ' + item.item_id"></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                {{-- Checkbox เลือกความเสียหาย --}}
                                                <select x-model="item.damageType" @change="calculateTotalFine()" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                                    <option value="">✅ สภาพสมบูรณ์</option>
                                                    <option value="scratch">⚠️ เป็นรอยถลอก (150฿)</option>
                                                    <option value="stained">⚠️ เปื้อน/คราบ (200฿)</option>
                                                    <option value="torn">❌ ขาด/ชำรุด (500฿)</option>
                                                    <option value="lost">⛔ สูญหาย (เต็มราคา)</option>
                                                </select>
                                                {{-- หมายเหตุเพิ่มเติม --}}
                                                <input type="text" x-model="item.note" x-show="item.damageType" placeholder="ระบุตำแหน่ง/รายละเอียด..." class="mt-1 w-full text-xs border-gray-300 rounded">
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-red-600">
                                                <span x-show="item.damageType" x-text="'+' + getFineAmount(item)"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- สรุปยอดค่าปรับ --}}
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between text-sm text-red-800">
                                <span><i data-lucide="clock" class="inline w-4 h-4"></i> ค่าปรับคืนล่าช้า (<span x-text="overdueDays"></span> วัน × 100฿):</span>
                                <span class="font-bold" x-text="formatNumber(overdueFine)"></span>
                            </div>
                            <div class="flex justify-between text-sm text-red-800">
                                <span><i data-lucide="alert-triangle" class="inline w-4 h-4"></i> ค่าปรับความเสียหาย:</span>
                                <span class="font-bold" x-text="formatNumber(damageFine)"></span>
                            </div>
                            <div class="border-t border-red-200 pt-2 flex justify-between text-lg font-extrabold text-red-900">
                                <span>ยอดค่าปรับรวมที่ต้องเก็บ:</span>
                                <span x-text="formatNumber(totalFine) + ' บาท'"></span>
                            </div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button @click="submitReturn" 
                                :disabled="isSubmitting"
                                class="w-full inline-flex justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!isSubmitting">ยืนยันการคืน & ชำระค่าปรับ</span>
                            <span x-show="isSubmitting">กำลังบันทึก...</span>
                        </button>
                        <button @click="closeModal" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            ยกเลิก
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
                currentItems: [],
                
                overdueDays: 0,
                overdueFine: 0,
                damageFine: 0,
                totalFine: 0,

                // เปิด Modal และเตรียมข้อมูล
                openModal(rental) {
                    this.currentRental = rental;
                    
                    // เตรียมรายการสินค้า พร้อม field สำหรับใส่ข้อมูลเสียหาย
                    this.currentItems = rental.items.map(item => ({
                        ...item,
                        damageType: '', // ค่าเริ่มต้นคือสมบูรณ์
                        note: ''
                    }));

                    this.calculateOverdue();
                    this.calculateTotalFine();
                    this.isModalOpen = true;
                },

                closeModal() {
                    this.isModalOpen = false;
                    this.currentRental = null;
                },

                // คำนวณวันล่าช้า
                calculateOverdue() {
                    const returnDate = new Date(this.currentRental.return_date);
                    const today = new Date();
                    // Reset เวลาให้เป็นเที่ยงคืนเพื่อเทียบแค่วัน
                    returnDate.setHours(0,0,0,0);
                    today.setHours(0,0,0,0);

                    const diffTime = today - returnDate;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

                    this.overdueDays = diffDays > 0 ? diffDays : 0;
                    this.overdueFine = this.overdueDays * 100; // ปรับวันละ 100
                },

                // คำนวณค่าปรับของเสียตามราคาที่คุณกำหนด
                getFineAmount(item) {
                    switch(item.damageType) {
                        case 'scratch': return 150;
                        case 'stained': return 200;
                        case 'torn': return 500;
                        case 'lost': return parseFloat(item.price); // ปรับเต็มราคา
                        default: return 0;
                    }
                },

                calculateTotalFine() {
                    this.damageFine = this.currentItems.reduce((sum, item) => sum + this.getFineAmount(item), 0);
                    this.totalFine = this.overdueFine + this.damageFine;
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('th-TH').format(num);
                },

                async submitReturn() {
                    if(!confirm(`ยอดค่าปรับทั้งหมด ${this.formatNumber(this.totalFine)} บาท\nยืนยันว่าลูกค้ายอมรับยอดและต้องการคืนชุด?`)) return;

                    this.isSubmitting = true;
                    try {
                        const res = await fetch(`/reception/return/${this.currentRental.rental_id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            // ส่งข้อมูลความเสียหายกลับไปที่ Controller
                            body: JSON.stringify({
                                items_damage: this.currentItems.map(i => ({
                                    id: i.id, // id ของ rental_items
                                    damage_type: i.damageType,
                                    note: i.note,
                                    fine_amount: this.getFineAmount(i)
                                })),
                                overdue_fine: this.overdueFine
                            })
                        });

                        const data = await res.json();
                        if(data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert('เกิดข้อผิดพลาด: ' + data.message);
                        }
                    } catch(e) {
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