{{-- 
    นี่คือ Partial View สำหรับจัดการ "User Types"
--}}
<div x-data="userTypesManagement(@json($user_types))">
    
    <div class="flex justify-end mb-4">
        <button @click="openAddModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มประเภทผู้ใช้
        </button>
    </div>

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
                <template x-for="type in userTypes" :key="type.user_type_id">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" x-text="type.name"></td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="type.description"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openEditModal(type)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200" title="แก้ไข">
                                <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                            </button>
                            
                            <form :action="getDeleteUrl(type.user_type_id)" method="POST" class="inline-block" @submit.prevent="deleteType($event, type.user_type_id)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 ml-2" title="ลบ">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    {{-- (ไม่มี Pagination สำหรับ User Types เพราะเรา Get() มาทั้งหมด) --}}

    {{-- ▼▼▼ Modal (ป๊อปอัพ) สำหรับเพิ่ม/แก้ไข User Type ▼▼▼ --}}
    <div x-show="isModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="isModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="isModalOpen" x-transition class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <form :action="formActionUrl" method="POST">
                    @csrf
                    <template x-if="isEditMode">
                        @method('PATCH')
                    </template>

                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" x-text="modalTitle"></h3>
                        <div class="mt-4 space-y-4">
                            
                            <div>
                                <label for="type_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" name="name" :value="currentType.name" id="type_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            
                            <div>
                                <label for="type_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</LabeL>
                                <textarea name="description" :value="currentType.description" id="type_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            บันทึก
                        </button>
                        <button @click="isModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            ยกเลิก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function userTypesManagement(userTypesData) {
        return {
            userTypes: userTypesData,
            isModalOpen: false,
            modalTitle: '',
            isEditMode: false,
            formActionUrl: '{{ route("manager.storeUserType") }}',
            
            currentType: {
                user_type_id: null,
                name: '',
                description: ''
            },

            openAddModal() {
                this.modalTitle = 'เพิ่มประเภทผู้ใช้ใหม่';
                this.isEditMode = false;
                this.resetCurrentType();
                this.formActionUrl = '{{ route("manager.storeUserType") }}';
                this.isModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            openEditModal(type) {
                this.modalTitle = 'แก้ไขประเภทผู้ใช้: ' + type.name;
                this.isEditMode = true;
                this.currentType = JSON.parse(JSON.stringify(type));
                this.formActionUrl = '{{ url("manager/user-types") }}/' + type.user_type_id;
                this.isModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            deleteType(event, typeId) {
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบประเภทนี้?')) {
                    event.target.closest('form').submit();
                }
            },
            
            getDeleteUrl(typeId) {
                return '{{ url("manager/user-types") }}/' + typeId;
            },

            resetCurrentType() {
                this.currentType = { user_type_id: null, name: '', description: '' };
            }
        };
    }
    // ไม่ต้องเรียก lucide.createIcons() ที่นี่ เพราะถูกเรียกใน partials/users.blade.php (หรือไฟล์แรกที่โหลด) แล้ว
</script>