{{-- 
    นี่คือไฟล์ View หลัก (Hybrid)
    - ใช้ Layout เดิม (<x-app-layout>)
    - ใช้ "ตารางแบบเก่า" (จาก partials/ เดิม)
    - ใช้ "ปุ่ม JS ธรรมดา" (onclick="toggleModal(...)")
    - เรียก "Modal แบบใหม่" (add-tailwind.blade.php)
--}}
@php
    // (Logic การตั้งชื่อ Title ยังคงเดิม)
    $tableTitles = [
        'users' => 'จัดการบัญชีผู้ใช้',
        'user_types' => 'จัดการประเภทผู้ใช้',
        'items' => 'จัดการสินค้า',
        'item_types' => 'จัดการประเภทสินค้า',
        'item_units' => 'จัดการหน่วยสินค้า',
    ];
    $title = $tableTitles[$table] ?? 'การจัดการข้อมูล';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __($title) }}
        </h2>
    </x-slot>

    {{-- ▼▼▼ [ใหม่] เพิ่มส่วนแสดงผล Error หรือ Status ▼▼▼ --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Message (แสดงผลการทำงาน) -->
            @if(session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            {{-- ^^^ [ใหม่] จบส่วนแสดงผล Error หรือ Status ^^^ --}}

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- ▼▼▼ [Hybrid] ส่วนของตาราง Items (ตารางแบบเก่า + ปุ่มแบบใหม่) ▼▼▼ --}}
                    @if($table == 'items')
                        
                        <!-- [ปุ่มแบบใหม่] (JS ธรรมดา) -->
                        <div class="flex justify-end mb-4">
                            <x-primary-button type="button" onclick="toggleModal('addItemModal', true)">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                เพิ่มสินค้า
                            </x-primary-button>
                        </div>

                        <!-- [ตารางแบบเก่า] (โครงสร้างเดิม) -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    
                                    {{-- [ดึงข้อมูลแบบใหม่] (ตัวแปร $items มาจาก Controller ใหม่) --}}
                                    @forelse($items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @php
                                                    $mainImage = $item->images->firstWhere('is_main', true) ?? $item->images->first();
                                                    $imageUrl = $mainImage ? asset('storage/' . $mainImage->path) : 'https://placehold.co/100x100/e2e8f0/94a3b8?text=No+Image';
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="Item Image" class="w-10 h-10 rounded object-cover">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $item->item_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->price }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->stock }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->type->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->unit->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                
                                                <!-- [ปุ่มแบบใหม่] (JS ธรรมดา) -->
                                                <x-secondary-button type="button" 
                                                    onclick="toggleModal('updateItemModal-{{ $item->id }}', true)"
                                                    class="!px-2 !py-1"
                                                    title="แก้ไขข้อมูลหลัก">
                                                    <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                                </x-secondary-button>
                                                
                                                <!-- [ปุ่มแบบใหม่] (JS ธรรมดา - สำหรับจัดการรูปภาพ) -->
                                                <x-secondary-button type="button" 
                                                    onclick="toggleModal('updateImageModal-{{ $item->id }}', true)"
                                                    class="!px-2 !py-1 ml-1"
                                                    title="จัดการรูปภาพ">
                                                    <i data-lucide="image" class="w-5 h-5"></i>
                                                </x-secondary-button>

                                                <!-- (ปุ่มลบยังใช้ Form เดิมได้) -->
                                                <form action="{{ route('manager.items.destroy', $item->id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button type="submit" class="!px-2 !py-1" title="ลบ">
                                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                    </x-danger-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                                ไม่พบข้อมูลสินค้า (กรุณากดปุ่ม "เพิ่มสินค้า" เพื่อสร้างข้อมูล)
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links (แบบเดิม) -->
                        <div class="mt-4">
                            {{ $items->links() }}
                        </div>

                        {{-- [หน้าต่างแบบใหม่] (เรียกใช้ Modal ที่สร้างด้วย Tailwind) --}}
                        @include('manager.modals.add-tailwind', ['types' => $types, 'units' => $units])
                        @foreach($items as $item)
                            @include('manager.modals.update-tailwind', ['item' => $item, 'types' => $types, 'units' => $units])
                        @endforeach
                    
                    {{-- ▼▼▼ (ส่วนนี้คือโค้ดเดิมสำหรับตารางอื่นๆ) ▼▼▼ --}}
                    @elseif($table == 'users')
                        {{-- (คุณต้องไปสร้างปุ่มและ Modal สำหรับ Users เอง โดยใช้แนวทางเดียวกับ Items) --}}
                        @include('manager.partials.users', compact('users', 'user_types'))

                    @elseif($table == 'user_types')
                        {{-- (คุณต้องไปสร้างปุ่มและ Modal สำหรับ User Types เอง) --}}
                        @include('manager.partials.user-types', compact('user_types'))

                    @elseif($table == 'item_types')
                         {{-- (คุณต้องไปสร้างปุ่มและ Modal สำหรับ Item Types เอง) --}}
                        @include('manager.partials.item-types', ['types' => $types])

                    @elseif($table == 'item_units')
                         {{-- (คุณต้องไปสร้างปุ่มและ Modal สำหรับ Item Units เอง) --}}
                        @include('manager.partials.item-units', ['units' => $units])
                    
                    @else
                        <p>กรุณาเลือกตารางที่ต้องการจัดการจากเมนูด้านข้าง</p>
                    @endif
                    {{-- === จบส่วนโค้ดเดิม === --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>