{{-- resources/views/manager/partials/user-types.blade.php --}}

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">ลำดับ</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ชื่อประเภท</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">รายละเอียด</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">จัดการ</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($user_types as $type)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $user_types->firstItem() + $loop->index }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    {{ $type->name }}
                </td>
                <td class="px-6 py-4 whitespace-normal text-sm text-gray-500 dark:text-gray-400">
                    {{ $type->description ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    {{-- ปุ่มแก้ไข --}}
                    <x-secondary-button type="button" onclick="toggleModal('updateUserTypeModal-{{ $type->user_type_id }}', true)" class="!px-2 !py-1" title="แก้ไข">
                        <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                    </x-secondary-button>

                    {{-- ปุ่มลบ --}}
                    <form action="{{ route('manager.user_types.destroy', $type->user_type_id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบประเภทผู้ใช้นี้?')">
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
                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    ไม่พบข้อมูลประเภทผู้ใช้
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination Links --}}
<div class="mt-4">
    {{ $user_types->links() }}
</div>

{{-- Include Modals (เพื่อให้ปุ่มเพิ่ม/แก้ไขทำงานได้) --}}
@include('manager.modals.add-user-type')

@foreach($user_types as $type)
    @include('manager.modals.update-user-type', ['type' => $type])
@endforeach