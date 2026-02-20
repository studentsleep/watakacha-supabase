<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-indigo-400 leading-tight flex items-center gap-2">
            <i data-lucide="percent" class="w-6 h-6"></i>
            โปรโมชั่น (Promotions)
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filter & Button --}}
            <div class="mb-6 bg-gray-800 p-4 rounded-xl shadow border border-gray-700 flex justify-between items-center gap-4">
                <form method="GET" action="{{ route('manager.promotions.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาโปรโมชั่น..." class="pl-4 py-2 bg-gray-700 border-gray-600 text-white rounded-lg">
                    <button type="submit" class="px-3 py-2 bg-gray-700 text-white rounded-lg"><i data-lucide="search" class="w-4 h-4"></i></button>
                </form>
                <button onclick="toggleModal('addPromotionModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มโปรโมชั่น
                </button>
            </div>

            <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase w-16">รหัส</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ชื่อโปรโมชั่น</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">ส่วนลด</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ระยะเวลา</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-indigo-400 uppercase">สถานะ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                        @forelse($promotions as $promo)
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono">#{{ $promo->promotion_id }}</td>
                            <td class="px-6 py-4 font-bold text-indigo-300 text-base" title="{{ $promo->description }}">{{ $promo->promotion_name }}</td>
                            <td class="px-6 py-4 text-right text-red-400 font-bold">
                                {{ number_format($promo->discount_value) }} {{ $promo->discount_type == 'percentage' ? '%' : '' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                {{ $promo->start_date ? \Carbon\Carbon::parse($promo->start_date)->format('d/m/y') : '-' }} -
                                {{ $promo->end_date ? \Carbon\Carbon::parse($promo->end_date)->format('d/m/y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $promo->status == 'active' ? 'bg-green-900 text-green-300 border border-green-700' : 'bg-red-900 text-red-300 border border-red-700' }}">
                                    {{ $promo->status == 'active' ? 'ใช้งาน' : 'ระงับ' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                <button onclick="toggleModal('updatePromotionModal-{{ $promo->promotion_id }}', true)" class="text-blue-400 hover:text-blue-300 mr-2"><i data-lucide="file-pen-line" class="w-5 h-5"></i></button>
                                <form id="delete-form-{{ $promo->promotion_id }}"

                                    action="{{ route('manager.promotions.destroy', $promo->promotion_id) }}"

                                    method="POST"

                                    class="inline-block">



                                    @csrf

                                    @method('DELETE')



                                    <button type="button"

                                        onclick="confirmDelete('delete-form-{{ $promo->promotion_id }}')"

                                        class="text-red-400 hover:text-red-300">

                                        <i data-lucide="trash-2" class="w-5 h-5"></i>

                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">ไม่พบข้อมูล</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($promotions->hasPages()) <div class="px-6 py-4 bg-gray-800 border-t border-gray-700">{{ $promotions->links() }}</div> @endif
            </div>

            @include('manager.modals.add-promotion')
            @foreach($promotions as $promo) @include('manager.modals.update-promotion', ['promo' => $promo]) @endforeach
        </div>
    </div>
</x-app-layout>