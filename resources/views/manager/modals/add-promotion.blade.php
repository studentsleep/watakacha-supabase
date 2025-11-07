{{-- Modal เพิ่ม Promotion --}}
<div id="addPromotionModal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" onclick="toggleModal('addPromotionModal', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document">
            <form action="{{ route('manager.promotions.store') }}" method="POST">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">เพิ่มโปรโมชั่น</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <x-input-label for="promotion_name" :value="__('ชื่อโปรโมชั่น')" />
                            <x-text-input type="text" name="promotion_name" id="promotion_name" required class="mt-1 block w-full" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="discount_type" :value="__('ประเภทส่วนลด')" />
                                <select name="discount_type" id="discount_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount (THB)</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="discount_value" :value="__('มูลค่าส่วนลด')" />
                                <x-text-input type="number" name="discount_value" id="discount_value" step="0.01" min="0" required class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('วันเริ่มต้น (ว่างได้)')" />
                                <x-text-input type="date" name="start_date" id="start_date" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('วันสิ้นสุด (ว่างได้)')" />
                                <x-text-input type="date" name="end_date" id="end_date" class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="status_promo" :value="__('สถานะ')" />
                            <select name="status" id="status_promo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="description_promo" :value="__('รายละเอียด')" />
                            <textarea name="description" id="description_promo" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="sm:ml-3">บันทึก</x-primary-button>
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('addPromotionModal', false)">ยกเลิก</x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>