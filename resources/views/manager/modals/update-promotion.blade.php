{{-- Modal แก้ไข Promotion --}}
<div id="updatePromotionModal-{{ $promo->promotion_id }}" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" onclick="toggleModal('updatePromotionModal-{{ $promo->promotion_id }}', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document">
            <form action="{{ route('manager.promotions.update', $promo->promotion_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">แก้ไข: {{ $promo->promotion_name }}</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <x-input-label for="promotion_name-{{ $promo->promotion_id }}" :value="__('ชื่อโปรโมชั่น')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                            <x-text-input type="text" name="promotion_name" id="promotion_name-{{ $promo->promotion_id }}" value="{{ $promo->promotion_name }}" required class="mt-1 block w-full" require/>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="discount_type-{{ $promo->promotion_id }}" :value="__('ประเภทส่วนลด')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                                <select name="discount_type" id="discount_type-{{ $promo->promotion_id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" require>
                                    <option value="percentage" @selected($promo->discount_type == 'percentage')>Percentage (%)</option>
                                    <option value="fixed" @selected($promo->discount_type == 'fixed')>Fixed Amount (THB)</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="discount_value-{{ $promo->promotion_id }}" :value="__('มูลค่าส่วนลด')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                                <x-text-input type="number" name="discount_value" id="discount_value-{{ $promo->promotion_id }}" require value="{{ $promo->discount_value }}" step="0.01" min="0" required class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date-{{ $promo->promotion_id }}" :value="__('วันเริ่มต้น')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                                <x-text-input type="date" name="start_date" id="start_date-{{ $promo->promotion_id }}" value="{{ $promo->start_date?->format('Y-m-d') }}" class="mt-1 block w-full" require/>
                            </div>
                            <div>
                                <x-input-label for="end_date-{{ $promo->promotion_id }}" :value="__('วันสิ้นสุด')" class="after:content-['*'] after:text-red-500 after:ml-0.5"/>
                                <x-text-input type="date" name="end_date" id="end_date-{{ $promo->promotion_id }}" value="{{ $promo->end_date?->format('Y-m-d') }}" class="mt-1 block w-full" require/>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="status_promo-{{ $promo->promotion_id }}" :value="__('สถานะ')" />
                            <select name="status" id="status_promo-{{ $promo->promotion_id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                <option value="active" @selected($promo->status == 'active')>Active</option>
                                <option value="inactive" @selected($promo->status == 'inactive')>Inactive</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="description_promo-{{ $promo->promotion_id }}" :value="__('รายละเอียด')" />
                            <textarea name="description" id="description_promo-{{ $promo->promotion_id }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">{{ $promo->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-primary-button type="submit" class="sm:ml-3">บันทึก</x-primary-button>
                    <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('updatePromotionModal-{{ $promo->promotion_id }}', false)">ยกเลิก</x-secondary-button>
                </div>
            </form>
        </div>
    </div>
</div>