<x-app-layout>
    {{-- ✅ SweetAlert2 & Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

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

        /* Custom Scrollbar & Search */
        .search-results {
            max-height: 200px;
            overflow-y: auto;
            position: absolute;
            z-index: 1000;
            width: 100%;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .search-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s;
        }

        .search-item:hover {
            background-color: #f9fafb;
            padding-left: 1.25rem;
        }

        .modal-body-scroll {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-600 rounded-xl text-white ">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    ระบบจัดการการเช่า (Rental Management)
                </h2>
            </div>
        </div>
    </x-slot>

    {{-- ✅ Main Content Wrapper (ต้องปิด div นี้ตอนท้ายสุด) --}}
    <div class="py-8" x-data="historySystem({
        promotions: {{ Js::from($promotions ?? []) }},
        makeupArtists: {{ Js::from($makeup_artists ?? []) }},
        packages: {{ Js::from($photo_packages ?? []) }},
        photographers: {{ Js::from($photographers ?? []) }},
        accessoriesList: {{ Js::from($accessories ?? []) }}
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🟢 TABS NAVIGATION (UI เดิมตามที่ขอ) --}}
            <div class="flex p-1 mb-8 bg-gray-100 rounded-2xl shadow-inner no-print">
                <button @click="activeTab = 'pending'"
                    :class="activeTab === 'pending' ? 'bg-white text-indigo-700 shadow-md ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'"
                    class="flex-1 rounded-xl py-3 text-sm font-bold transition-all duration-200 flex justify-center items-center gap-2">
                    <span>🕒 รอชำระเงิน</span>
                    @if($pending->count() > 0)
                    <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full shadow-sm">{{ $pending->count() }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'active'"
                    :class="activeTab === 'active' ? 'bg-white text-indigo-700 shadow-md ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'"
                    class="flex-1 rounded-xl py-3 text-sm font-bold transition-all duration-200 flex justify-center items-center gap-2">
                    <span>📦 กำลังเช่า (Active)</span>
                    @if($active->count() > 0)
                    <span class="bg-blue-500 text-white text-[10px] px-2 py-0.5 rounded-full shadow-sm">{{ $active->count() }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'bg-white text-indigo-700 shadow-md ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'"
                    class="flex-1 rounded-xl py-3 text-sm font-bold transition-all duration-200 flex justify-center items-center gap-2">
                    <span>📜 ประวัติการคืน (History)</span>
                </button>
            </div>

            {{-- ======================================================= --}}
            {{-- 🟡 TAB 1: PENDING (รอชำระเงิน) --}}
            {{-- ======================================================= --}}
            <div x-show="activeTab === 'pending'" x-transition:enter="transition ease-out duration-300">

                {{-- Search --}}
                <div class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex gap-3 items-center no-print">
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <form action="{{ route('reception.rental') }}" method="GET" class="flex-grow flex gap-2">
                        <input type="hidden" name="tab" value="pending">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาใบสั่งจองรอชำระ..." class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="bg-gray-900 text-white px-5 rounded-xl text-sm font-bold hover:bg-black transition">ค้นหา</button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @if($pending->isEmpty())
                    <div class="p-16 text-center text-gray-400 flex flex-col items-center">
                        <div class="bg-gray-50 p-4 rounded-full mb-3"><svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg></div>
                        <p>ไม่มีรายการรอชำระเงิน</p>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs text-gray-500 uppercase tracking-wider">รหัสการเช่า</th>
                                    <th class="px-6 py-4 text-left text-xs text-gray-500 uppercase tracking-wider">ลูกค้า</th>
                                    <th class="px-6 py-4 text-right text-xs text-gray-500 uppercase tracking-wider">ยอดสุทธิ</th>
                                    <th class="px-6 py-4 text-right text-xs text-gray-500 uppercase tracking-wider">มัดจำ (50%)</th>
                                    <th class="px-6 py-4 text-center text-xs text-gray-500 uppercase tracking-wider">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @foreach($pending as $item)
                                <tr class="hover:bg-indigo-50/30 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded border border-indigo-100">#{{ $item->rental_id }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center text-xs font-bold">
                                                {{ mb_substr($item->member ? $item->member->first_name : ($item->description ? 'G' : '?'), 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ $item->member ? $item->member->first_name . ' ' . $item->member->last_name : 'ลูกค้าทั่วไป' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $item->member ? $item->member->tel : ($item->description ?? '-') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-white">
                                        {{ number_format($item->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-orange-600">
                                        {{ number_format($item->total_amount * 0.5, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center gap-2">
                                            <button @click="openPaymentModal({{ Js::from($item) }})" class="p-1.5 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition" title="ชำระเงิน">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </button>
                                            <button @click="openEditModal({{ Js::from($item) }})" class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition" title="แก้ไข">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button @click="confirmCancel({{ $item->rental_id }})" class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="ลบ">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- 🔵 TAB 2: ACTIVE (รายการเช่าคงค้าง) --}}
            {{-- ======================================================= --}}
            <div x-show="activeTab === 'active'" style="display: none;" x-transition:enter="transition ease-out duration-300">

                {{-- Search --}}
                <div class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex gap-3 items-center no-print">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <form action="{{ route('reception.rental') }}" method="GET" class="flex-grow flex gap-2">
                        <input type="hidden" name="tab" value="active">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหา ID, ลูกค้า (รายการที่กำลังเช่า)..." class="w-full border-gray-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" class="bg-gray-900 text-white px-5 rounded-xl text-sm font-bold hover:bg-black transition">ค้นหา</button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-200 dark:border-gray-700 overflow-visible">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">รหัสการเช่า</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ลูกค้า</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">วันที่ / กำหนดคืน</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">ยอดคงเหลือ</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">สถานะ</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @forelse($active as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150 group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded border border-indigo-100">#{{ $item->rental_id }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center text-xs font-bold">
                                                {{ mb_substr($item->member ? $item->member->first_name : ($item->description ? 'G' : '?'), 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ $item->member ? $item->member->first_name . ' ' . $item->member->last_name : 'ลูกค้าทั่วไป' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $item->member ? $item->member->tel : ($item->description ?? '-') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        <div class="flex flex-col">
                                            <span class="font-medium">
                                                เริ่ม: {{ \Carbon\Carbon::parse($item->rental_date)->addYears(543)->locale('th')->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="text-xs text-gray-400">
                                                คืน: {{ \Carbon\Carbon::parse($item->return_date)->addYears(543)->locale('th')->translatedFormat('d M Y') }}
                                            </span>
                                            @php
                                            $returnDate = \Carbon\Carbon::parse($item->return_date)->startOfDay();
                                            $now = \Carbon\Carbon::now()->startOfDay();
                                            $isOverdue = $now->gt($returnDate) && $item->status == \App\Models\Rental::STATUS_RENTED;
                                            $overdueDays = abs($now->diffInDays($returnDate));
                                            @endphp
                                            @if($isOverdue)
                                            <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full w-fit mt-1 animate-pulse border border-red-200">
                                                ⚠️ เลยกำหนด {{ number_format($overdueDays) }} วัน
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-white">
                                        @php
                                        $paid = $item->payments->where('status', 'paid')->sum('amount');
                                        $remaining = $item->total_amount - $paid;
                                        @endphp
                                        @if($remaining > 0)
                                        <span class="text-orange-500" title="ค้างชำระ">
                                            {{ number_format($remaining, 0) }}
                                        </span>
                                        <div class="text-[10px] text-gray-400">จาก {{ number_format($item->total_amount, 0) }}</div>
                                        @else
                                        <span class="text-green-600">ครบถ้วน</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($item->status == \App\Models\Rental::STATUS_AWAITING_PICKUP)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                            <span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-1.5 animate-pulse"></span> รอรับชุด
                                        </span>
                                        @elseif($item->status == \App\Models\Rental::STATUS_RENTED)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5 animate-pulse"></span> กำลังเช่า
                                        </span>
                                        @elseif($item->status == \App\Models\Rental::STATUS_PENDING_PAYMENT)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                            💰 รอชำระ
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                            {{ $item->status }}
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($item->status == \App\Models\Rental::STATUS_AWAITING_PICKUP)
                                            <button @click="confirmPickup({{ $item->rental_id }})" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-yellow-600 hover:bg-yellow-700 shadow-sm transition-all">
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                รับชุด
                                            </button>
                                            @elseif($item->status == \App\Models\Rental::STATUS_RENTED)
                                            <a href="{{ route('reception.return', ['search' => $item->rental_id]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                </svg>
                                                คืนชุด
                                            </a>
                                            @elseif($item->status == \App\Models\Rental::STATUS_PENDING_PAYMENT || $remaining > 0)
                                            <button @click="openPaymentModal({{ Js::from($item) }})" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-green-600 hover:bg-green-700 shadow-sm transition-all">
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                ชำระ
                                            </button>
                                            @endif

                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" @click.away="open = false" class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" x-cloak class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-xl border border-gray-100 z-50 overflow-hidden text-left py-1">
                                                    <button @click="openReceiptModal({{ Js::from($item) }}); open = false;" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">📄 ดูใบเสร็จ</button>
                                                    <button @click="openEditModal({{ Js::from($item) }}); open = false;" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">✏️ แก้ไขรายการ</button>
                                                    <button @click="confirmCancel({{ $item->rental_id }}); open = false;" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">❌ ยกเลิกรายการ</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="mt-2 text-sm font-medium">ไม่พบรายการเช่าที่กำลังดำเนินการ</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- ⚪ TAB 3: HISTORY (ประวัติ)--}}
            {{-- ======================================================= --}}
            <div x-show="activeTab === 'history'" style="display: none;" x-transition:enter="transition ease-out duration-300">

                {{-- Search (เหลือแค่ Search อย่างเดียว) --}}
                <div class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex gap-3 items-center no-print">
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <form action="{{ route('reception.rental') }}" method="GET" class="flex-grow flex gap-2">
                        <input type="hidden" name="tab" value="history">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาประวัติ (ID, ชื่อ)..." class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="bg-gray-900 text-white px-5 rounded-xl text-sm font-bold hover:bg-black transition">ค้นหา</button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-200 dark:border-gray-700 overflow-visible">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs text-gray-500 uppercase tracking-wider">Ref ID</th>
                                <th class="px-6 py-4 text-left text-xs text-gray-500 uppercase tracking-wider">ลูกค้า</th>
                                <th class="px-6 py-4 text-center text-xs text-gray-500 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-4 text-right text-xs text-gray-500 uppercase tracking-wider">ค่าปรับ</th>
                                <th class="px-6 py-4 text-right text-xs text-gray-500 uppercase tracking-wider">ยอดสุทธิ</th>
                                <th class="px-6 py-4 text-center text-xs text-gray-500 uppercase tracking-wider">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach($history as $item)
                            <tr class="hover:bg-indigo-50/30 transition duration-150">
                                {{-- 1. Ref ID  --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded border border-indigo-100">#{{ $item->rental_id }}</span>
                                </td>
                                {{-- 2. ลูกค้า --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center text-xs font-bold">
                                            {{ mb_substr($item->member ? $item->member->first_name : ($item->description ? 'G' : '?'), 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $item->member ? $item->member->first_name . ' ' . $item->member->last_name : 'ลูกค้าทั่วไป' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $item->member ? $item->member->tel : ($item->description ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->status == \App\Models\Rental::STATUS_RETURNED)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        คืนแล้ว
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200">ยกเลิก</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-red-600 font-bold">
                                    {{ $item->fine_amount > 0 ? number_format($item->fine_amount, 2) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-white">
                                    {{ number_format($item->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="openReceiptModal({{ Js::from($item) }})" class="text-sm text-gray-900 hover:text-indigo-900 hover:underline bg-indigo-50 px-3 py-1 rounded-lg">📄 ดูใบเสร็จ</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">{{ $history->links() }}</div>
                </div>
            </div>

        </div> {{-- 💰 PAYMENT MODAL --}}
        <div x-show="showPaymentModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showPaymentModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="bg-green-100 text-green-600 p-2 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg></span>
                            ยืนยันการชำระเงิน
                        </h3>
                        <div class="space-y-5">
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                <div class="flex justify-between text-sm text-gray-600 mb-1"><span>ยอดรวมทั้งสิ้น</span><span x-text="formatNumber(selectedItem?.total_amount)"></span></div>
                                <div class="flex justify-between text-lg font-bold text-gray-900"><span>ยอดมัดจำ (50%)</span><span class="text-green-600" x-text="formatNumber(depositAmount)"></span></div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">ช่องทางการชำระเงิน</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <button type="button" @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'ring-2 ring-green-500 bg-green-50 text-green-700 border-green-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'" class="flex flex-col items-center justify-center p-3 rounded-xl border transition h-24">
                                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg><span class="text-xs font-bold">เงินสด</span>
                                    </button>
                                    <button type="button" @click="paymentMethod = 'transfer'" :class="paymentMethod === 'transfer' ? 'ring-2 ring-blue-500 bg-blue-50 text-blue-700 border-blue-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'" class="flex flex-col items-center justify-center p-3 rounded-xl border transition h-24">
                                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg><span class="text-xs font-bold">โอนเงิน</span>
                                    </button>
                                    <button type="button" @click="paymentMethod = 'credit_card'" :class="paymentMethod === 'credit_card' ? 'ring-2 ring-purple-500 bg-purple-50 text-purple-700 border-purple-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'" class="flex flex-col items-center justify-center p-3 rounded-xl border transition h-24">
                                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg><span class="text-xs font-bold">บัตรเครดิต</span>
                                    </button>
                                </div>
                            </div>
                            <div x-show="selectedItem?.member" class="bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="text-indigo-700 font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg> ใช้แต้มแลกส่วนลด</span>
                                    <span class="text-xs bg-white px-2 py-0.5 rounded text-indigo-600 border border-indigo-200">มี <span x-text="selectedItem?.member?.points"></span> แต้ม</span>
                                </div>
                                <input type="number" x-model="pointsToUse" class="w-full text-sm rounded-lg border-indigo-200 focus:ring-indigo-500 focus:border-indigo-500" placeholder="ระบุจำนวนแต้มที่ต้องการใช้" min="0" :max="selectedItem?.member?.points">
                                <p class="text-[10px] text-indigo-400 mt-1 text-right">* 100 แต้ม = 1 บาท</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button @click="submitPayment" class="w-full inline-flex justify-center rounded-xl px-4 py-2.5 bg-green-600 text-base font-bold text-white hover:bg-green-700 shadow-lg transform transition hover:-translate-y-0.5 sm:w-auto">ยืนยันการชำระเงิน</button>
                        <button @click="showPaymentModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl px-4 py-2.5 bg-white text-base font-bold text-gray-700 hover:bg-gray-100 border border-gray-300 shadow-sm sm:mt-0 sm:w-auto transition">ยกเลิก</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✏️ EDIT MODAL --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showEditModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl w-full border border-gray-100">
                    <div class="bg-white px-6 pt-5 pb-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg></span>
                            แก้ไขรายการเช่า <span class="text-gray-400 font-mono text-base ml-2">#<span x-text="editForm.rental_id"></span></span>
                        </h3>
                        <button @click="showEditModal=false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                    </div>
                    <div class="bg-white px-6 py-6 modal-body-scroll">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-2 space-y-6">
                                <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">ระยะเวลาเช่า (7 วัน)</label>
                                    <div class="flex gap-4 items-center">
                                        <input type="date" x-model="editForm.rental_date" class="w-full rounded-xl border-gray-300 focus:ring-indigo-500 h-11" @change="updateReturnDate">
                                        <div class="text-sm"><span class="text-xs text-gray-400 block mb-0.5">คืนวันที่:</span><span class="font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100" x-text="returnDateDisplay"></span></div>
                                    </div>
                                </div>
                                {{-- Item & Acc Search --}}
                                <div class="relative">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">เพิ่มชุด/สินค้า</label>
                                    <div class="relative">
                                        <input type="text" x-model="itemQuery" @input.debounce.300ms="searchItems" placeholder="พิมพ์ชื่อชุด หรือรหัส..." class="w-full pl-11 rounded-xl border-gray-300 focus:ring-indigo-500 h-11">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg></div>
                                    </div>
                                    <div x-show="searchResults.length > 0" class="search-results mt-2">
                                        <template x-for="item in searchResults" :key="item.id">
                                            <div @click="addToEditCart(item, 'item')" class="search-item flex justify-between items-center group">
                                                <div>
                                                    <div class="text-sm font-bold text-gray-800" x-text="item.item_name"></div>
                                                    <div class="text-xs text-gray-400">ID: <span x-text="item.id"></span></div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-sm font-bold text-indigo-600" x-text="formatNumber(item.price)"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="relative">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">เพิ่มอุปกรณ์เสริม</label>
                                    <div class="relative">
                                        <input type="text" x-model="accQuery" placeholder="พิมพ์ชื่ออุปกรณ์..." class="w-full pl-11 rounded-xl border-gray-300 focus:ring-orange-500 h-11" @focus="showAccDropdown=true" @click.away="showAccDropdown=false">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg></div>
                                    </div>
                                    <div x-show="showAccDropdown && filteredAccessories.length > 0" class="search-results mt-2">
                                        <template x-for="acc in filteredAccessories" :key="acc.id">
                                            <div @click="addToEditCart(acc, 'acc'); showAccDropdown=false" class="search-item flex justify-between items-center group">
                                                <span class="text-sm font-medium text-gray-700" x-text="acc.name"></span>
                                                <span class="text-sm font-bold text-gray-500" x-text="formatNumber(acc.price)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                {{-- Services Selects --}}
                                <div class="p-5 bg-white rounded-xl border border-gray-200 shadow-sm grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1">โปรโมชั่น</label>
                                        <select x-model="editForm.promotion_id" class="w-full text-sm rounded-lg border-gray-300 focus:ring-indigo-500">
                                            <option value="">-- ไม่ใช้โปรโมชั่น --</option><template x-for="p in promotions" :key="p.promotion_id">
                                                <option :value="p.promotion_id" x-text="p.promotion_name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1">ช่างแต่งหน้า</label>
                                        <select x-model="editForm.makeup_id" class="w-full text-sm rounded-lg border-gray-300 focus:ring-pink-500">
                                            <option value="">-- ไม่รับบริการ --</option><template x-for="m in makeupArtists" :key="m.makeup_id">
                                                <option :value="m.makeup_id" x-text="m.first_name + ' (' + formatNumber(m.price) + ')'"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1">แพ็กเกจภาพ</label>
                                        <select x-model="editForm.package_id" class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500">
                                            <option value="">-- ไม่รับบริการ --</option><template x-for="pk in packages" :key="pk.package_id">
                                                <option :value="pk.package_id" x-text="pk.package_name + ' (' + formatNumber(pk.price) + ')'"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div x-show="editForm.package_id">
                                        <label class="block text-xs font-bold text-gray-500 mb-1">ช่างภาพ</label>
                                        <select x-model="editForm.photographer_id" class="w-full text-sm rounded-lg border-gray-300 focus:ring-blue-500">
                                            <option value="">-- ไม่ระบุ (ร้านจัดให้) --</option><template x-for="ph in photographers" :key="ph.photographer_id">
                                                <option :value="ph.photographer_id" x-text="ph.first_name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-200 h-full flex flex-col shadow-inner">
                                <h4 class="font-bold text-gray-800 mb-4 border-b border-gray-200 pb-3 flex justify-between items-center"><span>รายการที่เลือก</span><span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full" x-text="editForm.items.length + editForm.accessories.length"></span></h4>
                                <div class="flex-grow overflow-y-auto max-h-96 space-y-3 pr-1">
                                    <template x-for="(item, index) in editForm.items" :key="'e-i-'+index">
                                        <div class="flex justify-between items-center bg-white p-3 rounded-xl border border-gray-200 shadow-sm">
                                            <div>
                                                <div class="text-sm font-bold text-gray-800" x-text="item.item_name"></div>
                                                <div class="text-xs text-indigo-600 font-medium bg-indigo-50 inline-block px-1.5 rounded mt-0.5">ชุดหลัก @ <span x-text="formatNumber(item.price)"></span></div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button @click="item.quantity > 0 ? item.quantity-- : null" class="w-7 h-7 bg-gray-100 rounded text-xs">-</button>
                                                <span class="text-sm font-bold w-6 text-center" x-text="item.quantity"></span>
                                                <button @click="item.quantity++" class="w-7 h-7 bg-gray-100 rounded text-xs">+</button>
                                                <button @click="editForm.items.splice(index, 1)" class="ml-1 text-gray-300 hover:text-red-500">×</button>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-for="(acc, index) in editForm.accessories" :key="'e-a-'+index">
                                        <div class="flex justify-between items-center bg-white p-3 rounded-xl border border-orange-100 shadow-sm">
                                            <div>
                                                <div class="text-sm font-bold text-gray-800" x-text="acc.name"></div>
                                                <div class="text-xs text-orange-600 font-medium bg-orange-50 inline-block px-1.5 rounded mt-0.5">อุปกรณ์ @ <span x-text="formatNumber(acc.pivot.price)"></span></div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button @click="acc.pivot.quantity > 0 ? acc.pivot.quantity-- : null" class="w-7 h-7 bg-gray-100 rounded text-xs">-</button>
                                                <span class="text-sm font-bold w-6 text-center" x-text="acc.pivot.quantity"></span>
                                                <button @click="acc.pivot.quantity++" class="w-7 h-7 bg-gray-100 rounded text-xs">+</button>
                                                <button @click="editForm.accessories.splice(index, 1)" class="ml-1 text-gray-300 hover:text-red-500">×</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-300 space-y-2">
                                    <template x-if="discountAmount() > 0">
                                        <div class="flex justify-between text-xs text-green-600 font-medium"><span>ส่วนลดโปรโมชั่น</span><span x-text="'-' + formatNumber(discountAmount())"></span></div>
                                    </template>
                                    <div class="flex justify-between text-xl font-bold text-gray-900"><span>ยอดรวมใหม่</span><span class="text-indigo-600" x-text="formatNumber(calculateEditTotal())"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100 rounded-b-2xl">
                        <button @click="submitEdit" class="inline-flex justify-center rounded-xl px-6 py-3 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 shadow-lg transform transition hover:-translate-y-0.5">บันทึกการแก้ไข</button>
                        <button @click="showEditModal = false" class="inline-flex justify-center rounded-xl px-6 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-100 border border-gray-300 shadow-sm transition">ยกเลิก</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🧾 RECEIPT MODAL (แก้ไขให้มีที่อยู่ + ชื่อพนักงาน) --}}
        <div x-show="showReceipt" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showReceipt = false"></div>
                <div id="receipt-modal" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200">
                    <div class="bg-gray-900 text-white p-6 relative overflow-hidden">
                        <!-- <div class="absolute top-0 right-0 p-4 opacity-10"><svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            </svg></div> -->
                        <button @click="showReceipt=false" class="absolute top-4 right-4 text-white opacity-70 hover:opacity-100 no-print transition">✕</button>
                        <div class="relative z-10">
                            <center>
                            <h3 class="text-xl font-bold tracking-wide mb-1" x-text="receiptData?.status === 'returned' ? 'ใบเสร็จรับเงิน (คืนชุด)' : 'ใบเสร็จรับเงิน (มัดจำ)'"></h3>
                            <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em]">Receipt / Tax Invoice</p>
                            </center>
                            <div class="mt-6 border-t border-gray-700 pt-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-sm text-white">Watakacha Wedding & Studio</h4>
                                        <p class="text-xs text-gray-400 mt-1">499/130 หมู่บ้านรุ่งเรือง<br>ซ. 8 อำเภอสันทราย เชียงใหม่ 50210</p>
                                        <p class="text-xs text-gray-400 mt-1">โทร. 082-280-6989</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray-400 uppercase mb-0.5">รหัสรายการเช่า</p>
                                        <div class="bg-white/10 px-2 py-1 rounded text-sm font-mono font-bold tracking-wider">#<span x-text="receiptData?.rental_id"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-8 bg-white relative">
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
                                <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">วันที่ (Date)</p>
                                <p class="text-gray-900 text-sm font-medium" x-text="receiptData ? new Date(receiptData.created_at).toLocaleDateString('th-TH') : '-'"></p>
                            </div>
                        </div>
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
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-2">
                            <div class="flex justify-between text-xs text-gray-500"><span>ยอดรวมค่าเช่า</span><span x-text="formatNumber(receiptData?.total_amount)"></span></div>
                            <template x-if="receiptData?.status === 'returned'">
                                <div>
                                    <div class="flex justify-between text-xs text-green-600"><span>ชำระแล้ว (มัดจำ)</span><span x-text="'-' + formatNumber(getPaidAmount(receiptData, 'deposit'))"></span></div>
                                    <template x-if="receiptData.fine_amount > 0">
                                        <div class="flex justify-between text-xs text-red-600 font-bold mt-1"><span>ค่าปรับ / เสียหาย</span><span x-text="'+' + formatNumber(receiptData.fine_amount)"></span></div>
                                    </template>
                                    <div class="border-t border-gray-200 my-2"></div>
                                    <div class="flex justify-between text-base font-bold text-gray-900"><span>ยอดชำระวันคืน</span><span x-text="formatNumber(getPaidAmount(receiptData, 'fine_remaining'))"></span></div>
                                </div>
                            </template>
                            <template x-if="receiptData?.status !== 'returned'">
                                <div>
                                    <div class="flex justify-between items-center bg-green-50 p-2 rounded text-xs text-green-700 border border-green-100"><span>ชำระแล้ว (มัดจำ)</span><span class="font-bold" x-text="formatNumber(getPaidAmount(receiptData, 'deposit'))"></span></div>
                                    <div class="flex justify-between text-xs text-gray-500 pt-1"><span>คงเหลือ</span><span x-text="formatNumber(Math.max(0, receiptData?.total_amount - getPaidAmount(receiptData, 'deposit')))"></span></div>
                                </div>
                            </template>
                        </div>
                        <div class="mt-8 pt-4 border-t border-dashed border-gray-200">
                            <div class="flex justify-between items-end">
                                <div class="text-left">
                                    {{-- ✅ เพิ่มชื่อพนักงาน --}}
                                    <p class="text-[10px] text-gray-400 uppercase mb-1">ผู้ทำรายการ (Cashier)</p>
                                    <p class="text-xs font-bold text-gray-800">{{ Auth::user()->first_name }}</p>
                                </div>
                                <div class="text-center">
                                    <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=${encodeURIComponent('Rental:' + receiptData?.rental_id)}`" class="w-16 h-16 mix-blend-multiply opacity-80 border p-1 rounded bg-white shadow-sm">
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <p class="font-bold text-gray-800 text-xs">ขอบคุณที่ใช้บริการ</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Thank you for your support</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 no-print flex gap-3 border-t border-gray-100 rounded-b-xl">
                        <button @click="window.print()" class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            พิมพ์ใบเสร็จ
                        </button>
                        <button @click="showReceipt = false" class="flex-1 py-2.5 bg-gray-900 text-white font-bold rounded-xl shadow-lg hover:bg-black transition">ปิดหน้าต่าง</button>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- ✅ ปิด div ของ x-data ครบถ้วน --}}

    <script>
        function historySystem(initData) {
            return {
                promotions: initData.promotions || [],
                makeupArtists: initData.makeupArtists || [],
                packages: initData.packages || [],
                photographers: initData.photographers || [],
                accessoriesList: initData.accessoriesList || [],

                activeTab: '{{ request("tab") ?? (count($pending) > 0 ? "pending" : "active") }}',
                showPaymentModal: false,
                showEditModal: false,
                showReceipt: false,

                selectedItem: null,
                receiptData: null,
                paymentMethod: 'cash',
                pointsToUse: 0,

                editForm: {
                    rental_id: '',
                    rental_date: '',
                    items: [],
                    accessories: [],
                    promotion_id: '',
                    makeup_id: '',
                    photographer_id: '',
                    package_id: ''
                },
                itemQuery: '',
                searchResults: [],
                accQuery: '',
                showAccDropdown: false,

                get filteredAccessories() {
                    if (this.accQuery === '') return this.accessoriesList;
                    return this.accessoriesList.filter(acc => acc.name.toLowerCase().includes(this.accQuery.toLowerCase()));
                },

                get depositAmount() {
                    if (!this.selectedItem) return 0;
                    let amount = parseFloat(this.selectedItem.total_amount) * 0.5;
                    if (this.pointsToUse > 0) amount -= Math.floor(this.pointsToUse / 100);
                    return Math.max(0, amount);
                },

                get returnDateDisplay() {
                    if (!this.editForm.rental_date) return '-';
                    let d = new Date(this.editForm.rental_date);
                    d.setDate(d.getDate() + 6);
                    return d.toLocaleDateString('th-TH');
                },

                updateReturnDate() {},

                formatNumber(num) {
                    return new Intl.NumberFormat('th-TH', {
                        minimumFractionDigits: 2
                    }).format(num || 0);
                },

                // ✅ Helper สำหรับคำนวณยอดเงินในใบเสร็จ
                getPaidAmount(item, type = null) {
                    if (!item || !item.payments) return 0;
                    return item.payments.reduce((sum, p) => {
                        if (p.status === 'paid' && (!type || p.type === type)) {
                            return sum + parseFloat(p.amount);
                        }
                        return sum;
                    }, 0);
                },

                openPaymentModal(item) {
                    this.selectedItem = item;
                    this.pointsToUse = 0;
                    this.showPaymentModal = true;
                },
                openReceiptModal(item) {
                    this.receiptData = item;
                    this.showReceipt = true;
                },

                openEditModal(item) {
                    try {
                        this.editForm.rental_id = item.rental_id;
                        this.editForm.rental_date = item.rental_date ? new Date(item.rental_date).toISOString().split('T')[0] : '';

                        this.editForm.items = item.items ? item.items.map(i => ({
                            id: i.item_id,
                            item_id: i.item_id,
                            item_name: i.item ? i.item.item_name : 'Unknown',
                            price: parseFloat(i.price),
                            quantity: i.quantity,
                            available_stock: 999
                        })) : [];

                        this.editForm.accessories = item.accessories ? item.accessories.map(a => ({
                            id: a.id,
                            name: a.name,
                            pivot: {
                                price: parseFloat(a.pivot.price),
                                quantity: a.pivot.quantity
                            }
                        })) : [];

                        this.editForm.promotion_id = item.promotion_id;
                        this.editForm.makeup_id = item.makeup_id;
                        this.editForm.photographer_id = item.photographer_id;
                        this.editForm.package_id = item.package_id;

                        this.showEditModal = true;
                    } catch (e) {
                        console.error(e);
                        Swal.fire('Error', 'ข้อมูลไม่สมบูรณ์', 'error');
                    }
                },

                async searchItems() {
                    if (this.itemQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    try {
                        const res = await fetch(`{{ route('reception.searchItems') }}?q=${this.itemQuery}&rental_date=${this.editForm.rental_date}`);
                        this.searchResults = await res.json();
                    } catch (e) {
                        console.error(e);
                    }
                },

                addToEditCart(item, type) {
                    if (type === 'item') {
                        let existing = this.editForm.items.find(i => i.item_id == item.id);
                        if (existing) existing.quantity++;
                        else this.editForm.items.push({
                            item_id: item.id,
                            id: item.id,
                            item_name: item.item_name,
                            price: parseFloat(item.price),
                            quantity: 1,
                            available_stock: item.available_stock
                        });
                        this.searchResults = [];
                        this.itemQuery = '';
                    } else if (type === 'acc') {
                        let existing = this.editForm.accessories.find(a => a.id == item.id);
                        if (existing) existing.pivot.quantity++;
                        else this.editForm.accessories.push({
                            id: item.id,
                            name: item.name,
                            pivot: {
                                price: parseFloat(item.price),
                                quantity: 1
                            }
                        });
                    }
                },

                discountAmount() {
                    let total = 0;
                    this.editForm.items.forEach(i => total += (i.price * i.quantity));
                    this.editForm.accessories.forEach(a => total += (a.pivot.price * a.pivot.quantity));
                    const promo = this.promotions.find(p => p.promotion_id == this.editForm.promotion_id);
                    if (!promo) return 0;
                    if (promo.discount_type === 'percentage') return (total * promo.discount_value / 100);
                    else return promo.discount_value;
                },

                calculateEditTotal() {
                    let total = 0;
                    this.editForm.items.forEach(i => {
                        if (i.quantity > 0) total += (i.price * i.quantity);
                    });
                    this.editForm.accessories.forEach(a => {
                        if (a.pivot.quantity > 0) total += (a.pivot.price * a.pivot.quantity);
                    });

                    const mk = this.makeupArtists.find(m => m.makeup_id == this.editForm.makeup_id);
                    if (mk) total += parseFloat(mk.price);

                    if (this.editForm.package_id) {
                        const pkg = this.packages.find(p => p.package_id == this.editForm.package_id);
                        if (pkg) total += parseFloat(pkg.price);
                    }
                    total -= this.discountAmount();
                    return Math.max(0, total);
                },

                async submitEdit() {
                    try {
                        const res = await fetch(`/admin/reception/rental/${this.editForm.rental_id}/update`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                rental_date: this.editForm.rental_date,
                                items: this.editForm.items.filter(i => i.quantity > 0).map(i => ({
                                    item_id: i.item_id,
                                    quantity: i.quantity,
                                    price: i.price
                                })),
                                accessories: this.editForm.accessories.filter(a => a.pivot.quantity > 0).map(a => ({
                                    id: a.id,
                                    quantity: a.pivot.quantity
                                })),
                                promotion_id: this.editForm.promotion_id,
                                makeup_id: this.editForm.makeup_id,
                                photographer_id: this.editForm.package_id ? this.editForm.photographer_id : null,
                                package_id: this.editForm.package_id,
                                total_amount: this.calculateEditTotal()
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            Swal.fire('สำเร็จ', 'แก้ไขข้อมูลเรียบร้อย', 'success').then(() => window.location.reload());
                        } else {
                            Swal.fire('แจ้งเตือน', data.message, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error', 'เกิดข้อผิดพลาดในการบันทึก', 'error');
                    }
                },

                async submitPayment() {
                    if (!this.selectedItem) return;
                    try {
                        const res = await fetch(`/admin/reception/rental/${this.selectedItem.rental_id}/confirm-payment`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                amount: this.depositAmount,
                                payment_method: this.paymentMethod,
                                points_used: this.pointsToUse
                            })
                        });
                        const data = await res.json();
                        if (data.success) Swal.fire('สำเร็จ', 'บันทึกการชำระเงินแล้ว', 'success').then(() => window.location.reload());
                        else Swal.fire('ผิดพลาด', data.message, 'error');
                    } catch (error) {
                        Swal.fire('Error', 'Connection Error', 'error');
                    }
                },
                async confirmPickup(rentalId) {
                    Swal.fire({
                        title: 'ยืนยันรับชุด?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'ยืนยัน'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            const res = await fetch(`/admin/reception/rental/${rentalId}/confirm-pickup`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            if (res.ok) window.location.reload();
                        }
                    });
                },
                async confirmCancel(rentalId) {
                    Swal.fire({
                        title: 'ยืนยันยกเลิก?',
                        text: 'ไม่สามารถกู้คืนได้',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'ยกเลิกบิล'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            const res = await fetch(`/admin/reception/rental/${rentalId}/cancel`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            if (res.ok) window.location.reload();
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>