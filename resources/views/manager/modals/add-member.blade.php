{{-- Modal เพิ่ม Member --}}
<div id="addMemberModal" 
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" 
     onclick="toggleModal('addMemberModal', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
             role="document">
            <form action="{{ route('manager.members.store') }}" method="POST">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        เพิ่มสมาชิกใหม่
                    </h3>
                    <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        
                        {{-- ( ... username, email, first_name, last_name, tel ... ) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="username_add_member" :value="__('Username')" />
                                <x-text-input type="text" name="username" id="username_add_member" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="email_add_member" :value="__('Email')" />
                                <x-text-input type="email" name="email" id="email_add_member" required class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="first_name_add_member" :value="__('First Name')" />
                                <x-text-input type="text" name="first_name" id="first_name_add_member" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="last_name_add_member" :value="__('Last Name')" />
                                <x-text-input type="text" name="last_name" id="last_name_add_member" required class="mt-1 block w-full" />
                            </div>
                        </div>
                         <div>
                            <x-input-label for="tel_add_member" :value="__('Telephone')" />
                            <x-text-input type="text" name="tel" id="tel_add_member" class="mt-1 block w-full" />
                        </div>

                        {{-- ▼▼▼ [แก้ไข] ช่อง Password และ Confirmation ▼▼▼ --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="password_add_member" :value="__('Password (min 8 chars)')" />
                                <x-text-input type="password" name="password" id="password_add_member" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation_add_member" :value="__('Confirm Password')" />
                                <x-text-input type="password" name="password_confirmation" id="password_confirmation_add_member" required class="mt-1 block w-full" />
                            </div>
                        </div>
                        {{-- ▲▲▲ จบส่วนที่แก้ไข ▲▲▲ --}}

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- ( ... points, status ... ) --}}
                            <div>
                                <x-input-label for="points_add_member" :value="__('Points')" />
                                <x-text-input type="number" name="points" id="points_add_member" value="0" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="status_add_member" :value="__('Status')" />
                                <select name="status" id="status_add_member" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                
                {{-- ( ... Buttons ... ) --}}
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="sm:ml-3">
                        บันทึกข้อมูล
                    </x-primary-button>
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('addMemberModal', false)">
                        ยกเลิก
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>