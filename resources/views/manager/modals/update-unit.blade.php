{{-- Modal แก้ไข ItemUnit --}}
<div id="updateUnitModal-{{ $unit->item_unit_id }}" 
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" 
     onclick="toggleModal('updateUnitModal-{{ $unit->item_unit_id }}', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
             role="document">
            <form action="{{ route('manager.units.update', $unit->item_unit_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        แก้ไขหน่วยสินค้า: {{ $unit->name }}
                    </h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <x-input-label for="name-{{ $unit->item_unit_id }}" :value="__('Name')" />
                            <x-text-input type="text" name="name" id="name-{{ $unit->item_unit_id }}" value="{{ $unit->name }}" required class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="description-{{ $unit->item_unit_id }}" :value="__('Description')" />
                            <textarea name="description" id="description-{{ $unit->item_unit_id }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $unit->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="sm:ml-3">
                        บันทึก
                    </x-primary-button>
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('updateUnitModal-{{ $unit->item_unit_id }}', false)">
                        ยกเลิก
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>