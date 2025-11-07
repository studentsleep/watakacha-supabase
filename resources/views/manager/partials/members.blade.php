{{-- 
    นี่คือ Partial View สำหรับจัดการ "Members" (บัญชีสมาชิก)
    - สร้างโดยอิงจาก users.blade.php
    - ใช้ Alpine.js x-data="memberManagement(...)"
    - [ใหม่] เพิ่มคอลัมน์ "พอยต์"
    - [ใหม่] Modal รองรับการสร้าง/แก้ไข รหัสผ่านตามหลักสากล
--}}

<div x-data="memberManagement(
    @json($members->items())
)">
    
    <div class="flex justify-end mb-4">
        <button @click="openAddModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> เพิ่มสมาชิก
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Username</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ชื่อ - นามสกุล</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tel</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">พอยต์</th> {{-- <--- [ใหม่] --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">สถานะ</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                
                {{-- วนลูปข้อมูลสมาชิก (จาก $members ที่ถูกแปลงเป็น Alpine data) --}}
                <template x-for="member in members" :key="member.member_id">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" x-text="member.username"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div x-text="(member.first_name || '') + ' ' + (member.last_name || '')"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div x-text="member.email"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div x-text="member.tel" class="text-xs"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="member.points"></td> {{-- <--- [ใหม่] --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                  :class="member.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100'"
                                  x-text="member.status">
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openEditModal(member)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200" title="แก้ไข">
                                <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                            </button>
                            
                            {{-- ปุ่มลบ (Form) --}}
                            <form :action="getDeleteUrl(member.member_id)" method="POST" class="inline-block" @submit.prevent="deleteMember($event, member.member_id)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 ml-2" title="ลบ">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                </template>

                {{-- แถวสำหรับแจ้งเตือนเมื่อไม่มีข้อมูล --}}
                <template x-if="members.length === 0">
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            ไม่พบข้อมูลสมาชิก (กรุณากดปุ่ม "เพิ่มสมาชิก" เพื่อสร้างข้อมูล)
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $members->links() }}
    </div>

    {{-- ▼▼▼ Modal (ป๊อปอัพ) สำหรับเพิ่ม/แก้ไข Member ▼▼▼ --}}
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
                        <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                            
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                                <input type="text" name="username" x-model="currentMember.username" id="username" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                                    <input type="text" name="first_name" x-model="currentMember.first_name" id="first_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                                    <input type="text" name="last_name" x-model="currentMember.last_name" id="last_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" name="email" x-model="currentMember.email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="tel" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tel</label>
                                <input type="text" name="tel" x-model="currentMember.tel" id="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="points" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Points</label>
                                    <input type="number" name="points" x-model="currentMember.points" id="points" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select name="status" id="status" required x-model="currentMember.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            {{-- ▼▼▼ [ใหม่] ส่วนจัดการรหัสผ่าน (แบ่งตาม Add/Edit Mode) ▼▼▼ --}}

                            <template x-if="!isEditMode">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t dark:border-gray-700 pt-4 mt-4">
                                     <div>
                                        <label for="password_add" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password (min 8)</label>
                                        <input type="password" name="password" x-model="currentMember.password" id="password_add" required class="mt-1 block w-full rounded-md ...">
                                    </div>
                                    <div>
                                        <label for="password_confirmation_add" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                                        <input type="password" name="password_confirmation" x-model="currentMember.password_confirmation" id="password_confirmation_add" required class="mt-1 block w-full rounded-md ...">
                                    </div>
                                </div>
                            </template>

                            <template x-if="isEditMode">
                                <div class="border-t dark:border-gray-700 pt-4 mt-4 space-y-4">
                                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">
                                        เปลี่ยนรหัสผ่าน (เว้นว่างทั้งหมดหากไม่เปลี่ยน)
                                    </h4>
                                    <div>
                                        <label for="current_password_edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                                        <input type="password" name="current_password" x-model="currentMember.current_password" id="current_password_edit" class="mt-1 block w-full rounded-md ...">
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="password_edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password (min 8)</label>
                                            <input type="password" name="password" x-model="currentMember.password" id="password_edit" class="mt-1 block w-full rounded-md ...">
                                        </div>
                                        <div>
                                            <label for="password_confirmation_edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                                            <input type="password" name="password_confirmation" x-model="currentMember.password_confirmation" id="password_confirmation_edit" class="mt-1 block w-full rounded-md ...">
                                        </div>
                                    </div>
                                </div>
                            </template>
                            {{-- === จบส่วนรหัสผ่าน === --}}
                            
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

{{-- Script สำหรับ Alpine.js (เฉพาะส่วน Members) --}}
<script>
    function memberManagement(membersData) {
        return {
            members: membersData,
            isModalOpen: false,
            modalTitle: '',
            isEditMode: false,
            formActionUrl: '{{ route("manager.members.store") }}', // URL เริ่มต้นสำหรับ "เพิ่ม"
            
            currentMember: {
                member_id: null,
                username: '',
                first_name: '',
                last_name: '',
                email: '',
                tel: '',
                points: 0,
                status: 'active',
                password: '',
                password_confirmation: '',
                current_password: ''
            },

            openAddModal() {
                this.modalTitle = 'เพิ่มสมาชิกใหม่';
                this.isEditMode = false;
                this.resetCurrentMember();
                this.formActionUrl = '{{ route("manager.members.store") }}';
                this.isModalOpen = true;
                this.ensureIcons();
            },

            openEditModal(member) {
                this.modalTitle = 'แก้ไขสมาชิก: ' + member.username;
                this.isEditMode = true;
                // โหลดข้อมูลหลัก
                this.currentMember = JSON.parse(JSON.stringify(member));
                // รีเซ็ตฟิลด์รหัสผ่านให้ว่างเสมอเมื่อเปิด Modal
                this.currentMember.password = '';
                this.currentMember.password_confirmation = '';
                this.currentMember.current_password = '';
                
                this.formActionUrl = '{{ url("manager/members") }}/' + member.member_id; // สร้าง URL สำหรับ "แก้ไข"
                this.isModalOpen = true;
                this.ensureIcons();
            },

            deleteMember(event, memberId) {
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสมาชิกนี้?')) {
                    event.target.closest('form').submit();
                }
            },
            
            getDeleteUrl(memberId) {
                return '{{ url("manager/members") }}/' + memberId;
            },

            resetCurrentMember() {
                this.currentMember = {
                    member_id: null, username: '', first_name: '',
                    last_name: '', email: '', tel: '',
                    points: 0, status: 'active',
                    password: '', password_confirmation: '', current_password: ''
                };
            },

            // ฟังก์ชันสำหรับเรียก Lucide Icons (เผื่อกรณีที่ Modal โหลดแล้วไอคอนไม่ขึ้น)
            ensureIcons() {
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            }
        };
    }

</script>