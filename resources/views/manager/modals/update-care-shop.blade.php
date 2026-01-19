{{-- Modal แก้ไข Care Shop --}}
<div id="updateCareShopModal-{{ $shop->care_shop_id }}" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" onclick="toggleModal('updateCareShopModal-{{ $shop->care_shop_id }}', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document">
            <form action="{{ route('manager.care_shops.update', $shop->care_shop_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">แก้ไขร้าน: {{ $shop->care_name }}</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <x-input-label for="care_name-{{ $shop->care_shop_id }}" :value="__('ชื่อร้าน')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                            <x-text-input type="text" name="care_name" id="care_name-{{ $shop->care_shop_id }}" value="{{ $shop->care_name }}" required class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="address-{{ $shop->care_shop_id }}" :value="__('ที่อยู่')" />
                            <textarea name="address" id="address-{{ $shop->care_shop_id }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">{{ $shop->address }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tel-{{ $shop->care_shop_id }}" :value="__('เบอร์โทร')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                                <x-text-input type="text" name="tel" id="tel-{{ $shop->care_shop_id }}" value="{{ $shop->tel }}" class="mt-1 block w-full" require/>
                            </div>
                            <div>
                                <x-input-label for="email-{{ $shop->care_shop_id }}" :value="__('อีเมล')" />
                                <x-text-input type="email" name="email" id="email-{{ $shop->care_shop_id }}" value="{{ $shop->email }}" class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="status-{{ $shop->care_shop_id }}" :value="__('สถานะ')" />
                            <select name="status" id="status-{{ $shop->care_shop_id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                <option value="active" @selected($shop->status == 'active')>กำลังใช้งาน</option>
                                <option value="inactive" @selected($shop->status == 'inactive')>ระงับการใช้งาน</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="sm:ml-3">บันทึก</x-primary-button>
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('updateCareShopModal-{{ $shop->care_shop_id }}', false)">ยกเลิก</x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>