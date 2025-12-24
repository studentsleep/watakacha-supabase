<div id="addAccessoryModal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" onclick="toggleModal('addAccessoryModal', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document" onclick="event.stopPropagation()">
            
            <form action="{{ route('manager.accessories.store') }}" method="POST">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">เพิ่มอุปกรณ์เสริม</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="name" :value="__('ชื่ออุปกรณ์')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('รายละเอียด')" />
                            <textarea name="description" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="price" :value="__('ราคาเช่า')" />
                                <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" required />
                            </div>
                            <div>
                                <x-input-label for="stock" :value="__('จำนวน (Stock)')" />
                                <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="item_type_id" :value="__('ประเภทสินค้า')" />
                                <select name="item_type_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- เลือกประเภท --</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="item_unit_id" :value="__('หน่วยนับ')" />
                                <select name="item_unit_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- เลือกหน่วย --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="w-full sm:w-auto sm:ml-3">บันทึก</x-primary-button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700" onclick="toggleModal('addAccessoryModal', false)">
                        ยกเลิก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>