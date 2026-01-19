<div id="updatePhotographerModal-{{ $photographer->photographer_id }}" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" onclick="toggleModal('updatePhotographerModal-{{ $photographer->photographer_id }}', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document" onclick="event.stopPropagation()">

            <form action="{{ route('manager.photographers.update', $photographer->photographer_id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">แก้ไข: {{ $photographer->first_name }}</h3>

                    <div class="mt-4 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="first_name_pg-{{ $photographer->photographer_id }}" :value="__('ชื่อจริง')" class="after:content-['*'] after:text-red-500 after:ml-0.5" />
                                <x-text-input type="text" name="first_name" id="first_name_pg-{{ $photographer->photographer_id }}" value="{{ $photographer->first_name }}" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="last_name_pg-{{ $photographer->photographer_id }}" :value="__('นามสกุล')" />
                                <x-text-input type="text" name="last_name" id="last_name_pg-{{ $photographer->photographer_id }}" value="{{ $photographer->last_name }}" class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tel_pg-{{ $photographer->photographer_id }}" :value="__('เบอร์โทร')" class="after:content-['*'] after:text-red-500 after:ml-0.5" />
                                <x-text-input type="text" name="tel" id="tel_pg-{{ $photographer->photographer_id }}" value="{{ $photographer->tel }}" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="email_pg-{{ $photographer->photographer_id }}" :value="__('อีเมล')" />
                                <x-text-input type="email" name="email" id="email_pg-{{ $photographer->photographer_id }}" value="{{ $photographer->email }}" class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="lineid_pg-{{ $photographer->photographer_id }}" :value="__('Line ID')" class="after:content-['*'] after:text-red-500 after:ml-0.5" />
                                <x-text-input type="text" name="lineid" id="lineid_pg-{{ $photographer->photographer_id }}" value="{{ $photographer->lineid }}" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="status_pg-{{ $photographer->photographer_id }}" :value="__('สถานะ')" />
                                <select name="status" id="status_pg-{{ $photographer->photographer_id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                    <option value="active" @selected($photographer->status == 'active')>เปิดใช้งาน</option>
                                    <option value="inactive" @selected($photographer->status == 'inactive')>ระงับการใช้งาน</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ปุ่มกด --}}
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="w-full sm:w-auto sm:ml-3">บันทึกแก้ไข</x-primary-button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700" onclick="toggleModal('updatePhotographerModal-{{ $photographer->photographer_id }}', false)">
                        ยกเลิก
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>