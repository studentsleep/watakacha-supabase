<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-indigo-400 leading-tight flex items-center gap-2">
            <i data-lucide="sparkles" class="w-6 h-6"></i>
            ช่างแต่งหน้า (Makeup Artists)
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-gray-800 p-4 rounded-xl shadow border border-gray-700 flex justify-between items-center gap-4">
                <form method="GET" action="{{ route('manager.makeup_artists.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อ..." class="pl-4 py-2 bg-gray-700 border-gray-600 text-white rounded-lg">
                    <button type="submit" class="px-3 py-2 bg-gray-700 text-white rounded-lg"><i data-lucide="search" class="w-4 h-4"></i></button>
                </form>
                <button onclick="toggleModal('addMakeupArtistModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มช่าง
                </button>
            </div>

            <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase w-16">รหัส</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ชื่อ-นามสกุล</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ติดต่อ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">ค่าจ้าง</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-indigo-400 uppercase">สถานะ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                        @forelse($makeup_artists as $artist)
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono">#{{ $artist->makeup_id }}</td>
                            <td class="px-6 py-4 font-bold text-indigo-300 text-base" title="{{ $artist->description }}">{{ $artist->first_name }} {{ $artist->last_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                <div>{{ $artist->tel }}</div>
                                @if($artist->lineid) <div class="text-xs text-green-400">Line: {{ $artist->lineid }}</div> @endif
                            </td>
                            <td class="px-6 py-4 text-right text-white font-mono">฿{{ number_format($artist->price) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $artist->status == 'active' ? 'bg-green-900 text-green-300 border border-green-700' : 'bg-red-900 text-red-300 border border-red-700' }}">
                                    {{ $artist->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                <button onclick="toggleModal('updateMakeupArtistModal-{{ $artist->makeup_id }}', true)" class="text-blue-400 hover:text-blue-300 mr-2"><i data-lucide="file-pen-line" class="w-5 h-5"></i></button>
                                <form action="{{ route('manager.makeup_artists.destroy', $artist->makeup_id) }}" method="POST" class="inline-block" onsubmit="return confirm('ลบ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-300"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
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
                @if($makeup_artists->hasPages()) <div class="px-6 py-4 bg-gray-800 border-t border-gray-700">{{ $makeup_artists->links() }}</div> @endif
            </div>

            @include('manager.modals.add-makeup-artist')
            @foreach($makeup_artists as $artist) @include('manager.modals.update-makeup-artist', ['artist' => $artist]) @endforeach
        </div>
    </div>
</x-app-layout>