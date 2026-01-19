<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-indigo-400 leading-tight flex items-center gap-2">
            <i data-lucide="camera" class="w-6 h-6"></i>
            ช่างภาพ (Photographers)
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filter --}}
            <div class="mb-6 bg-gray-800 p-4 rounded-xl shadow border border-gray-700 flex justify-between items-center gap-4">
                <form method="GET" action="{{ route('manager.photographers.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อ..." class="pl-4 py-2 bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg"><i data-lucide="search" class="w-4 h-4"></i></button>
                </form>
                <button onclick="toggleModal('addPhotographerModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มช่างภาพ
                </button>
            </div>

            <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase w-16">รหัส</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ชื่อ-นามสกุล</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ติดต่อ (โทร / Line)</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-indigo-400 uppercase">สถานะ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                        @forelse($photographers as $pg)
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono">#{{ $pg->photographer_id }}</td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-indigo-300 text-base">{{ $pg->first_name }} {{ $pg->last_name }}</div>
                            </td>

                            {{-- ✅ แก้ไขส่วนติดต่อ เพิ่ม Line ID --}}
                            <td class="px-6 py-4 text-sm text-gray-400">
                                <div class="flex items-center gap-2 mb-1">
                                    <i data-lucide="phone" class="w-3 h-3 text-gray-500"></i>
                                    {{ $pg->tel }}
                                </div>
                                @if($pg->email)
                                <div class="text-xs text-gray-500 ml-5">{{ $pg->email }}</div>
                                @endif

                                @if($pg->lineid)
                                <div class="flex items-center gap-2 mt-1 text-green-400 font-medium text-xs bg-green-900/20 px-2 py-0.5 rounded w-fit border border-green-900/50">
                                    <i data-lucide="message-circle" class="w-3 h-3"></i>
                                    Line: {{ $pg->lineid }}
                                </div>
                                @endif
                            </td>

                            {{-- ✅ แก้ไขสถานะเป็นภาษาไทย --}}
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $pg->status == 'active' ? 'bg-green-900 text-green-300 border border-green-700' : 'bg-red-900 text-red-300 border border-red-700' }}">
                                    {{ $pg->status == 'active' ? 'เปิดใช้งาน' : 'ระงับการใช้งาน' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right font-medium">
                                <button onclick="toggleModal('updatePhotographerModal-{{ $pg->photographer_id }}', true)" class="text-blue-400 hover:text-blue-300 mr-2">
                                    <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                </button>

                                {{-- Form ลบแบบ SweetAlert --}}
                                <form id="delete-form-{{ $pg->photographer_id }}"
                                    action="{{ route('manager.photographers.destroy', $pg->photographer_id) }}"
                                    method="POST"
                                    class="inline-block">

                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                        onclick="confirmDelete('delete-form-{{ $pg->photographer_id }}')"
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
                @if($photographers->hasPages())
                <div class="px-6 py-4 bg-gray-800 border-t border-gray-700">{{ $photographers->links() }}</div>
                @endif
            </div>

            @include('manager.modals.add-photographer')
            @foreach($photographers as $pg)
            @include('manager.modals.update-photographer', ['photographer' => $pg])
            @endforeach
        </div>
    </div>
</x-app-layout>