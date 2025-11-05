{{-- 
    นี่คือ Modal (หน้าต่าง Insert) "แบบใหม่"
    - สร้างด้วย Tailwind CSS (ธีมเดิม)
    - ถูกควบคุมโดย "JS ธรรมดา" (toggleModal)
    - ส่งข้อมูลไปยัง Route "แบบใหม่" (manager.items.store)
--}}
<div id="addItemModal" 
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true"
     onclick="toggleModal('addItemModal', false, event)">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
             role="document">
            
            {{-- [CRUD ใหม่] ส่ง Form ไปยัง Route ใหม่ --}}
            <form action="{{ route('manager.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                        เพิ่มสินค้าใหม่
                    </h3>
                    <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        
                        <!-- Field: Name -->
                        <div>
                            <x-input-label for="item_name" :value="__('ชื่อสินค้า (Name)')" />
                            <x-text-input type="text" name="name" id="item_name" required class="mt-1 block w-full" />
                        </div>

                        <!-- Field: Description -->
                        <div>
                            <x-input-label for="item_description" :value="__('คำอธิบาย (Description)')" />
                            <textarea name="description" id="item_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Field: Price -->
                            <div>
                                <x-input-label for="price" :value="__('ราคา (Price)')" />
                                <x-text-input type="number" name="price" id="price" step="0.01" required class="mt-1 block w-full" />
                            </div>
                            <!-- Field: Stock -->
                            <div>
                                <x-input-label for="stock" :value="__('จำนวน (Stock)')" />
                                <x-text-input type="number" name="stock" id="stock" required class="mt-1 block w-full" />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Field: Item Type -->
                            <div>
                                <x-input-label for="item_type_id" :value="__('ประเภท (Type)')" />
                                <select name="item_type_id" id="item_type_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- เลือกประเภท --</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Field: Item Unit -->
                            <div>
                                <x-input-label for="item_unit_id" :value="__('หน่วย (Unit)')" />
                                <select name="item_unit_id" id="item_unit_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- เลือกหน่วย --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Field: Image Upload -->
                        <div>
                            <x-input-label for="images" :value="__('อัปโหลดรูปภาพ (เพิ่มได้หลายรูป)')" />
                            <input type="file" name="images[]" id="images" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-600">
                        </div>

                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="sm:ml-3">
                        บันทึกข้อมูล
                    </x-primary-button>
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('addItemModal', false)">
                        ยกเลิก
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>