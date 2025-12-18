{{-- Modal แก้ไข Makeup Artist --}}
<div id="updateMakeupArtistModal-{{ $artist->makeup_id }}" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden" onclick="toggleModal('updateMakeupArtistModal-{{ $artist->makeup_id }}', false, event)">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document">
            <form action="{{ route('manager.makeup_artists.update', $artist->makeup_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">แก้ไข: {{ $artist->first_name }}</h3>
                    <div class="mt-4 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="first_name-{{ $artist->makeup_id }}" :value="__('ชื่อจริง')" />
                                <x-text-input type="text" name="first_name" id="first_name-{{ $artist->makeup_id }}" value="{{ $artist->first_name }}" required class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="last_name-{{ $artist->makeup_id }}" :value="__('นามสกุล')" />
                                <x-text-input type="text" name="last_name" id="last_name-{{ $artist->makeup_id }}" value="{{ $artist->last_name }}" required class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tel-{{ $artist->makeup_id }}" :value="__('เบอร์โทร')" />
                                <x-text-input type="text" name="tel" id="tel-{{ $artist->makeup_id }}" value="{{ $artist->tel }}" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="email-{{ $artist->makeup_id }}" :value="__('Email')" />
                                <x-text-input type="email" name="email" id="email-{{ $artist->makeup_id }}" value="{{ $artist->email }}" class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="lineid-{{ $artist->makeup_id }}" :value="__('Line ID')" />
                                <x-text-input type="text" name="lineid" id="lineid-{{ $artist->makeup_id }}" value="{{ $artist->lineid }}" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="price-{{ $artist->makeup_id }}" :value="__('ราคา')" />
                                <x-text-input type="number" name="price" id="price-{{ $artist->makeup_id }}" value="{{ $artist->price }}" step="0.01" min="0" required class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="status-{{ $artist->makeup_id }}" :value="__('สถานะ')" />
                            <select name="status" id="status-{{ $artist->makeup_id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                <option value="active" @selected($artist->status == 'active')>Active</option>
                                <option value="inactive" @selected($artist->status == 'inactive')>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="description-{{ $artist->makeup_id }}" :value="__('รายละเอียด')" />
                        <textarea name="description" id="description-{{ $artist->makeup_id }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">{{ $artist->description }}</textarea>
                    </div>
                </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <x-primary-button type="submit" class="sm:ml-3">บันทึก</x-primary-button>
            <x-secondary-button type="button" class="mt-3 sm:mt-0" onclick="toggleModal('updateMakeupArtistModal-{{ $artist->makeup_id }}', false)">ยกเลิก</x-secondary-button>
        </div>
        </form>
    </div>
</div>
</div>