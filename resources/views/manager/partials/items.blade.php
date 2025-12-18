<div x-data="itemManagement(
    @json($items->items()), 
    @json($types), 
    @json($units)
)">
    
    <div class="flex justify-end mb-4">
        <button @click="openAddModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มสินค้า
        </button>
    </div>

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
                    
                    {{-- [เพิ่มใหม่] Header คำอธิบาย --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                    
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                
                <template x-for="item in items" :key="item.id">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <img :src="getMainImageUrl(item)" alt="Item Image" class="w-10 h-10 rounded object-cover">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" x-text="item.item_name"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="item.price"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="item.stock"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="item.type ? item.type.name : 'N/A'"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="item.unit ? item.unit.name : 'N/A'"></td>
                        
                        {{-- [เพิ่มใหม่] Body คำอธิบาย --}}
                        {{-- ใช้ max-w-xs และ truncate เพื่อตัดคำถ้ายาวเกินไป (เอาเมาส์ชี้เพื่อดูเต็มๆ ได้) --}}
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate" 
                            :title="item.description" 
                            x-text="item.description || '-'">
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openEditModal(item)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200" title="แก้ไข">
                                <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                            </button>
                            
                            <form :action="getDeleteUrl(item.id)" method="POST" class="inline-block" @submit.prevent="deleteItem($event, item.id)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 ml-2" title="ลบ">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                </template>

                <template x-if="items.length === 0">
                    <tr>
                        {{-- [แก้ไข] เพิ่ม colspan เป็น 8 เพราะเพิ่มมา 1 คอลัมน์ --}}
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            ไม่พบข้อมูลสินค้า (กรุณากดปุ่ม "เพิ่มสินค้า" เพื่อสร้างข้อมูล)
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>

    {{-- ▼▼▼ Modal (ส่วน Modal คงเดิมตามที่คุณส่งมา) ▼▼▼ --}}
    <div x-show="isModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         style="display: none;">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="isModalOpen" x-transition.opacity
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 @click="isModalOpen = false" aria-hidden="true">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="isModalOpen" x-transition
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                
                <form :action="formActionUrl" method="POST" enctype="multipart/form-data">
                    @csrf
                    <template x-if="isEditMode">
                        @method('PATCH')
                    </template>

                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title" x-text="modalTitle"></h3>
                        <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                            
                            <div>
                                <label for="item_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อสินค้า (Name)</label>
                                <input type="text" name="name" :value="currentItem.item_name" id="item_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="item_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">คำอธิบาย (Description)</label>
                                <textarea name="description" id="item_description" x-text="currentItem.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ราคา (Price)</label>
                                    <input type="number" name="price" :value="currentItem.price" id="price" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">จำนวน (Stock)</label>
                                    <input type="number" name="stock" :value="currentItem.stock" id="stock" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="item_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ประเภท (Type)</label>
                                    <select name="item_type_id" id="item_type_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">-- เลือกประเภท --</option>
                                        <template x-for="type in types" :key="type.id">
                                            {{-- [แก้ไข] เช็ค type.id กับ currentItem.item_type_id หรือ currentItem.type.id ให้ตรงกับโครงสร้างข้อมูล --}}
                                            <option :value="type.id" x-text="type.name" :selected="type.id == currentItem.item_type_id"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label for="item_unit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">หน่วย (Unit)</label>
                                    <select name="item_unit_id" id="item_unit_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">-- เลือกหน่วย --</option>
                                        <template x-for="unit in units" :key="unit.id">
                                             {{-- [แก้ไข] เช็ค unit.id กับ currentItem.item_unit_id --}}
                                            <option :value="unit.id" x-text="unit.name" :selected="unit.id == currentItem.item_unit_id"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div x-show="!isEditMode">
                                <label for="images" class="block text-sm font-medium text-gray-700 dark:text-gray-300">อัปโหลดรูปภาพ (เพิ่มได้หลายรูป)</label>
                                <input type="file" name="images[]" id="images" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-200 hover:file:bg-blue-100">
                            </div>

                            <div x-show="isEditMode" class="space-y-4">
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">จัดการรูปภาพ</h4>
                                </div>
                                
                                <div>
                                    <label for="new_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เพิ่มรูปภาพใหม่</label>
                                    <form :action="getImageUploadUrl(currentItem.id)" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 mt-1">
                                        @csrf
                                        <input type="file" name="image" id="new_image" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-50 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-100">
                                        <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-500" title="อัปโหลด">
                                            <i data-lucide="upload" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-4">
                                    <template x-for="image in currentItem.images" :key="image.id">
                                        <div class="relative group">
                                            <img :src="getImageUrl(image.path)" alt="Item Image" class="w-full h-24 rounded object-cover border-2"
                                                 :class="image.is_main ? 'border-green-500' : 'border-gray-300 dark:border-gray-600'">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-150 rounded flex items-center justify-center gap-2">
                                                
                                                <form :action="getImageDeleteUrl(image.id)" method="POST" @submit.prevent="deleteImage($event, image.id)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-8 h-8 rounded-full bg-red-600 text-white opacity-0 group-hover:opacity-100 flex items-center justify-center hover:bg-red-700" title="ลบรูปนี้">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>

                                                <form :action="getImageSetMainUrl(image.id)" method="POST" @submit.prevent="setMainImage($event, image.id)">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" x-show="!image.is_main" class="w-8 h-8 rounded-full bg-blue-600 text-white opacity-0 group-hover:opacity-100 flex items-center justify-center hover:bg-blue-700" title="ตั้งเป็นรูปหลัก">
                                                        <i data-lucide="check" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                                
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            บันทึกข้อมูลหลัก
                        </button>
                        <button @click="isModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            ยกเลิก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- === จบส่วน Modal === --}}

</div>

{{-- Script สำหรับ Alpine.js (เฉพาะส่วน Items) --}}
<script>
    function itemManagement(itemsData, typesData, unitsData) {
        return {
            items: itemsData,
            types: typesData,
            units: unitsData,
            isModalOpen: false,
            modalTitle: '',
            isEditMode: false,
            formActionUrl: '{{ route("manager.storeItem") }}', 
            
            currentItem: {
                id: null,
                item_name: '',
                description: '',
                price: 0,
                stock: 0,
                item_type_id: '', // แก้ไขชื่อตัวแปรให้ตรงกับในฐานข้อมูล (ถ้าใน DB เป็น item_type_id)
                item_unit_id: '', // แก้ไขชื่อตัวแปรให้ตรงกับในฐานข้อมูล
                images: []
            },

            // URL Helpers
            getImageUrl(path) {
                return `{{ asset('storage') }}/${path}`;
            },
            getMainImageUrl(item) {
                if (!item.images || item.images.length === 0) {
                    return 'https://placehold.co/100x100/e2e8f0/94a3b8?text=No+Image';
                }
                let mainImage = item.images.find(img => img.is_main);
                if (mainImage) {
                    return this.getImageUrl(mainImage.path);
                }
                return this.getImageUrl(item.images[0].path); // Fallback to first image
            },
            getDeleteUrl(itemId) {
                return `{{ url("manager/items") }}/${itemId}`;
            },
            getImageUploadUrl(itemId) {
                return `{{ url("manager/items") }}/${itemId}/image`;
            },
            getImageDeleteUrl(imageId) {
                return `{{ url("manager/item-images") }}/${imageId}`;
            },
            getImageSetMainUrl(imageId) {
                return `{{ url("manager/item-images") }}/${imageId}/set-main`;
            },

            // Modal Functions
            openAddModal() {
                this.modalTitle = 'เพิ่มสินค้าใหม่';
                this.isEditMode = false;
                this.resetCurrentItem();
                this.formActionUrl = '{{ route("manager.storeItem") }}';
                this.isModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            openEditModal(item) {
                this.modalTitle = 'แก้ไขสินค้า: ' + item.item_name;
                this.isEditMode = true;
                this.currentItem = JSON.parse(JSON.stringify(item));
                
                // [TIPS] ตรวจสอบว่า currentItem.item_type_id หรือ currentItem.type.id มีค่าหรือไม่ เพื่อให้ Select เลือกถูก
                // บางครั้งข้อมูลที่ส่งมาอาจจะเป็น object (item.type) ต้อง map ให้เป็น ID
                if(item.type) this.currentItem.item_type_id = item.type.id;
                if(item.unit) this.currentItem.item_unit_id = item.unit.id;

                this.formActionUrl = `{{ url("manager/items") }}/${item.id}`;
                this.isModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            // Form Submit Functions
            deleteItem(event, itemId) {
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?')) {
                    event.target.closest('form').submit();
                }
            },
            deleteImage(event, imageId) {
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรูปภาพนี้?')) {
                    event.target.closest('form').submit();
                }
            },
            setMainImage(event, imageId) {
                if (confirm('คุณต้องการตั้งค่ารูปนี้เป็นรูปหลักหรือไม่?')) {
                    event.target.closest('form').submit();
                }
            },

            resetCurrentItem() {
                this.currentItem = {
                    id: null, item_name: '', description: '', price: 0,
                    stock: 0, item_type_id: '', item_unit_id: '', images: []
                };
            }
        };
    }

    // เรียก Lucide อีกครั้งเมื่อ DOM พร้อม (เผื่อ)
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>