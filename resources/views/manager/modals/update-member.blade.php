{{-- Modal แก้ไข Member --}}
<div id="updateMemberModal-{{ $member->member_id }}" 
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" 
     onclick="toggleModal('updateMemberModal-{{ $member->member_id }}', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
             role="document">
            <form action="{{ route('manager.members.update', $member->member_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        แก้ไขสมาชิก: {{ $member->username }}
                    </h3>

                    {{-- ▼▼▼ [เพิ่มโค้ดนี้] เพื่อแสดงข้อผิดพลาด ▼▼▼ --}}
                    @if ($errors->any())
                        <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">เกิดข้อผิดพลาด!</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        
                        {{-- ( ... username, email, first_name, last_name, tel ... ) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="username-{{ $member->member_id }}" :value="__('ชื่อผู้ใช้')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                                <x-text-input type="text" name="username" id="username-{{ $member->member_id }}" value="{{ $member->username }}" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="email-{{ $member->member_id }}" :value="__('อีเมล')" />
                                <x-text-input type="email" name="email" id="email-{{ $member->member_id }}" value="{{ $member->email }}" required class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="first_name-{{ $member->member_id }}" :value="__('ชื่อจริง')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                                <x-text-input type="text" name="first_name" id="first_name-{{ $member->member_id }}" value="{{ $member->first_name }}" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="last_name-{{ $member->member_id }}" :value="__('นามสกุล')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                                <x-text-input type="text" name="last_name" id="last_name-{{ $member->member_id }}" value="{{ $member->last_name }}"  class="mt-1 block w-full" />
                            </div>
                        </div>
                         <div>
                            <x-input-label for="tel-{{ $member->member_id }}" :value="__('เบอร์มือถือ')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                            <x-text-input type="text" name="tel" id="tel-{{ $member->member_id }}" value="{{ $member->tel }}" class="mt-1 block w-full" require/>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                             {{-- ( ... points, status ... ) --}}
                             <div>
                                <x-input-label for="points-{{ $member->member_id }}" :value="__('แต้มสะสม')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                                <x-text-input type="number" name="points" id="points-{{ $member->member_id }}" value="{{ $member->points }}" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="status-{{ $member->member_id }}" :value="__('สถานะ')" />
                                <select name="status" id="status-{{ $member->member_id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="active" @selected($member->status == 'active')>กำลังใช้งาน</option>
                                    <option value="inactive" @selected($member->status == 'inactive')>ระงับการใช้งาน</option>
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
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('updateMemberModal-{{ $member->member_id }}', false)">
                        ยกเลิก
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>