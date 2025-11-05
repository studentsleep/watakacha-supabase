{{-- Modal แก้ไข User --}}
<div id="updateUserModal-{{ $user->user_id }}" 
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" 
     onclick="toggleModal('updateUserModal-{{ $user->user_id }}', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
             role="document">
            <form action="{{ route('manager.users.update', $user->user_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        แก้ไขผู้ใช้: {{ $user->username }}
                    </h3>
                    <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="username-{{ $user->user_id }}" :value="__('Username')" />
                                <x-text-input type="text" name="username" id="username-{{ $user->user_id }}" value="{{ $user->username }}" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="email-{{ $user->user_id }}" :value="__('Email')" />
                                <x-text-input type="email" name="email" id="email-{{ $user->user_id }}" value="{{ $user->email }}" required class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="first_name-{{ $user->user_id }}" :value="__('First Name')" />
                                <x-text-input type="text" name="first_name" id="first_name-{{ $user->user_id }}" value="{{ $user->first_name }}" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="last_name-{{ $user->user_id }}" :value="__('Last Name')" />
                                <x-text-input type="text" name="last_name" id="last_name-{{ $user->user_id }}" value="{{ $user->last_name }}" required class="mt-1 block w-full" />
                            </div>
                        </div>

                         <div>
                            <x-input-label for="tel-{{ $user->user_id }}" :value="__('Telephone')" />
                            <x-text-input type="text" name="tel" id="tel-{{ $user->user_id }}" value="{{ $user->tel }}" class="mt-1 block w-full" />
                        </div>

                        <div>
                            <x-input-label for="password-{{ $user->user_id }}" :value="__('New Password (ว่างไว้หากไม่เปลี่ยน)')" />
                            <x-text-input type="password" name="password" id="password-{{ $user->user_id }}" class="mt-1 block w-full" />
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="user_type_id-{{ $user->user_id }}" :value="__('User Type')" />
                                <select name="user_type_id" id="user_type_id-{{ $user->user_id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- เลือกประเภท --</option>
                                    @foreach($user_types as $type)
                                        <option value="{{ $type->user_type_id }}" @selected($user->user_type_id == $type->user_type_id)>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="status-{{ $user->user_id }}" :value="__('Status')" />
                                <select name="status" id="status-{{ $user->user_id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="active" @selected($user->status == 'active')>Active</option>
                                    <option value="inactive" @selected($user->status == 'inactive')>Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="sm:ml-3">
                        บันทึกข้อมูล
                    </x-primary-button>
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('updateUserModal-{{ $user->user_id }}', false)">
                        ยกเลิก
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>