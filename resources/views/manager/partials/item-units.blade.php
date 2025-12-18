{{-- 
    นี่คือ Partial View สำหรับจัดการ "Item Units"
--}}
<div x-data="simpleCrudManagement(
    @json($units), 
    '{{ route("manager.storeUnit") }}', 
    '{{ url("manager/units") }}'
)">
    
    <!-- ปุ่มเพิ่ม -->
    <div class="flex justify-end mb-4">
        <button @click="openAddModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มหน่วยสินค้า
        </button>
    </div>

    <!-- ตารางแสดงผล -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="item in items" :key="item.id">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" x-text="item.name"></td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="item.description"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openEditModal(item)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200" title="แก้ไข">
                                <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                            </button>
                            <form :action="getDeleteUrl(item.id)" method="POST" class="inline-block" @submit.prevent="deleteItem($event)">
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
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            ไม่พบข้อมูล
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Modal สำหรับเพิ่ม/แก้ไข --}}
    <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="isModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="isModalOpen" x-transition class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="formActionUrl" method="POST">
                    @csrf
                    <template x-if="isEditMode">
                        @method('PATCH')
                    </template>
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" x-text="modalTitle"></h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="item_unit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">หน่วยสินค้า</label>
                                <input type="text" name="name" :value="currentItem.name" id="item_unit_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            </div>
                            <div>
                                <label for="item_unit_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">คำอธิบาย</label>
                                <textarea name="description" x-text="currentItem.description" id="item_unit_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                            บันทึก
                        </button>
                        <button @click="isModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            ยกเลิก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Script นี้จะถูกใช้โดย item-types ด้วย
    // (ตรวจสอบว่ามีฟังก์ชันนี้หรือยัง ก่อนที่จะประกาศ)
    if (typeof simpleCrudManagement !== 'function') {
        function simpleCrudManagement(itemsData, storeUrl, baseUrl) {
            return {
                items: itemsData,
                isModalOpen: false,
                modalTitle: '',
                isEditMode: false,
                formActionUrl: storeUrl,
                currentItem: { id: null, name: '', description: '' },

                openAddModal() {
                    this.modalTitle = 'เพิ่มรายการใหม่';
                    this.isEditMode = false;
                    this.resetCurrentItem();
                    this.formActionUrl = storeUrl;
                    this.isModalOpen = true;
                    this.$nextTick(() => lucide.createIcons());
                },
                openEditModal(item) {
                    this.modalTitle = 'แก้ไขรายการ: ' + item.name;
                    this.isEditMode = true;
                    this.currentItem = JSON.parse(JSON.stringify(item));
                    this.formActionUrl = `${baseUrl}/${item.id}`;
                    this.isModalOpen = true;
                    this.$nextTick(() => lucide.createIcons());
                },
                deleteItem(event) {
                    if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?')) {
                        event.target.closest('form').submit();
                    }
                },
                getDeleteUrl(itemId) {
                    return `${baseUrl}/${itemId}`;
                },
                resetCurrentItem() {
                    this.currentItem = { id: null, name: '', description: '' };
                }
            };
        }
    }
</script>

