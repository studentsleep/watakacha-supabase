<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Dashboard ผู้บริหาร
            </h2>

            {{-- 🕒 ตัวเลือกช่วงเวลา --}}
            <form method="GET" action="{{ route('manager.dashboard') }}" class="flex flex-wrap items-center gap-2 bg-white dark:bg-gray-700 p-2 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600">
                <div class="hidden sm:flex gap-1 border-r pr-2 border-gray-200 dark:border-gray-600 mr-2">
                    {{-- ✅ เพิ่มปุ่ม วันนี้ --}}
                    <a href="?filter=today" class="px-3 py-1 rounded text-xs transition {{ $filter == 'today' ? 'bg-indigo-100 text-indigo-700 font-bold' : 'text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">วันนี้</a>
                    <a href="?filter=week" class="px-3 py-1 rounded text-xs transition {{ $filter == 'week' ? 'bg-indigo-100 text-indigo-700 font-bold' : 'text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">7 วัน</a>
                    <a href="?filter=month" class="px-3 py-1 rounded text-xs transition {{ $filter == 'month' ? 'bg-indigo-100 text-indigo-700 font-bold' : 'text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">เดือนนี้</a>
                    <a href="?filter=year" class="px-3 py-1 rounded text-xs transition {{ $filter == 'year' ? 'bg-indigo-100 text-indigo-700 font-bold' : 'text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">ปีนี้</a>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs py-1.5">
                    <span>-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs py-1.5">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md text-xs font-bold shadow transition">ค้นหา</button>
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1️⃣ Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-l-4 border-green-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">รายรับ (ช่วงที่เลือก)</p>
                    <h3 class="text-2xl font-bold text-green-500 mt-1">{{ number_format($totalRevenuePeriod) }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1">สินค้า + อุปกรณ์ + บริการ</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-l-4 border-red-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">รายจ่าย (ช่วงที่เลือก)</p>
                    <h3 class="text-2xl font-bold text-red-500 mt-1">{{ number_format($totalExpensePeriod) }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1">ต้นทุนช่าง + ค่าซ่อมบำรุง</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-l-4 border-indigo-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">กำไรขั้นต้น (ช่วงที่เลือก)</p>
                    <h3 class="text-2xl font-bold text-indigo-500 mt-1">{{ number_format($totalProfitPeriod) }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1">รายรับ - รายจ่าย</p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-2xl shadow-inner flex flex-col justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">สถานะวันนี้ (Realtime)</p>
                        <div class="flex justify-between items-end mt-2">
                            <span class="text-sm font-medium text-green-500">รับ: +{{ number_format($todayRevenue) }}</span>
                            <span class="text-sm font-medium text-red-400">จ่าย: -{{ number_format($todayExpense) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2️⃣ Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- 📊 กราฟสัดส่วนรายได้ (Pie Chart) --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    {{-- ✅ แก้ไข: เพิ่ม dark:text-gray-200 เพื่อให้หัวข้อสีขาวในโหมดมืด --}}
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <span class="p-1 bg-yellow-100 text-yellow-600 rounded">💰</span> สัดส่วนที่มาของรายได้ (Revenue Mix)
                    </h3>
                    <div class="relative h-64 w-full flex justify-center">
                        <canvas id="revenuePieChart"></canvas>
                    </div>
                    {{-- ตารางเล็กใต้กราฟ --}}
                    <div class="mt-4 grid grid-cols-3 text-center gap-2 text-xs">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded text-blue-800 dark:text-blue-300">
                            <div class="font-bold">สินค้า (Net)</div>
                            <div>{{ number_format($revItemsNet) }}</div>
                        </div>
                        <div class="p-2 bg-orange-50 dark:bg-orange-900/30 rounded text-orange-800 dark:text-orange-300">
                            <div class="font-bold">อุปกรณ์</div>
                            <div>{{ number_format($revAccessories) }}</div>
                        </div>
                        <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded text-purple-800 dark:text-purple-300">
                            <div class="font-bold">บริการ</div>
                            <div>{{ number_format($revServices) }}</div>
                        </div>
                    </div>
                </div>

                {{-- 📊 กราฟเปรียบเทียบกำไรบริการ (Bar Chart) --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    {{-- ✅ แก้ไข: เพิ่ม dark:text-gray-200 --}}
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <span class="p-1 bg-purple-100 text-purple-600 rounded">💇📸</span> กำไรจากงานบริการ (Services Margin)
                    </h3>
                    <div class="relative h-64">
                        <canvas id="serviceProfitChart"></canvas>
                    </div>
                    <div class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
                        @if($revServices > 0)
                        กำไรจากบริการ: <span class="font-bold text-green-600 dark:text-green-400">{{ number_format($revServices - $costServices) }}</span>
                        (อัตรากำไรขั้นต้น: {{ number_format( (($revServices - $costServices) / $revServices) * 100, 1) }}%)
                        @else
                        ไม่มีรายได้บริการในช่วงนี้
                        @endif
                    </div>
                </div>
            </div>

            {{-- ... (ต่อจาก Section 2 เดิม) ... --}}

            {{-- 🆕 2.5 Expense Breakdown Chart (เพิ่มใหม่) --}}
            <div class="grid grid-cols-1 gap-8 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <span class="p-1 bg-red-100 text-red-600 rounded">💸</span> สัดส่วนรายจ่าย (Expense Breakdown)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        {{-- กราฟ --}}
                        <div class="relative h-64 w-full flex justify-center">
                            <canvas id="expensePieChart"></canvas>
                        </div>

                        {{-- รายละเอียดตัวเลข --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">ค่าซ่อมบำรุง (Maintenance)</span>
                                </div>
                                <span class="font-mono font-bold text-red-600 dark:text-red-400">{{ number_format($costMaintenance) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">ค่าจ้างช่าง (Services Cost)</span>
                                </div>
                                <span class="font-mono font-bold text-orange-600 dark:text-orange-400">{{ number_format($costServices) }}</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-500">รวมรายจ่ายทั้งหมด</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($costMaintenance + $costServices) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3️⃣ รายงานสถิติ (Top Lists) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- 🏆 สินค้ายอดนิยม --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-3 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-800 flex justify-between items-center">
                        <h3 class="font-bold text-blue-800 dark:text-blue-300 text-sm">🏆 ชุดยอดนิยม (Top 5 Items)</h3>
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 uppercase">
                            <tr>
                                <th class="px-4 py-2">ชื่อชุด</th>
                                <th class="px-4 py-2 text-right">จำนวนเช่า</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($topItems as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <td class="px-4 py-2 font-medium">{{ $item->item->item_name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-right font-bold text-blue-600 dark:text-blue-400">{{ $item->total_qty }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-gray-400 text-xs">ไม่มีข้อมูล</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 🎒 อุปกรณ์เสริมยอดนิยม --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-3 bg-orange-50 dark:bg-orange-900/20 border-b border-orange-100 dark:border-orange-800 flex justify-between items-center">
                        <h3 class="font-bold text-orange-800 dark:text-orange-300 text-sm">🎒 อุปกรณ์เสริมยอดนิยม (Top 5 Accs)</h3>
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 uppercase">
                            <tr>
                                <th class="px-4 py-2">ชื่ออุปกรณ์</th>
                                <th class="px-4 py-2 text-right">จำนวนเช่า</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($topAccessories as $acc)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <td class="px-4 py-2 font-medium">{{ $acc->accessory->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-right font-bold text-orange-600 dark:text-orange-400">{{ $acc->total_qty }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-gray-400 text-xs">ไม่มีข้อมูล</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 🏷️ รายงานโปรโมชั่น --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-3 bg-green-50 dark:bg-green-900/20 border-b border-green-100 dark:border-green-800 flex justify-between items-center">
                        <h3 class="font-bold text-green-800 dark:text-green-300 text-sm">🏷️ การใช้โปรโมชั่น (Promotions)</h3>
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 uppercase">
                            <tr>
                                <th class="px-4 py-2">ชื่อโปรโมชั่น</th>
                                <th class="px-4 py-2 text-right">ครั้ง</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($promotionStats as $promo)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <td class="px-4 py-2 font-medium">{{ $promo->promotion->promotion_name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-right font-bold text-green-600 dark:text-green-400">{{ $promo->usage_count }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-gray-400 text-xs">ไม่มีการใช้โปรโมชั่น</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4️⃣ รายการแจ้งชำรุด --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-red-50 dark:bg-red-900/20 flex justify-between items-center">
                    <h3 class="font-bold text-red-800 dark:text-red-300 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        รายการแจ้งชำรุดล่าสุด
                    </h3>
                    <a href="{{ route('maintenance.index') }}" class="text-xs bg-white dark:bg-gray-700 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 px-3 py-1 rounded-full hover:bg-red-100 dark:hover:bg-red-800 transition">ดูทั้งหมด</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-6 py-3">สินค้า</th>
                                <th class="px-6 py-3">อาการ</th>
                                <th class="px-6 py-3">วันที่แจ้ง</th>
                                <th class="px-6 py-3 text-right">ค่าซ่อม</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($damagedItemsList as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <td class="px-6 py-3 font-bold text-gray-800 dark:text-gray-200">
                                    {{ $item->item ? $item->item->item_name : ($item->accessory ? $item->accessory->name . ' (อุปกรณ์)' : 'ไม่ระบุ') }}
                                </td>
                                <td class="px-6 py-3"><span class="text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 px-2 py-0.5 rounded text-xs">{{ $item->damage_description }}</span></td>
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($item->created_at)->locale('th')->diffForHumans() }}</td>
                                <td class="px-6 py-3 text-right font-mono text-red-500 dark:text-red-400">{{ $item->shop_cost > 0 ? number_format($item->shop_cost) : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 dark:text-gray-500">ไม่มีรายการชำรุดล่าสุด</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Data for JS --}}
    <div id="chart-data"
        data-labels="{{ json_encode($chartLabels) }}"
        data-income="{{ json_encode($incomeData) }}"
        data-expense="{{ json_encode($expenseData) }}"
        data-status="{{ json_encode($itemStatus) }}"
        data-rev-items="{{ $revItemsNet ?? 0 }}"
        data-rev-accessories="{{ $revAccessories ?? 0 }}"
        data-rev-services="{{ $revServices ?? 0 }}"
        data-cost-services="{{ $costServices ?? 0 }}"
        data-cost-maintenance="{{ $costMaintenance ?? 0 }}"
        class="hidden">
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ตั้งค่าสี Global สำหรับ Dark Mode
            Chart.defaults.color = '#cbd5e1';
            Chart.defaults.borderColor = '#334155';

            const dataEl = document.getElementById('chart-data');
            if (!dataEl) return;

            // ดึงข้อมูล Array
            const labels = JSON.parse(dataEl.dataset.labels || '[]');
            const incomeData = JSON.parse(dataEl.dataset.income || '[]');
            const expenseData = JSON.parse(dataEl.dataset.expense || '[]');

            // ✅ ดึงค่าตัวเลข (แปลงเป็น Float เพื่อป้องกันค่าว่าง)
            const revItemsNet = parseFloat(dataEl.dataset.revItems || 0);
            const revAccessories = parseFloat(dataEl.dataset.revAccessories || 0);
            const revServices = parseFloat(dataEl.dataset.revServices || 0);
            const costServices = parseFloat(dataEl.dataset.costServices || 0);

            const costMaintenance = parseFloat(dataEl.dataset.costMaintenance || 0);

            // ----------------------------------------------------
            // 📊 กราฟ 1: Revenue Mix Pie Chart (สัดส่วนรายได้)
            // ----------------------------------------------------
            const ctxRev = document.getElementById('revenuePieChart');
            if (ctxRev) {
                new Chart(ctxRev.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: ['ชุด/สินค้า (Net)', 'อุปกรณ์เสริม', 'บริการ (หน้าบิล)'],
                        datasets: [{
                            data: [revItemsNet, revAccessories, revServices],
                            backgroundColor: ['#3b82f6', '#f97316', '#a855f7'],
                            borderWidth: 2,
                            borderColor: '#1f2937'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#e2e8f0',
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let val = context.parsed;
                                        return ' ' + new Intl.NumberFormat('th-TH', {
                                            style: 'currency',
                                            currency: 'THB'
                                        }).format(val);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ----------------------------------------------------
            // 📊 กราฟ 2: Service Profit Bar Chart (กำไรบริการ)
            // ----------------------------------------------------
            const ctxService = document.getElementById('serviceProfitChart');
            if (ctxService) {
                new Chart(ctxService.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['รายได้บริการ', 'ต้นทุนช่าง'],
                        datasets: [{
                            label: 'จำนวนเงิน (บาท)',
                            // ✅ ใช้ตัวแปรที่ดึงมาด้านบน
                            data: [revServices, costServices],
                            backgroundColor: ['#a855f7', '#ef4444'],
                            borderRadius: 5,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#374151'
                                },
                                ticks: {
                                    color: '#9ca3af'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#e5e7eb',
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + new Intl.NumberFormat('th-TH', {
                                            style: 'currency',
                                            currency: 'THB'
                                        }).format(context.parsed.y);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ----------------------------------------------------
            // 📊 กราฟ 3: Finance Chart (รายรับ vs รายจ่าย)
            // ----------------------------------------------------
            const ctxFinance = document.getElementById('financeChart');
            if (ctxFinance) {
                new Chart(ctxFinance.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'รายรับ (Income)',
                            data: incomeData,
                            backgroundColor: '#10b981',
                            borderRadius: 4,
                            barPercentage: 0.6
                        }, {
                            label: 'รายจ่าย (Expense)',
                            data: expenseData,
                            backgroundColor: '#ef4444',
                            borderRadius: 4,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#374151'
                                },
                                ticks: {
                                    color: '#9ca3af'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#9ca3af'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#cbd5e1'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + new Intl.NumberFormat('th-TH', {
                                            style: 'currency',
                                            currency: 'THB'
                                        }).format(context.parsed.y);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ----------------------------------------------------
            // 📊 กราฟ 4: Expense Breakdown Pie Chart (รายจ่ายแยกประเภท)
            // ----------------------------------------------------
            const ctxExp = document.getElementById('expensePieChart');
            if (ctxExp) {
                new Chart(ctxExp.getContext('2d'), {
                    type: 'doughnut', // ใช้ Doughnut ให้ดูต่างจาก Revenue
                    data: {
                        labels: ['ค่าซ่อมบำรุง', 'ค่าจ้างช่าง'],
                        datasets: [{
                            data: [costMaintenance, costServices],
                            backgroundColor: ['#ef4444', '#f97316'], // Red, Orange
                            borderWidth: 2,
                            borderColor: '#1f2937'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right', // ย้าย Legend ไปขวา
                                labels: {
                                    color: '#e2e8f0',
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let val = context.parsed;
                                        return ' ' + new Intl.NumberFormat('th-TH', {
                                            style: 'currency',
                                            currency: 'THB'
                                        }).format(val);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>