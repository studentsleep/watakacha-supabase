<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-indigo-400 leading-tight flex items-center gap-2">
            <i data-lucide="box-select" class="w-6 h-6"></i>
            หน่วยนับ (Item Units)
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-gray-800 p-4 rounded-xl shadow border border-gray-700 flex justify-between items-center gap-4">
                <form method="GET" action="{{ route('manager.units.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาหน่วย..." class="pl-4 py-2 bg-gray-700 border-gray-600 text-white rounded-lg">
                    <button type="submit" class="px-3 py-2 bg-gray-700 text-white rounded-lg"><i data-lucide="search" class="w-4 h-4"></i></button>
                </form>
                <button onclick="toggleModal('addUnitModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มหน่วย
                </button>
            </div>

            <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase w-16">รหัส</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ชื่อหน่วย</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">รายละเอียด</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                        @forelse($units as $unit)
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono">#{{ $unit->id }}</td>
                            <td class="px-6 py-4 font-bold text-indigo-300 text-base" title="{{ $unit->description }}">{{ $unit->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $unit->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-medium">
                                <button onclick="toggleModal('updateUnitModal-{{ $unit->id }}', true)" class="text-blue-400 hover:text-blue-300 mr-2"><i data-lucide="file-pen-line" class="w-5 h-5"></i></button>
                                <form action="{{ route('manager.units.destroy', $unit->id) }}" method="POST" class="inline-block" onsubmit="return confirm('ลบ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-300"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">ไม่พบข้อมูล</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($units->hasPages()) <div class="px-6 py-4 bg-gray-800 border-t border-gray-700">{{ $units->links() }}</div> @endif
            </div>
            @include('manager.modals.add-unit')
            @foreach($units as $unit) @include('manager.modals.update-unit', ['unit' => $unit]) @endforeach
        </div>
    </div>
</x-app-layout>