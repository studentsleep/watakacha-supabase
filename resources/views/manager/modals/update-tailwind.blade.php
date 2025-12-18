{{--
    นี่คือ Modal (หน้าต่าง Update) "แบบใหม่"
    - เราแยก Modal แก้ไขข้อมูลหลัก และ Modal จัดการรูปภาพ
    - ถูกควบคุมโดย "JS ธรรมดา" (toggleModal)
    - ส่งข้อมูลไปยัง Route "แบบใหม่"
--}}
@php
// (ป้องกัน Error หากไม่มี $item)
if (!isset($item)) $item = new \App\Models\Item();
@endphp

<!-- ===== 1. Modal แก้ไขข้อมูลหลัก ===== -->
<div id="updateItemModal-{{ $item->id }}"
    class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden"
    aria-labelledby="modal-title-update-{{ $item->id }}" role="dialog" aria-modal="true"
    onclick="toggleModal('updateItemModal-{{ $item->id }}', false, event)">

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
            role="document">

            {{-- [CRUD ใหม่] ส่ง Form ไปยัง Route ใหม่ --}}
            <form action="{{ route('manager.items.update', $item->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title-update-{{ $item->id }}">
                        แก้ไขข้อมูลหลัก: {{ $item->item_name }}
                    </h3>
                    <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">

                        <!-- Field: Name -->
                        <div>
                            <x-input-label for="item_name-{{ $item->id }}" :value="__('ชื่อสินค้า (Name)')" />
                            <x-text-input type="text" name="name" id="item_name-{{ $item->id }}" value="{{ $item->item_name }}" required class="mt-1 block w-full" />
                        </div>

                        <!-- Field: Description -->
                        <div>
                            <x-input-label for="item_description-{{ $item->id }}" :value="__('คำอธิบาย (Description)')" />
                            <textarea name="description" id="item_description-{{ $item->id }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $item->description }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Field: Price -->
                            <div>
                                <x-input-label for="price-{{ $item->id }}" :value="__('ราคา (Price)')" />
                                <x-text-input type="number" name="price" id="price-{{ $item->id }}" value="{{ $item->price }}" step="0.01" required class="mt-1 block w-full" />
                            </div>
                            <!-- Field: Stock -->
                            <div>
                                <x-input-label for="stock-{{ $item->id }}" :value="__('จำนวน (Stock)')" />
                                <x-text-input type="number" name="stock" id="stock-{{ $item->id }}" value="{{ $item->stock }}" required class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="update_item_type_id-{{ $item->id }}" :value="__('ประเภท (Type)')" />
                                {{-- แก้ name เป็น item_type_id --}}
                                <select name="item_type_id" id="update_item_type_id-{{ $item->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- เลือกประเภท --</option>
                                    @foreach($types as $type)
                                    <option value="{{ $type->id }}" @selected($item->item_type_id == $type->id)>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="update_item_unit_id-{{ $item->id }}" :value="__('หน่วย (Unit)')" />
                                {{-- แก้ name เป็น item_unit_id --}}
                                <select name="item_unit_id" id="update_item_unit_id-{{ $item->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- เลือกหน่วย --</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" @selected($item->item_unit_id == $unit->id)>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="sm:ml-3">
                        บันทึกข้อมูลหลัก
                    </x-primary-button>
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('updateItemModal-{{ $item->id }}', false)">
                        ยกเลิก
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== 2. Modal จัดการรูปภาพ ===== -->
<div id="updateImageModal-{{ $item->id }}"
    class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden"
    aria-labelledby="modal-title-image-{{ $item->id }}" role="dialog" aria-modal="true"
    onclick="toggleModal('updateImageModal-{{ $item->id }}', false, event)">

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
            role="document">

            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title-image-{{ $item->id }}">
                    จัดการรูปภาพ: {{ $item->item_name }}
                </h3>
                <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">

                    <!-- Form อัปโหลดรูปใหม่ -->
                    <div>
                        {{-- เปลี่ยน Label --}}
                        <x-input-label for="new_images-{{ $item->id }}" :value="__('เพิ่มรูปภาพใหม่ (เลือกได้หลายรูป)')" />

                        <form action="{{ route('manager.items.uploadImage', $item->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 mt-1">
                            @csrf
                            {{--
                                [แก้ไข] 
                                1. name="images[]" (เติม [] เพื่อส่งเป็น array)
                                2. เพิ่ม attribute 'multiple'
                                3. เปลี่ยน id เป็น new_images-...
                            --}}
                            <input type="file" name="images[]" id="new_images-{{ $item->id }}" multiple required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-600">

                            <x-primary-button type="submit" class="!px-3 !py-2" title="อัปโหลด">
                                <i data-lucide="upload" class="w-4 h-4"></i>
                            </x-primary-button>
                        </form>
                    </div>

                    <!-- รูปภาพที่มีอยู่ -->
                    <div class="grid grid-cols-3 gap-4">
                        @forelse($item->images as $image)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $image->path) }}" alt="Item Image" class="w-full h-24 rounded object-cover border-2
                                    {{ $image->is_main ? 'border-green-500' : 'border-gray-300 dark:border-gray-600' }}">

                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-150 rounded flex items-center justify-center gap-2">

                                <!-- ปุ่มลบรูป -->
                                <form action="{{ route('manager.items.destroyImage', $image->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรูปภาพนี้?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit" class="!w-8 !h-8 !p-0 !flex !items-center !justify-center" title="ลบรูปนี้">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </x-danger-button>
                                </form>

                                <!-- ปุ่มตั้งเป็นรูปหลัก -->
                                @if(!$image->is_main)
                                <form action="{{ route('manager.items.setMainImage', $image->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <x-secondary-button type="submit" class="!w-8 !h-8 !p-0 !flex !items-center !justify-center" title="ตั้งเป็นรูปหลัก">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </x-secondary-button>
                                </form>
                                @endif

                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 col-span-3">ยังไม่มีรูปภาพสำหรับสินค้านี้</p>
                        @endforelse
                    </div>

                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <x-secondary-button type="button" onclick="toggleModal('updateImageModal-{{ $item->id }}', false)">
                    ปิดหน้าต่าง
                </x-secondary-button>
            </div>
        </div>
    </div>
</div>