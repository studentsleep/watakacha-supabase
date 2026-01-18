<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Dashboard ภาพรวมร้าน
            </h2>

            {{-- 🕒 ปุ่มเลือกช่วงเวลา --}}
            <div class="bg-white p-1 rounded-lg shadow-sm border border-gray-200 flex text-sm">
                <a href="?filter=week" class="px-4 py-1.5 rounded-md transition {{ $filter == 'week' ? 'bg-indigo-600 text-white font-bold shadow' : 'text-gray-600 hover:bg-gray-100' }}">7 วัน</a>
                <a href="?filter=month" class="px-4 py-1.5 rounded-md transition {{ $filter == 'month' ? 'bg-indigo-600 text-white font-bold shadow' : 'text-gray-600 hover:bg-gray-100' }}">เดือนนี้</a>
                <a href="?filter=year" class="px-4 py-1.5 rounded-md transition {{ $filter == 'year' ? 'bg-indigo-600 text-white font-bold shadow' : 'text-gray-600 hover:bg-gray-100' }}">ปีนี้</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1️⃣ Top Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-l-4 border-green-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">รายรับวันนี้</p>
                    <h3 class="text-2xl font-bold text-green-600 mt-1">+฿{{ number_format($totalRevenueToday) }}</h3>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-l-4 border-red-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">รายจ่ายวันนี้ (ซ่อม/ซัก)</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1">-฿{{ number_format($totalExpenseToday) }}</h3>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-l-4 border-indigo-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">กำไรวันนี้</p>
                    <h3 class="text-2xl font-bold text-indigo-600 mt-1">
                        ฿{{ number_format($totalRevenueToday - $totalExpenseToday) }}
                    </h3>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-l-4 border-orange-500 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">สินค้าชำรุด/ซ่อม</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $damagedItemsCount }} ชิ้น</h3>
                    </div>
                    <div class="p-2 bg-orange-100 rounded-full text-orange-500 animate-pulse">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 2️⃣ Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                        เปรียบเทียบ รายรับ vs รายจ่าย ({{ $filter == 'year' ? 'รายปี' : ($filter == 'month' ? 'รายเดือน' : '7 วันล่าสุด') }})
                    </h3>
                    <div class="relative h-80">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center items-center">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 w-full text-left">สถานะสินค้าปัจจุบัน</h3>
                    <div class="relative h-64 w-full flex justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- 3️⃣ Damaged Items Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-red-50 flex justify-between items-center">
                    <h3 class="font-bold text-red-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        รายการแจ้งชำรุดล่าสุด (Damaged Items)
                    </h3>
                    <a href="{{ route('maintenance.index') }}" class="text-xs bg-white border border-red-200 text-red-600 px-3 py-1 rounded-full hover:bg-red-100 transition">จัดการทั้งหมด</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-6 py-3">สินค้า</th>
                                <th class="px-6 py-3">อาการ</th>
                                <th class="px-6 py-3">วันที่แจ้ง</th>
                                <th class="px-6 py-3 text-right">ค่าซ่อม (ถ้ามี)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($damagedItemsList as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-bold text-gray-100">{{ $item->item->item_name ?? 'ไม่ทราบ' }}</td>
                                <td class="px-6 py-3"><span class="text-red-600 bg-red-50 px-2 py-0.5 rounded text-xs">{{ $item->damage_description }}</span></td>
                                <td class="px-6 py-3 text-gray-500">{{ \Carbon\Carbon::parse($item->created_at)->locale('th')->diffForHumans() }}</td>
                                <td class="px-6 py-3 text-right font-mono text-red-500">{{ $item->shop_cost > 0 ? number_format($item->shop_cost) : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">ไม่มีรายการชำรุดล่าสุด</td>
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
        class="hidden">
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataEl = document.getElementById('chart-data');

            const labels = JSON.parse(dataEl.dataset.labels);
            const incomeData = JSON.parse(dataEl.dataset.income);
            const expenseData = JSON.parse(dataEl.dataset.expense);

            const rawStatus = JSON.parse(dataEl.dataset.status);
            const statusLabels = Object.keys(rawStatus);
            const statusValues = Object.values(rawStatus);

            // 1️⃣ Finance Chart (Bar: Income vs Expense)
            const ctxFinance = document.getElementById('financeChart').getContext('2d');
            new Chart(ctxFinance, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'รายรับ (Income)',
                            data: incomeData,
                            backgroundColor: '#10b981', // Emerald-500
                            borderRadius: 4,
                            barPercentage: 0.6,
                        },
                        {
                            label: 'รายจ่าย (Expense)',
                            data: expenseData,
                            backgroundColor: '#ef4444', // Red-500
                            borderRadius: 4,
                            barPercentage: 0.6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('th-TH', {
                                            style: 'currency',
                                            currency: 'THB'
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // 2️⃣ Status Chart (Doughnut)
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: ['#3b82f6', '#f59e0b', '#ef4444'], // Blue, Amber, Red
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>