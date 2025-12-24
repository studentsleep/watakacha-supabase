<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ประวัติการชำระเงิน (Payment History)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Search Box --}}
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mb-6">
                <form action="{{ route('reception.paymentHistory') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"
                           placeholder="ค้นหาเลขที่บิล หรือ ชื่อลูกค้า...">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700">ค้นหา</button>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">วันที่ชำระ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">เลขที่บิล</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ลูกค้า</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ประเภท</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ยอดเงิน</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">ช่องทาง</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $payment->payment_date->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold">
                                    #{{ $payment->rental_id }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $payment->rental->member->first_name ?? 'Guest' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($payment->type == 'deposit') <span class="text-blue-600 font-bold">มัดจำ</span>
                                    @elseif($payment->type == 'fine') <span class="text-red-600 font-bold">ค่าปรับ</span>
                                    @else <span>ส่วนที่เหลือ</span> @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-mono font-bold text-gray-700 dark:text-gray-300">
                                    {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    {{ $payment->payment_method }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{-- ปุ่มลบ (ถ้าต้องการให้ Manager ลบได้) --}}
                                    @if(Auth::user()->user_type_id == 1)
                                    <form action="{{ route('payments.destroy', $payment->payment_id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบ? ยอดเงินจะหายไปจากระบบ')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>