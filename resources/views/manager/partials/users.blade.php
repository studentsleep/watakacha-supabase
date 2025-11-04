{{-- 
    นี่คือ Partial View สำหรับจัดการ "Users" (บัญชีผู้ใช้ Admin)
    - [แก้ไข] แยกคอลัมน์ Email และ Tel
    - [แก้ไข] เพิ่ม "แถว" สำหรับแจ้งเตือนเมื่อไม่มีข้อมูล (Empty State)
--}}

<div x-data="userManagement(
    @json($users->items()), 
    @json($user_types)
)">
    
    <!-- ปุ่มเพิ่มผู้ใช้ -->
    <div class="flex justify-end mb-4">
        <button @click="openAddModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มผู้ใช้
        </button>
    </div>

    <!-- ตารางแสดงผลผู้ใช้ -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Username</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ชื่อ - นามสกุล</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th> {{-- <--- แยกแล้ว --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tel</th> {{-- <--- แยกแล้ว --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ประเภท</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">สถานะ</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                
                {{-- วนลูปข้อมูลผู้ใช้ (จาก $users ที่ถูกแปลงเป็น Alpine data) --}}
                <template x-for="user in users" :key="user.user_id">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" x-text="user.username"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div x-text="(user.first_name || '') + ' ' + (user.last_name || '')"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div x-text="user.email"></div>
                        </td> {{-- <--- แยกแล้ว --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div x-text="user.tel" class="text-xs"></div>
                        </td> {{-- <--- แยกแล้ว --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="user.user_type ? user.user_type.name : 'N/A'"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                  :class="user.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100'"
                                  x-text="user.status">
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openEditModal(user)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200" title="แก้ไข">
                                <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                            </button>
                            
                            {{-- ปุ่มลบ (Form) --}}
                            <form :action="getDeleteUrl(user.user_id)" method="POST" class="inline-block" @submit.prevent="deleteUser($event, user.user_id)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 ml-2" title="ลบ">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                </template>

                {{-- ▼▼▼ [ใหม่] แถวสำหรับแจ้งเตือนเมื่อไม่มีข้อมูล ▼▼▼ --}}
                <template x-if="users.length === 0">
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            ไม่พบข้อมูลผู้ใช้ (กรุณากดปุ่ม "เพิ่มผู้ใช้" เพื่อสร้างข้อมูล)
                        </td>
                    </tr>
                </template>
                {{-- === จบส่วนแถวแจ้งเตือน === --}}
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>

    {{-- ▼▼▼ Modal (ป๊อปอัพ) สำหรับเพิ่ม/แก้ไข User ▼▼▼ --}}
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
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <form :action="formActionUrl" method="POST">
                    @csrf
                    <template x-if="isEditMode">
                        @method('PATCH')
                    </template>

                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title" x-text="modalTitle"></h3>
                        <div class="mt-4 space-y-4">
                            
                            <!-- Field: Username -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                                <input type="text" name="username" :value="currentUser.username" id="username" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Field: First Name -->
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                                    <input type="text" name="first_name" :value="currentUser.first_name" id="first_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <!-- Field: Last Name -->
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                                    <input type="text" name="last_name" :value="currentUser.last_name" id="last_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <!-- Field: Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" name="email" :value="currentUser.email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Field: Tel -->
                            <div>
                                <label for="tel" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tel</label>
                                <input type="text" name="tel" :value="currentUser.tel" id="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Field: User Type -->
                                <div>
                                    <label for="user_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Type</label>
                                    <select name="user_type_id" id="user_type_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">-- เลือกประเภท --</option>
                                        <template x-for="type in userTypes" :key="type.user_type_id">
                                            <option :value="type.user_type_id" x-text="type.name" :selected="type.user_type_id == currentUser.user_type_id"></option>
                                        </template>
                                    </select>
                                </div>
                                <!-- Field: Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="active" :selected="currentUser.status == 'active'">Active</option>
                                        <option value="inactive" :selected="currentUser.status == 'inactive'">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Field: Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                <input type="password" name="password" id="password" :placeholder="isEditMode ? '(เว้นว่างไว้หากไม่ต้องการเปลี่ยน)' : 'กรอกรหัสผ่าน'" :required="!isEditMode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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
    {{-- === จบส่วน Modal === --}}

</div>

{{-- Script สำหรับ Alpine.js (เฉพาะส่วน Users) --}}
<script>
    function userManagement(usersData, userTypesData) {
        return {
            users: usersData,
            userTypes: userTypesData,
            isModalOpen: false,
            modalTitle: '',
            isEditMode: false,
            formActionUrl: '{{ route("manager.storeUser") }}', // URL เริ่มต้นสำหรับ "เพิ่ม"
            
            currentUser: {
                user_id: null,
                username: '',
                first_name: '',
                last_name: '',
                email: '',
                tel: '',
                user_type_id: '',
                status: 'active'
            },

            openAddModal() {
                this.modalTitle = 'เพิ่มผู้ใช้ใหม่';
                this.isEditMode = false;
                this.resetCurrentUser();
                this.formActionUrl = '{{ route("manager.storeUser") }}';
                this.isModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            openEditModal(user) {
                this.modalTitle = 'แก้ไขผู้ใช้: ' + user.username;
                this.isEditMode = true;
                this.currentUser = JSON.parse(JSON.stringify(user));
                this.formActionUrl = '{{ url("manager/users") }}/' + user.user_id; // สร้าง URL สำหรับ "แก้ไข"
                this.isModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            deleteUser(event, userId) {
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?')) {
                    event.target.closest('form').submit();
                }
            },
            
            getDeleteUrl(userId) {
                return '{{ url("manager/users") }}/' + userId;
            },

            resetCurrentUser() {
                this.currentUser = {
                    user_id: null, username: '', first_name: '',
                    last_name: '', email: '', tel: '',
                    user_type_id: '', status: 'active'
                };
            }
        };
    }

    // เราจะใช้ lucide.createIcons() ที่ถูกเรียกใน app.blade.php
    // แต่ถ้าไอคอนใน Modal ไม่ขึ้น ให้ย้าย LDOM มาไว้ที่นี่
    document.addEventListener('DOMContentLoaded', () => {
        // lucide.createIcons() ถูกเรียกใน app.blade.php แล้ว
        // แต่ Alpine.js อาจจะยังไม่พร้อม
        // เราจึงเรียก .createIcons() อีกครั้งใน openAddModal/openEditModal
    });
</script>