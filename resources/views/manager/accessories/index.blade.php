<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-indigo-400 leading-tight flex items-center gap-2">
            <i data-lucide="headphones" class="w-6 h-6"></i>
            จัดการอุปกรณ์เสริม (Accessories)
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filter Section (เหมือน Items แต่ตัดปุ่มรูปภาพออก) --}}
            <div class="mb-6 bg-gray-800 p-4 rounded-xl shadow border border-gray-700 flex justify-between items-center gap-4">
                <form method="GET" action="{{ route('manager.accessories.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาอุปกรณ์..."
                            class="w-full pl-10 pr-4 py-2 bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i data-lucide="search" class="w-4 h-4"></i></div>
                    </div>
                    <button type="submit" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg"><i data-lucide="search" class="w-4 h-4"></i></button>
                </form>
                <button onclick="toggleModal('addAccessoryModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มอุปกรณ์
                </button>
            </div>

            <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase w-16">รหัส</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ชื่ออุปกรณ์ / ประเภท</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">รายละเอียด</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">ราคา</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">คงเหลือ / หน่วย</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                        @forelse($accessories as $acc)
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono">#{{ $acc->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-indigo-300 text-base mb-1" title="อุปกรณ์เสริม">{{ $acc->name }}</div>
                                <div class="text-xs text-gray-500 bg-gray-700/50 px-2 py-0.5 rounded inline-block border border-gray-600">
                                    {{ $acc->type->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                <div class="max-w-xs truncate" title="{{ $acc->description }}">
                                    {{ $acc->description ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-white font-mono">{{ number_format($acc->price, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $acc->stock > 0 ? 'bg-green-900 text-green-300 border border-green-700' : 'bg-red-900 text-red-300 border border-red-700' }}">
                                        {{ number_format($acc->stock) }}
                                    </span>
                                    <span class="text-sm text-gray-400">{{ $acc->unit->name ?? 'ชิ้น' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                <button onclick="toggleModal('updateAccessoryModal-{{ $acc->id }}', true)" class="text-blue-400 hover:text-blue-300 mr-2"><i data-lucide="file-pen-line" class="w-5 h-5"></i></button>
                                <form id="delete-form-{{ $acc->id }}"
                                    action="{{ route('manager.accessories.destroy', $acc->id) }}"
                                    method="POST"
                                    class="inline-block">

                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                        onclick="confirmDelete('delete-form-{{ $acc->id }}')"
                                        class="text-red-400 hover:text-red-300">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">ไม่พบข้อมูล</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($accessories->hasPages()) <div class="px-6 py-4 bg-gray-800 border-t border-gray-700">{{ $accessories->links() }}</div> @endif
            </div>

            @include('manager.modals.add-accessory')
            @foreach($accessories as $acc) @include('manager.modals.update-accessory', ['accessory' => $acc]) @endforeach
        </div>
    </div>
</x-app-layout>