<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="20" height="14" x="2" y="5" rx="2" />
                    <line x1="2" x2="22" y1="10" y2="10" />
                </svg>
            </div>
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ประวัติการชำระเงิน (Payment History)
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🔍 Filter Bar --}}
            <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form action="{{ route('reception.paymentHistory') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:flex-grow">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">ค้นหาข้อมูล</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="เลขที่บิล, ชื่อลูกค้า..."
                                class="w-full pl-10 rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 h-11 transition-all">
                        </div>
                    </div>
                    <button type="submit" class="w-full md:w-auto px-6 h-11 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                        </svg>
                        กรองข้อมูล
                    </button>
                    @if(request('search'))
                    <a href="{{ route('reception.paymentHistory') }}" class="w-full md:w-auto px-4 h-11 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition flex items-center justify-center">
                        ล้างค่า
                    </a>
                    @endif
                </form>
            </div>

            {{-- 📋 Data Table --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    @if($payments->isEmpty())
                    <div class="text-center py-16 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-lg font-medium">ไม่พบประวัติการชำระเงิน</p>
                        <p class="text-sm">ลองเปลี่ยนคำค้นหาดูนะครับ</p>
                    </div>
                    @else
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reference ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ลูกค้า</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">วันที่ชำระ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">ประเภท</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">ช่องทาง</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">ยอดเงิน</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150 group">

                                {{-- Reference ID (ลิ้งค์ไปหน้าประวัติ) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('reception.history', ['search' => $payment->rental_id]) }}" class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded hover:bg-indigo-100 transition">
                                        #{{ $payment->rental_id }}
                                    </a>
                                </td>

                                {{-- ลูกค้า (Avatar) --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-tr from-emerald-400 to-teal-500 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                            {{ mb_substr($payment->rental->member ? $payment->rental->member->first_name : 'G', 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $payment->rental->member ? $payment->rental->member->first_name . ' ' . $payment->rental->member->last_name : 'ลูกค้าทั่วไป (Guest)' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $payment->rental->member ? $payment->rental->member->tel : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- วันที่ชำระ --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->addYears(543)->locale('th')->translatedFormat('d M Y') }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('H:i') }} น.
                                        </span>
                                    </div>
                                </td>

                                {{-- ประเภท (Badge) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($payment->type == 'deposit')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                        มัดจำ
                                    </span>
                                    @elseif($payment->type == 'fine')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                        ค่าปรับ
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                        ส่วนที่เหลือ
                                    </span>
                                    @endif
                                </td>

                                {{-- ช่องทาง --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                    @switch($payment->payment_method)
                                    @case('cash')
                                    <span class="flex items-center justify-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        เงินสด
                                    </span>
                                    @break
                                    @case('transfer')
                                    <span class="flex items-center justify-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        โอนเงิน
                                    </span>
                                    @break
                                    @case('credit_card')
                                    <span class="flex items-center justify-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        บัตรเครดิต
                                    </span>
                                    @break
                                    @default
                                    {{ $payment->payment_method }}
                                    @endswitch
                                </td>

                                {{-- ยอดเงิน --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="font-mono font-bold text-green-200 text-sm">
                                        {{ number_format($payment->amount, 2) }}
                                    </span>
                                </td>


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>

                {{-- Pagination --}}
                @if($payments->hasPages())
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $payments->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>