<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-yellow-100 rounded-lg text-yellow-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="8" />
                    <line x1="12" y1="18" x2="12" y2="12" />
                    <line x1="12" y1="6" x2="12.01" y2="6" />
                </svg> {{-- หรือไอคอน coins --}}
            </div>
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ประวัติการใช้แต้ม (Point History)
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🔍 Filter Bar --}}
            <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form action="{{ route('reception.pointHistory') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:flex-grow">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">ค้นหาสมาชิก</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="ชื่อสมาชิก, รหัสสมาชิก..."
                                class="w-full pl-10 rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500 h-11 transition-all">
                        </div>
                    </div>
                    <button type="submit" class="w-full md:w-auto px-6 h-11 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                        </svg>
                        กรองข้อมูล
                    </button>
                    @if(request('search'))
                    <a href="{{ route('reception.pointHistory') }}" class="w-full md:w-auto px-4 h-11 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition flex items-center justify-center">
                        ล้างค่า
                    </a>
                    @endif
                </form>
            </div>

            {{-- 📋 Data Table --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reference ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">สมาชิก</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">วันที่ทำรายการ</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">รายละเอียด</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">ประเภท</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">แต้ม (Points)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse($transactions as $tx)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150 group">

                                {{-- Reference ID (ใช้ Rental ID ถ้ามี หรือเว้นว่าง) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($tx->rental_id)
                                    <a href="{{ route('reception.history', ['search' => $tx->rental_id]) }}" class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded hover:bg-indigo-100 transition">
                                        #{{ $tx->rental_id }}
                                    </a>
                                    @else
                                    <span class="text-gray-400 font-mono text-sm">-</span>
                                    @endif
                                </td>

                                {{-- สมาชิก (Avatar Style) --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-tr from-yellow-400 to-orange-500 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                            {{ mb_substr($tx->member->first_name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $tx->member->first_name ?? '-' }} {{ $tx->member->last_name ?? '' }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $tx->member->member_id ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- วันที่ทำรายการ --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d M Y') }}</span>
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('H:i') }} น.</span>
                                    </div>
                                </td>

                                {{-- รายละเอียด --}}
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $tx->description }}
                                </td>

                                {{-- ประเภท (Badge) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($tx->change_type == 'earn')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                        </svg>
                                        ได้รับ
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                        </svg>
                                        ใช้ไป
                                    </span>
                                    @endif
                                </td>

                                {{-- แต้ม --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="text-sm font-bold {{ $tx->point_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $tx->point_change > 0 ? '+' : '' }}{{ number_format($tx->point_change) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="font-medium">ไม่พบประวัติการใช้แต้ม</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($transactions->hasPages())
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $transactions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>