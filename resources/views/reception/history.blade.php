<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ประวัติการเช่า (Rental History)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Filters --}}
            <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                <form action="{{ route('reception.history') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-grow">
                        <label class="text-sm text-gray-500 dark:text-gray-400">ค้นหา</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="ID หรือ ชื่อลูกค้า" 
                               class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm h-10">
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">สถานะ</label>
                        <select name="status" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm h-10">
                            <option value="all">ทั้งหมด</option>
                            <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>กำลังเช่า (Rented)</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>คืนแล้ว (Returned)</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 h-10">
                        กรองข้อมูล
                    </button>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ลูกค้า</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">วันที่เช่า</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ยอดรวม</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">สถานะ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">พนักงาน</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                                @foreach($rentals as $rental)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4">#{{ $rental->rental_id }}</td>
                                    <td class="px-6 py-4">
                                        {{ $rental->member ? $rental->member->first_name . ' ' . $rental->member->last_name : 'Guest' }}
                                    </td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($rental->rental_date)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">{{ number_format($rental->total_amount, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @if($rental->status == 'rented')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                กำลังเช่า
                                            </span>
                                        @elseif($rental->status == 'returned')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                คืนแล้ว
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $rental->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $rental->user->name ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $rentals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>