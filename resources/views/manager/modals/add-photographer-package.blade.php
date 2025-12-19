{{-- Modal เพิ่ม Package --}}
<div id="addPhotographerPackageModal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" onclick="toggleModal('addPhotographerPackageModal', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document">
            <form action="{{ route('manager.photographer_packages.store') }}" method="POST">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">เพิ่มแพ็คเกจช่างภาพ</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <x-input-label for="package_name" :value="__('ชื่อแพ็คเกจ')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                            <x-text-input type="text" name="package_name" id="package_name" required class="mt-1 block w-full" require/>
                        </div>
                        <div>
                            <x-input-label for="price_pkg" :value="__('ราคา')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                            <x-text-input type="number" name="price" id="price_pkg" step="0.01" min="0" required class="mt-1 block w-full" require/>
                        </div>
                        <div>
                            <x-input-label for="description_pkg" :value="__('รายละเอียด')" />
                            <textarea name="description" id="description_pkg" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="sm:ml-3">บันทึก</x-primary-button>
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('addPhotographerPackageModal', false)">ยกเลิก</x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>