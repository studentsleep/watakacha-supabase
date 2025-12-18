{{-- Modal เพิ่ม User --}}
<div id="addUserModal" 
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" 
     onclick="toggleModal('addUserModal', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
             role="document">
            <form action="{{ route('manager.users.store') }}" method="POST">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        เพิ่มผู้ใช้ใหม่
                    </h3>
                    <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="username" :value="__('ชื่อผู้ใช้')" />
                                <x-text-input type="text" name="username" id="username" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('อีเมล')" />
                                <x-text-input type="email" name="email" id="email" required class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="first_name" :value="__('ชื่อ')" />
                                <x-text-input type="text" name="first_name" id="first_name" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="last_name" :value="__('นามสกุล')" />
                                <x-text-input type="text" name="last_name" id="last_name" required class="mt-1 block w-full" />
                            </div>
                        </div>

                         <div>
                            <x-input-label for="tel" :value="__('เบอร์มือถือ')" />
                            <x-text-input type="text" name="tel" id="tel" class="mt-1 block w-full" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('รหัสผ่าน (ขั้นต่ำ 8 ตัว)')" />
                            <x-text-input type="password" name="password" id="password" required class="mt-1 block w-full" />
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="user_type_id" :value="__('ประเภทผู้ใช้')" />
                                <select name="user_type_id" id="user_type_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- เลือกประเภท --</option>
                                    @foreach($user_types as $type)
                                        <option value="{{ $type->user_type_id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('สถานะ')" />
                                <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="sm:ml-3">
                        บันทึกข้อมูล
                    </x-primary-button>
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('addUserModal', false)">
                        ยกเลิก
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>