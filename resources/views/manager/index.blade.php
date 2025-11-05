{{-- 
    นี่คือไฟล์ View หลัก (Hybrid) ที่ยกเครื่องใหม่ทั้งหมด
    - ใช้ Layout เดิม (<x-app-layout>)
    - ใช้ "ปุ่ม JS ธรรมดา" (onclick="toggleModal(...)")
    - เรียก "Modal แบบใหม่"
    - [ใหม่] ใช้ตารางแบบ Tailwind สำหรับทุกตาราง
--}}
@php
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

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-200" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- ▼▼▼ 1. ตาราง Items (สมบูรณ์แล้ว) ▼▼▼ --}}
                    @if($table == 'items')
                        
                        <div class="flex justify-end mb-4">
                            <x-primary-button type="button" onclick="toggleModal('addItemModal', true)">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มสินค้า
                            </x-primary-button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Image</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @php
                                                    $mainImage = $item->images->firstWhere('is_main', true) ?? $item->images->first();
                                                    $imageUrl = $mainImage ? asset('storage/'. $mainImage->path) : 'https://placehold.co/100x100/e2e8f0/94a3b8?text=No+Image';
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="Item Image" class="w-10 h-10 rounded object-cover">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $item->item_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->price }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->stock }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->type->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->unit->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <x-secondary-button type="button" onclick="toggleModal('updateItemModal-{{ $item->id }}', true)" class="!px-2 !py-1" title="แก้ไขข้อมูลหลัก">
                                                    <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                                </x-secondary-button>
                                                <x-secondary-button type="button" onclick="toggleModal('updateImageModal-{{ $item->id }}', true)" class="!px-2 !py-1 ml-1" title="จัดการรูปภาพ">
                                                    <i data-lucide="image" class="w-5 h-5"></i>
                                                </x-secondary-button>
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
                                        <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">ไม่พบข้อมูลสินค้า</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $items->links() }}</div>
                        @include('manager.modals.add-tailwind', ['types' => $types, 'units' => $units])
                        @foreach($items as $item)
                            @include('manager.modals.update-tailwind', ['item' => $item, 'types' => $types, 'units' => $units])
                        @endforeach
                    
                    {{-- ▼▼▼ 2. [ใหม่] ตาราง Users ▼▼▼ --}}
                    @elseif($table == 'users')
                        
                        <div class="flex justify-end mb-4">
                            <x-primary-button type="button" onclick="toggleModal('addUserModal', true)">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มผู้ใช้ใหม่
                            </x-primary-button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Username</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Full Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($users as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $user->username }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->first_name }} {{ $user->last_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->userType->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $user->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <x-secondary-button type="button" onclick="toggleModal('updateUserModal-{{ $user->user_id }}', true)" class="!px-2 !py-1" title="แก้ไข">
                                                    <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                                </x-secondary-button>
                                                <form action="{{ route('manager.users.destroy', $user->user_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button type="submit" class="!px-2 !py-1" title="ลบ">
                                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                    </x-danger-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">ไม่พบข้อมูลผู้ใช้</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $users->links() }}</div>
                        @include('manager.modals.add-user', ['user_types' => $user_types])
                        @foreach($users as $user)
                            @include('manager.modals.update-user', ['user' => $user, 'user_types' => $user_types])
                        @endforeach

                    {{-- ▼▼▼ 3. [ใหม่] ตาราง User Types ▼▼▼ --}}
                    @elseif($table == 'user_types')
                        
                        <div class="flex justify-end mb-4">
                            <x-primary-button type="button" onclick="toggleModal('addUserTypeModal', true)">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มประเภทผู้ใช้
                            </x-primary-button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($user_types as $type)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $type->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $type->description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <x-secondary-button type="button" onclick="toggleModal('updateUserTypeModal-{{ $type->user_type_id }}', true)" class="!px-2 !py-1" title="แก้ไข">
                                                    <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                                </x-secondary-button>
                                                <form action="{{ route('manager.user_types.destroy', $type->user_type_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบประเภทนี้?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button type="submit" class="!px-2 !py-1" title="ลบ">
                                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                    </x-danger-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">ไม่พบข้อมูลประเภทผู้ใช้</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @include('manager.modals.add-user-type')
                        @foreach($user_types as $type)
                            @include('manager.modals.update-user-type', ['type' => $type])
                        @endforeach

                    {{-- ▼▼▼ 4. [ใหม่] ตาราง Item Types ▼▼▼ --}}
                    @elseif($table == 'item_types')
                        
                        <div class="flex justify-end mb-4">
                            <x-primary-button type="button" onclick="toggleModal('addTypeModal', true)">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มประเภทสินค้า
                            </x-primary-button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($types as $type)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $type->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $type->description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <x-secondary-button type="button" onclick="toggleModal('updateTypeModal-{{ $type->item_type_id }}', true)" class="!px-2 !py-1" title="แก้ไข">
                                                    <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                                </x-secondary-button>
                                                <form action="{{ route('manager.types.destroy', $type->item_type_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบประเภทนี้?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button type="submit" class="!px-2 !py-1" title="ลบ">
                                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                    </x-danger-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">ไม่พบข้อมูลประเภทสินค้า</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @include('manager.modals.add-type')
                        @foreach($types as $type)
                            @include('manager.modals.update-type', ['type' => $type])
                        @endforeach

                    {{-- ▼▼▼ 5. [ใหม่] ตาราง Item Units ▼▼▼ --}}
                    @elseif($table == 'item_units')
                        
                        <div class="flex justify-end mb-4">
                            <x-primary-button type="button" onclick="toggleModal('addUnitModal', true)">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มหน่วยสินค้า
                            </x-primary-button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($units as $unit)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $unit->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $unit->description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <x-secondary-button type="button" onclick="toggleModal('updateUnitModal-{{ $unit->item_unit_id }}', true)" class="!px-2 !py-1" title="แก้ไข">
                                                    <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                                </x-secondary-button>
                                                <form action="{{ route('manager.units.destroy', $unit->item_unit_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบหน่วยนี้?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button type="submit" class="!px-2 !py-1" title="ลบ">
                                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                    </x-danger-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">ไม่พบข้อมูลหน่วยสินค้า</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @include('manager.modals.add-unit')
                        @foreach($units as $unit)
                            @include('manager.modals.update-unit', ['unit' => $unit])
                        @endforeach
                    
                    @else
                        <p>กรุณาเลือกตารางที่ต้องการจัดการจากเมนูด้านข้าง</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>