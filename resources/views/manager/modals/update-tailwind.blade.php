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
                            <x-input-label for="item_name-{{ $item->id }}" :value="__('ชื่อสินค้า (Name)')" class="after:content-['*'] after:text-red-500 after:ml-0.5" />
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
                                <x-input-label for="price-{{ $item->id }}" :value="__('ราคา (Price)')" class="after:content-['*'] after:text-red-500 after:ml-0.5" />
                                <x-text-input type="number" name="price" id="price-{{ $item->id }}" value="{{ $item->price }}" step="0.01" required class="mt-1 block w-full" />
                            </div>
                            <!-- Field: Stock -->
                            <div>
                                <x-input-label for="stock-{{ $item->id }}" :value="__('จำนวน (Stock)')" class="after:content-['*'] after:text-red-500 after:ml-0.5" />
                                <x-text-input type="number" name="stock" id="stock-{{ $item->id }}" value="{{ $item->stock }}" required class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="update_item_type_id-{{ $item->id }}" :value="__('ประเภท (Type)')" class="after:content-['*'] after:text-red-500 after:ml-0.5" />
                                {{-- แก้ name เป็น item_type_id --}}
                                <select name="item_type_id" id="update_item_type_id-{{ $item->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- เลือกประเภท --</option>
                                    @foreach($types as $type)
                                    <option value="{{ $type->id }}" @selected($item->item_type_id == $type->id)>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="update_item_unit_id-{{ $item->id }}" :value="__('หน่วย (Unit)')" class="after:content-['*'] after:text-red-500 after:ml-0.5" />
                                {{-- แก้ name เป็น item_unit_id --}}
                                <select name="item_unit_id" id="update_item_unit_id-{{ $item->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- เลือกหน่วย --</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" @selected($item->item_unit_id == $unit->id)>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="status-{{ $item->id }}" :value="__('สถานะ (Status)')" class="after:content-['*'] after:text-red-500 after:ml-0.5" />
                                <select name="status" id="status-{{ $item->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="active" @selected($item->status == 'active')>พร้อมใช้งาน (Active)</option>
                                    <option value="inactive" @selected($item->status == 'inactive')>ระงับการใช้งาน (Inactive)</option>
                                    <option value="maintenance" @selected($item->status == 'maintenance')>ซ่อมบำรุง (Maintenance)</option>
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

<div id="updateImageModal-{{ $item->id }}"
    class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden"
    aria-labelledby="modal-title-image-{{ $item->id }}" role="dialog" aria-modal="true"
    onclick="toggleModal('updateImageModal-{{ $item->id }}', false, event)">

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full"
            role="document">

            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title-image-{{ $item->id }}">
                        จัดการรูปภาพ: <span class="text-indigo-500">{{ $item->item_name }}</span>
                    </h3>
                    <button onclick="toggleModal('updateImageModal-{{ $item->id }}', false)" class="text-gray-400 hover:text-gray-500">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="space-y-6 max-h-[70vh] overflow-y-auto pr-2">

                    <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border border-indigo-100 dark:border-indigo-800">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="image-plus" class="w-4 h-4 inline mr-1"></i> เพิ่มรูปภาพใหม่
                        </label>
                        <form action="{{ route('manager.items.uploadImage', $item->id) }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
                            @csrf
                            <input type="file" name="images[]" multiple required
                                class="block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-white file:text-indigo-700
                                          hover:file:bg-gray-50 border border-gray-300 rounded-md cursor-pointer bg-white">

                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                อัปโหลด
                            </button>
                        </form>
                    </div>

                    <form id="bulkDeleteForm-{{ $item->id }}" action="{{ route('manager.items.bulkDestroyImages') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex justify-between items-center mb-2">
                            <label class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 mr-2"
                                    onclick="toggleSelectAll(this, '{{ $item->id }}')">
                                เลือกทั้งหมด
                            </label>

                            <button type="button" onclick="confirmBulkDelete('{{ $item->id }}')"
                                class="inline-flex items-center px-3 py-1 bg-white border border-red-300 rounded-md font-semibold text-xs text-red-700 uppercase tracking-widest shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i> ลบที่เลือก
                            </button>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @forelse($item->images as $image)
                            <div class="relative group bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-2 hover:shadow-md transition-all">

                                <div class="absolute top-2 left-2 z-20">
                                    <input type="checkbox" name="ids[]" value="{{ $image->id }}"
                                        class="image-checkbox-{{ $item->id }} w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer">
                                </div>

                                <div class="aspect-w-16 aspect-h-12 mb-2 overflow-hidden rounded bg-gray-200 relative">
                                    <img src="{{ $image->path }}"
                                        class="w-full h-32 object-cover {{ $image->is_main ? 'ring-4 ring-green-500/50' : '' }}">

                                    @if($image->is_main)
                                    <div class="absolute top-0 right-0 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded-bl shadow">
                                        MAIN
                                    </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between gap-2 mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">

                                    <button type="button"
                                        {{-- 1. เอา URL ไปใส่ใน data-url แทน --}}
                                        data-url="{{ route('manager.items.destroyImage', $image->id) }}"

                                        {{-- 2. ใน onclick ให้ดึงค่าจาก data-url มาใช้ --}}
                                        onclick="confirmDeleteSingle(this.getAttribute('data-url'))"

                                        class="flex-1 text-gray-500 hover:text-red-600 hover:bg-red-50 p-1.5 rounded transition text-center"
                                        title="ลบรูปนี้">
                                        <i data-lucide="trash-2" class="w-4 h-4 mx-auto"></i>
                                    </button>

                                    @if(!$image->is_main)
                                    <button type="button"
                                        onclick="document.getElementById('btn-submit-main-{{ $image->id }}').click()"
                                        class="flex-1 text-gray-500 hover:text-green-600 hover:bg-green-50 p-1.5 rounded transition text-center"
                                        title="ตั้งเป็นรูปหลัก">
                                        <i data-lucide="check-circle" class="w-4 h-4 mx-auto"></i>
                                    </button>
                                    @else
                                    <div class="flex-1 text-green-500 p-1.5 text-center cursor-default">
                                        <i data-lucide="check-circle-2" class="w-4 h-4 mx-auto fill-green-100"></i>
                                    </div>
                                    @endif

                                </div>
                            </div>
                            @empty
                            <div class="col-span-full py-10 text-center border-2 border-dashed border-gray-300 rounded-lg">
                                <i data-lucide="image-off" class="w-10 h-10 mx-auto text-gray-400 mb-2"></i>
                                <p class="text-gray-500">ยังไม่มีรูปภาพ</p>
                            </div>
                            @endforelse
                        </div>
                    </form>

                    <div class="hidden">
                        @foreach($item->images as $image)
                        @if(!$image->is_main)
                        <form action="{{ route('manager.items.setMainImage', $image->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" id="btn-submit-main-{{ $image->id }}"></button>
                        </form>
                        @endif
                        @endforeach
                    </div>

                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t dark:border-gray-600">
                <button type="button" onclick="toggleModal('updateImageModal-{{ $item->id }}', false)"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>
    </div>
</div>