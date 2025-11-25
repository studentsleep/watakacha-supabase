<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i data-lucide="sparkles" class="inline w-6 h-6 mr-2"></i>
            ประวัติการบริการ (Services History)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Filters --}}
            <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                <form action="{{ route('reception.serviceHistory') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                    
                    {{-- Search --}}
                    <div class="flex-grow">
                        <label class="text-sm text-gray-500 dark:text-gray-400">ค้นหา (ลูกค้า / ชื่อช่าง)</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="พิมพ์ชื่อ..." 
                                   class="w-full pl-10 rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm h-10">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i data-lucide="search" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Type --}}
                    <div class="w-48">
                        <label class="text-sm text-gray-500 dark:text-gray-400">ประเภทบริการ</label>
                        <select name="type" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm h-10">
                            <option value="all">ทั้งหมด</option>
                            <option value="makeup" {{ request('type') == 'makeup' ? 'selected' : '' }}>💄 แต่งหน้า</option>
                            <option value="photo" {{ request('type') == 'photo' ? 'selected' : '' }}>📷 ถ่ายภาพ</option>
                        </select>
                    </div>

                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md h-10 shadow transition flex items-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4"></i> กรองข้อมูล
                    </button>
                    
                    @if(request()->has('type') || request()->has('search'))
                        <a href="{{ route('reception.serviceHistory') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 h-10 flex items-center">
                            ล้างค่า
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($services->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                            <p>ไม่พบประวัติการใช้บริการ</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">วันที่ใช้บริการ</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">ลูกค้า (Member)</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">บริการแต่งหน้า</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">บริการถ่ายภาพ</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">สถานะบิล</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @foreach($services as $service)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        
                                        {{-- วันที่ --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ \Carbon\Carbon::parse($service->rental_date)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Bill #{{ $service->rental_id }}
                                            </div>
                                        </td>

                                        {{-- ลูกค้า --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($service->member)
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold mr-3">
                                                        {{ substr($service->member->first_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $service->member->first_name }} {{ $service->member->last_name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">{{ $service->member->tel }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">ลูกค้าทั่วไป (Guest)</span>
                                            @endif
                                        </td>

                                        {{-- บริการแต่งหน้า --}}
                                        <td class="px-6 py-4">
                                            @if($service->makeup_id)
                                                <div class="flex items-start gap-2">
                                                    <span class="p-1 bg-pink-100 text-pink-600 rounded-md">
                                                        <i data-lucide="brush" class="w-4 h-4"></i>
                                                    </span>
                                                    <div>
                                                        <div class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                                            ช่าง{{ $service->makeupArtist->first_name ?? '-' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            ราคา: {{ number_format($service->makeupArtist->price ?? 0) }} บาท
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>

                                        {{-- บริการถ่ายภาพ --}}
                                        <td class="px-6 py-4">
                                            @if($service->photographer_id)
                                                <div class="flex items-start gap-2">
                                                    <span class="p-1 bg-blue-100 text-blue-600 rounded-md">
                                                        <i data-lucide="camera" class="w-4 h-4"></i>
                                                    </span>
                                                    <div>
                                                        <div class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                                            ช่าง{{ $service->photographer->first_name ?? '-' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            แพ็กเกจ: {{ $service->photographerPackage->package_name ?? '-' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            ราคา: {{ number_format($service->photographerPackage->price ?? 0) }} บาท
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>

                                        {{-- สถานะ --}}
                                        <td class="px-6 py-4 text-center">
                                            @if($service->status == 'returned')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    สำเร็จ (คืนแล้ว)
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    กำลังดำเนินงาน
                                                </span>
                                            @endif
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $services->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>