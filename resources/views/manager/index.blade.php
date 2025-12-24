{{-- resources/views/manager/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __($title ?? 'การจัดการข้อมูล') }}
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

                    {{-- ส่วนหัว: ปุ่มเพิ่ม + ช่องค้นหา + ตัวกรอง --}}
                    {{-- [แก้ไขจุดที่ 2] เพิ่ม 'item_units' ใน array เพื่อให้แสดงช่องค้นหา --}}
                    @if(in_array($table, ['items', 'accessories', 'users', 'member_accounts', 'care_shops', 'makeup_artists', 'photographers', 'photographer_packages', 'promotions', 'point_transactions', 'item_units', 'user_types', 'item_types']))
                    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">

                        <form method="GET" action="{{ route('manager.index') }}" class="w-full xl:w-auto flex flex-col sm:flex-row gap-2">
                            <input type="hidden" name="table" value="{{ $table }}">

                            <div class="relative">
                                <x-text-input type="text" name="search" placeholder="ค้นหา..." value="{{ request('search') }}" class="w-full sm:w-64" />
                            </div>

                            @if(in_array($table, ['users', 'member_accounts', 'care_shops', 'makeup_artists', 'photographers', 'promotions']))
                            <select name="status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" onchange="this.form.submit()">
                                <option value="">สถานะทั้งหมด</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>กำลังใช้งาน</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ระงับการใช้งาน</option>
                            </select>
                            @endif

                            @if($table == 'users' && isset($user_types))
                            <select name="type_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" onchange="this.form.submit()">
                                <option value="">ประเภททั้งหมด</option>
                                @foreach($user_types as $type)
                                <option value="{{ $type->user_type_id }}" {{ request('type_id') == $type->user_type_id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            @endif

                            <x-primary-button type="submit">
                                <i data-lucide="search" class="w-4 h-4"></i>
                            </x-primary-button>

                            @if(request('search') || request('status') || request('type_id'))
                            <a href="{{ route('manager.index', ['table' => $table]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 whitespace-nowrap">
                                ล้างค่า
                            </a>
                            @endif
                        </form>

                        @if($table != 'point_transactions')
                        <div class="self-end sm:self-auto">
                            @php
                            $modalMap = [
                            'items' => 'addItemModal',
                            'accessories' => 'addAccessoryModal',
                            'users' => 'addUserModal',
                            'user_types' => 'addUserTypeModal',
                            'item_types' => 'addTypeModal',
                            'item_units' => 'addUnitModal',
                            'member_accounts' => 'addMemberModal',
                            'care_shops' => 'addCareShopModal',
                            'makeup_artists' => 'addMakeupArtistModal',
                            'photographers' => 'addPhotographerModal',
                            'photographer_packages' => 'addPhotographerPackageModal',
                            'promotions' => 'addPromotionModal',
                            ];
                            $modalName = $modalMap[$table] ?? '';
                            @endphp
                            @if($modalName)
                            <x-primary-button type="button" onclick="toggleModal('{{ $modalName }}', true)">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มข้อมูล
                            </x-primary-button>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- (เนื้อหาตารางอื่นๆ ... คงเดิม) --}}
                    @if($table == 'items')
                    {{-- ... (ตาราง Items คงเดิม) ... --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">รูปภาพ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อสินค้า (ประเภท)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ราคาเช่า</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">คงเหลือ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $items->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                        $mainImage = $item->images->firstWhere('is_main', true) ?? $item->images->first();
                                        $imageUrl = $mainImage ? asset('storage/'. $mainImage->path) : 'https://placehold.co/100x100/e2e8f0/94a3b8?text=No+Image';
                                        @endphp
                                        <img src="{{ $imageUrl }}" alt="Item Image" class="w-12 h-12 rounded object-cover border border-gray-200">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{ $item->item_name }}
                                        <span class="text-xs text-gray-500 font-normal">({{ $item->type->name ?? '-' }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ number_format($item->price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ number_format($item->stock) }} {{ $item->unit->name ?? 'ชิ้น' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updateItemModal-{{ $item->id }}', true)" class="!px-2 !py-1" title="แก้ไข">
                                            <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                        </x-secondary-button>
                                        <x-secondary-button type="button" onclick="toggleModal('updateImageModal-{{ $item->id }}', true)" class="!px-2 !py-1 ml-1" title="รูปภาพ">
                                            <i data-lucide="image" class="w-5 h-5"></i>
                                        </x-secondary-button>
                                        <form action="{{ route('manager.items.destroy', $item->id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1"><i data-lucide="trash-2" class="w-5 h-5"></i></x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $items->links() }}</div>
                    @include('manager.modals.add-tailwind', ['types' => $types, 'units' => $units])
                    @foreach($items as $item)
                    @include('manager.modals.update-tailwind', ['item' => $item, 'types' => $types, 'units' => $units])
                    @endforeach

                    @elseif($table == 'accessories')
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่ออุปกรณ์ (ประเภท)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ราคา</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">คงเหลือ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($accessories as $acc)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $accessories->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{ $acc->name }}
                                        <span class="text-xs text-gray-500 font-normal">({{ $acc->type->name ?? '-' }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        {{ number_format($acc->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        {{ number_format($acc->stock) }} {{ $acc->unit->name ?? 'ชิ้น' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updateAccessoryModal-{{ $acc->id }}', true)" class="!px-2 !py-1">
                                            <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                        </x-secondary-button>

                                        <form action="{{ route('manager.accessories.destroy', $acc->id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1">
                                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                                            </x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $accessories->links() }}</div>

                    {{-- Include Modals --}}
                    @include('manager.modals.add-accessory', ['types' => $types, 'units' => $units])

                    @foreach($accessories as $acc)
                    @include('manager.modals.update-accessory', ['accessory' => $acc, 'types' => $types, 'units' => $units])
                    @endforeach

                    @elseif($table == 'users')
                    {{-- ... (ตาราง Users คงเดิม) ... --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อผู้ใช้ (Username)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อ - นามสกุล</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ข้อมูลติดต่อ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ประเภท</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">สถานะ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $users->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $user->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->first_name }} {{ $user->last_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div>{{ $user->email }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->tel ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->userType->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $user->status == 'active' ? 'กำลังใช้งาน' : 'ระงับการใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updateUserModal-{{ $user->user_id }}', true)" class="!px-2 !py-1" title="แก้ไข">
                                            <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                        </x-secondary-button>
                                        <form action="{{ route('manager.users.destroy', $user->user_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1"><i data-lucide="trash-2" class="w-5 h-5"></i></x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $users->links() }}</div>
                    @include('manager.modals.add-user', ['user_types' => $user_types])
                    @foreach($users as $user)
                    @include('manager.modals.update-user', ['user' => $user, 'user_types' => $user_types])
                    @endforeach

                    @elseif($table == 'user_types')
                    {{-- ... (ตาราง User Types คงเดิม) ... --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อประเภท</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">รายละเอียด</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($user_types as $type)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $type->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $type->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updateUserTypeModal-{{ $type->user_type_id }}', true)" class="!px-2 !py-1"><i data-lucide="file-pen-line" class="w-5 h-5"></i></x-secondary-button>
                                        <form action="{{ route('manager.user_types.destroy', $type->user_type_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1"><i data-lucide="trash-2" class="w-5 h-5"></i></x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('manager.modals.add-user-type')
                    @foreach($user_types as $type)
                    @include('manager.modals.update-user-type', ['type' => $type])
                    @endforeach

                    @elseif($table == 'item_types')
                    {{-- ... (ตาราง Item Types คงเดิม) ... --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อประเภทสินค้า</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">รายละเอียด</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($types as $type)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $type->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $type->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updateTypeModal-{{ $type->id }}', true)" class="!px-2 !py-1"><i data-lucide="file-pen-line" class="w-5 h-5"></i></x-secondary-button>
                                        <form action="{{ route('manager.types.destroy', $type->id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1"><i data-lucide="trash-2" class="w-5 h-5"></i></x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('manager.modals.add-type')
                    @foreach($types as $type)
                    @include('manager.modals.update-type', ['type' => $type])
                    @endforeach

                    {{-- ▼▼▼ 5. ตาราง Item Units (หน่วยสินค้า) ▼▼▼ --}}
                    @elseif($table == 'item_units')
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อหน่วยนับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">รายละเอียด</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($units as $unit)
                                <tr>
                                    {{-- [แก้ไขจุดที่ 3] ใช้การคำนวณลำดับแบบ Pagination --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $units->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $unit->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $unit->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updateUnitModal-{{ $unit->id }}', true)" class="!px-2 !py-1">
                                            <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                        </x-secondary-button>
                                        <form action="{{ route('manager.units.destroy', $unit->id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1">
                                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                                            </x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- [แก้ไขจุดที่ 4] เพิ่ม Pagination Links --}}
                    <div class="mt-4">{{ $units->links() }}</div>

                    @include('manager.modals.add-unit')
                    @foreach($units as $unit)
                    @include('manager.modals.update-unit', ['unit' => $unit])
                    @endforeach

                    @elseif($table == 'member_accounts')
                    {{-- ... (ตาราง Member Accounts คงเดิม) ... --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อผู้ใช้ (Username)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อ - นามสกุล</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ข้อมูลติดต่อ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">แต้มสะสม</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">สถานะ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($members as $member)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $members->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $member->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $member->first_name }} {{ $member->last_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div>{{ $member->email }}</div>
                                        <div class="text-xs text-gray-500">{{ $member->tel ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-blue-600">{{ number_format($member->points) }} แต้ม</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $member->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $member->status == 'active' ? 'กำลังใช้งาน' : 'ระงับการใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updateMemberModal-{{ $member->member_id }}', true)" class="!px-2 !py-1"><i data-lucide="file-pen-line" class="w-5 h-5"></i></x-secondary-button>
                                        <form action="{{ route('manager.members.destroy', $member->member_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1"><i data-lucide="trash-2" class="w-5 h-5"></i></x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $members->links() }}</div>
                    @include('manager.modals.add-member')
                    @foreach($members as $member)
                    @include('manager.modals.update-member', ['member' => $member])
                    @endforeach

                    @elseif($table == 'point_transactions')
                    {{-- ... (ตาราง Point Transactions คงเดิม) ... --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">วันที่ทำรายการ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">สมาชิก</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ประเภท</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จำนวนพอยต์</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">รายละเอียด</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">รหัสการเช่า</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($transactions as $tx)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tx->transaction_date ? $tx->transaction_date->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $tx->member->username ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tx->change_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $tx->point_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $tx->point_change > 0 ? '+' : '' }}{{ number_format($tx->point_change) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal text-sm">{{ $tx->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $tx->rental_id ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $transactions->links() }}</div>

                    @elseif($table == 'care_shops')
                    {{-- ... (ตาราง Care Shops คงเดิม) ... --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อร้าน</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ที่อยู่</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ข้อมูลติดต่อ</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">สถานะ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($care_shops as $shop)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $care_shops->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $shop->care_name }}</td>
                                    <td class="px-6 py-4 whitespace-normal text-sm">{{ $shop->address ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div>{{ $shop->email ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $shop->tel ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $shop->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $shop->status == 'active' ? 'กำลังใช้งาน' : 'ระงับการใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updateCareShopModal-{{ $shop->care_shop_id }}', true)" class="!px-2 !py-1"><i data-lucide="file-pen-line" class="w-5 h-5"></i></x-secondary-button>
                                        <form action="{{ route('manager.care_shops.destroy', $shop->care_shop_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1"><i data-lucide="trash-2" class="w-5 h-5"></i></x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $care_shops->links() }}</div>
                    @include('manager.modals.add-care-shop')
                    @foreach($care_shops as $shop)
                    @include('manager.modals.update-care-shop', ['shop' => $shop])
                    @endforeach

                    @elseif($table == 'makeup_artists')
                    {{-- ... (ตาราง Makeup Artists) ... --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อ - นามสกุล</th>
                                    {{-- [แก้ไข] เปลี่ยนหัวข้อเป็น ช่องทางการติดต่อ --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ช่องทางการติดต่อ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ราคาจ้าง</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">สถานะ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">รายละเอียด</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($makeup_artists as $artist)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $makeup_artists->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $artist->first_name }} {{ $artist->last_name }}</td>

                                    {{-- [แก้ไข] ส่วนแสดงผลข้อมูลติดต่อ --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div>{{ $artist->email ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $artist->tel ?? '-' }}</div>
                                        @if($artist->lineid)
                                        <div class="text-xs text-green-600 mt-0.5">
                                            <span class="font-bold">Line:</span> {{ $artist->lineid }}
                                        </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ number_format($artist->price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $artist->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $artist->status == 'active' ? 'กำลังใช้งาน' : 'ระงับการใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal text-sm">{{ $artist->description ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updateMakeupArtistModal-{{ $artist->makeup_id }}', true)" class="!px-2 !py-1"><i data-lucide="file-pen-line" class="w-5 h-5"></i></x-secondary-button>
                                        <form action="{{ route('manager.makeup_artists.destroy', $artist->makeup_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1"><i data-lucide="trash-2" class="w-5 h-5"></i></x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $makeup_artists->links() }}</div>
                    @include('manager.modals.add-makeup-artist')
                    @foreach($makeup_artists as $artist)
                    @include('manager.modals.update-makeup-artist', ['artist' => $artist])
                    @endforeach

                    @elseif($table == 'photographers')
                    {{-- ... (ตาราง Photographers) ... --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อ - นามสกุล</th>
                                    {{-- [แก้ไข] เปลี่ยนหัวข้อเป็น ช่องทางการติดต่อ --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ช่องทางการติดต่อ</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">สถานะ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($photographers as $photographer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $photographers->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $photographer->first_name }} {{ $photographer->last_name }}</td>

                                    {{-- [แก้ไข] ส่วนแสดงผลข้อมูลติดต่อ --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div>{{ $photographer->email ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $photographer->tel ?? '-' }}</div>
                                        @if($photographer->lineid)
                                        <div class="text-xs text-green-600 mt-0.5">
                                            <span class="font-bold">Line:</span> {{ $photographer->lineid }}
                                        </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $photographer->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $photographer->status == 'active' ? 'กำลังใช้งาน' : 'ระงับการใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updatePhotographerModal-{{ $photographer->photographer_id }}', true)" class="!px-2 !py-1"><i data-lucide="file-pen-line" class="w-5 h-5"></i></x-secondary-button>
                                        <form action="{{ route('manager.photographers.destroy', $photographer->photographer_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1"><i data-lucide="trash-2" class="w-5 h-5"></i></x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $photographers->links() }}</div>
                    @include('manager.modals.add-photographer')
                    @foreach($photographers as $photographer)
                    @include('manager.modals.update-photographer', ['photographer' => $photographer])
                    @endforeach

                    {{-- ▼▼▼ 11. ตาราง Photographer Packages (แพ็คเกจช่างภาพ) ▼▼▼ --}}
                    @elseif($table == 'photographer_packages')
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อแพ็คเกจ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ราคา</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">รายละเอียด</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($photographer_packages as $package)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $photographer_packages->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $package->package_name }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        {{ number_format($package->price, 2) }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-normal text-sm text-gray-600 dark:text-gray-400">
                                        {{ $package->description ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updatePhotographerPackageModal-{{ $package->package_id }}', true)" class="!px-2 !py-1">
                                            <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                        </x-secondary-button>
                                        <form action="{{ route('manager.photographer_packages.destroy', $package->package_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1">
                                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                                            </x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูลแพ็คเกจ</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $photographer_packages->links() }}</div>

                    @include('manager.modals.add-photographer-package')
                    @foreach($photographer_packages as $package)
                    @include('manager.modals.update-photographer-package', ['package' => $package])
                    @endforeach

                    @elseif($table == 'promotions')
                    {{-- ... (ตาราง Promotions คงเดิม) ... --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อโปรโมชั่น</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">มูลค่าส่วนลด</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ประเภทส่วนลด</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ระยะเวลา (เริ่ม - สิ้นสุด)</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">สถานะ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($promotions as $promo)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $promotions->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $promo->promotion_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ number_format($promo->discount_value) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($promo->discount_type == 'percentage')
                                        เปอร์เซ็นต์
                                        @elseif($promo->discount_type == 'fixed')
                                        บาท
                                        @else
                                        {{ $promo->discount_type }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $promo->start_date ? $promo->start_date->format('d/m/Y') : '-' }} ถึง {{ $promo->end_date ? $promo->end_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $promo->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $promo->status == 'active' ? 'กำลังใช้งาน' : 'ระงับการใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-secondary-button type="button" onclick="toggleModal('updatePromotionModal-{{ $promo->promotion_id }}', true)" class="!px-2 !py-1"><i data-lucide="file-pen-line" class="w-5 h-5"></i></x-secondary-button>
                                        <form action="{{ route('manager.promotions.destroy', $promo->promotion_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method('DELETE')
                                            <x-danger-button type="submit" class="!px-2 !py-1"><i data-lucide="trash-2" class="w-5 h-5"></i></x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูล</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $promotions->links() }}</div>
                    @include('manager.modals.add-promotion')
                    @foreach($promotions as $promo)
                    @include('manager.modals.update-promotion', ['promo' => $promo])
                    @endforeach

                    @else
                    <p class="text-center text-gray-500">กรุณาเลือกตารางที่ต้องการจัดการจากเมนูด้านข้าง</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>