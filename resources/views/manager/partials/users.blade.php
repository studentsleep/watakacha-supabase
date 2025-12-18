{{-- resources/views/manager/partials/users.blade.php --}}

<div x-data="userManagement(
    @json($users->items()), 
    @json($user_types),
    {{ $errors->any() ? 'true' : 'false' }}
)">

    {{-- ปุ่มเพิ่มผู้ใช้ --}}
    <div class="flex justify-end mb-4">
        <button @click="openAddModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มผู้ใช้
        </button>
    </div>

    {{-- ▼▼▼ แก้ไขส่วนตาราง (Scrollbar ด้านใน + Sticky Header) ▼▼▼ --}}
    <div class="overflow-auto max-h-[70vh] border border-gray-200 dark:border-gray-700 rounded-md shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-fixed">
            <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10 shadow-sm"> {{-- เพิ่ม sticky top-0 --}}
                <tr>
                    {{-- เพิ่ม bg-gray-50 ให้ th เพื่อไม่ให้ข้อมูลทะลุหัวตารางตอนเลื่อน --}}
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-1/6 bg-gray-50 dark:bg-gray-700">ชื่อผู้ใช้</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-1/5 bg-gray-50 dark:bg-gray-700">ชื่อ - นามสกุล</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-1/5 bg-gray-50 dark:bg-gray-700">อีเมล</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-24 bg-gray-50 dark:bg-gray-700">เบอร์มือถือ</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-24 bg-gray-50 dark:bg-gray-700">ประเภท</th>
                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-24 bg-gray-50 dark:bg-gray-700">สถานะ</th>
                    <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-24 bg-gray-50 dark:bg-gray-700">จัดการ</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="user in users" :key="user.user_id">
                    <tr>
                        <td class="px-3 py-4 whitespace-normal break-words text-sm font-medium" x-text="user.username"></td>
                        
                        <td class="px-3 py-4 whitespace-normal text-sm">
                            <div x-text="(user.first_name || '') + ' ' + (user.last_name || '')"></div>
                        </td>
                        
                        <td class="px-3 py-4 whitespace-normal break-all text-sm text-gray-500 dark:text-gray-400">
                            <div x-text="user.email"></div>
                        </td>
                        
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div x-text="user.tel" class="text-xs"></div>
                        </td>
                        
                        <td class="px-3 py-4 whitespace-nowrap text-sm" x-text="user.user_type ? user.user_type.name : 'N/A'"></td>
                        
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="user.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100'"
                                x-text="user.status">
                            </span>
                        </td>
                        
                        <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openEditModal(user)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200" title="แก้ไข">
                                <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                            </button>
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
                <template x-if="users.length === 0">
                    <tr>
                        <td colspan="7" class="px-3 py-4 text-center text-gray-500 dark:text-gray-400">
                            ไม่พบข้อมูลผู้ใช้ (กรุณากดปุ่ม "เพิ่มผู้ใช้" เพื่อสร้างข้อมูล)
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    {{-- ▲▲▲ จบส่วนแก้ไขตาราง ▲▲▲ --}}

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    {{-- Modal ยังคงเดิม --}}
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
                    
                    <div style="position: absolute; left: -9999px; top: -9999px; opacity: 0;">
                        <input type="text" name="fake_usernameremembered" tabindex="-1">
                        <input type="password" name="fake_passwordremembered" tabindex="-1">
                    </div>
                    
                    <input type="hidden" name="_method" value="PATCH" :disabled="!isEditMode">

                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title" x-text="modalTitle"></h3>

                        @if ($errors->any())
                        <div class="mb-4 bg-red-50 dark:bg-red-900 p-4 rounded-md">
                            <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-200">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="mt-4 space-y-4 max-h-[60vh] overflow-y-auto pr-2">

                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อผู้ใช้</label>
                                <input type="text" name="username" x-model="currentUser.username" id="username" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" autocomplete="off"
                                    onfocus="this.removeAttribute('readonly');">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อ</label>
                                    <input type="text" name="first_name" x-model="currentUser.first_name" id="first_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">นามสกุล</label>
                                    <input type="text" name="last_name" x-model="currentUser.last_name" id="last_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">อีเมล</label>
                                <input type="email" name="email" x-model="currentUser.email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="tel" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เบอร์มือถือ</label>
                                <input type="text" name="tel" x-model="currentUser.tel" id="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="user_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ประเภทผู้ใช้</label>
                                    <select name="user_type_id" id="user_type_id" x-model="currentUser.user_type_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">-- เลือกประเภท --</option>
                                        <template x-for="type in userTypes" :key="type.user_type_id">
                                            <option :value="type.user_type_id" x-text="type.name" :selected="type.user_type_id == currentUser.user_type_id"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">สถานะ</label>
                                    <select name="status" id="status" x-model="currentUser.status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="active">กำลังใช้งาน</option>
                                        <option value="inactive">ไม่ได้ใช้งาน</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสผ่าน</label>
                                <input type="password" name="password" id="password"
                                    :placeholder="isEditMode ? '(เว้นว่างไว้หากไม่ต้องการเปลี่ยน)' : 'กรอกรหัสผ่าน (ขั้นต่ำ 8 ตัวอักษร)'"
                                    :required="!isEditMode"
                                    autocomplete="new-password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    onfocus="this.removeAttribute('readonly');">
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
    function userManagement(usersData, userTypesData, hasErrors) {
        return {
            users: usersData,
            userTypes: userTypesData,
            isModalOpen: hasErrors,
            modalTitle: hasErrors ? 'ตรวจสอบข้อมูล (Error)' : '',
            isEditMode: false,
            formActionUrl: '{{ route("manager.storeUser") }}',

            currentUser: {
                user_id: null,
                username: '{{ old("username") }}',
                first_name: '{{ old("first_name") }}',
                last_name: '{{ old("last_name") }}',
                email: '{{ old("email") }}',
                tel: '{{ old("tel") }}',
                user_type_id: '{{ old("user_type_id") }}',
                status: '{{ old("status", "active") }}'
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
                this.formActionUrl = '{{ url("manager/users") }}/' + user.user_id;
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
                    user_id: null,
                    username: '',
                    first_name: '',
                    last_name: '',
                    email: '',
                    tel: '',
                    user_type_id: '',
                    status: 'active'
                };
            }
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        // หากต้องการ logic เพิ่มเติมเมื่อโหลดหน้า
    });
</script>