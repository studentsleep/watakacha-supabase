<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-indigo-400 leading-tight flex items-center gap-2">
            <i data-lucide="package" class="w-6 h-6"></i>
            จัดการคลังสินค้า (Items)
        </h2>
    </x-slot>

    @if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 mx-8 mt-4 rounded shadow-md relative" role="alert">
        <strong class="font-bold">เกิดข้อผิดพลาด!</strong>
        <span class="block sm:inline">บันทึกข้อมูลไม่สำเร็จ โปรดตรวจสอบ:</span>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 mx-8 mt-4 rounded shadow-md">
        {{ session('success') }}
    </div>
    @endif
    <div class="py-8 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Search & Button --}}
            <div class="mb-6 bg-gray-800 p-4 rounded-xl shadow border border-gray-700 flex justify-between items-center gap-4">
                <form method="GET" action="{{ route('manager.items.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อสินค้า..."
                            class="w-full pl-10 pr-4 py-2 bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><i data-lucide="search" class="w-4 h-4"></i></div>
                    </div>
                    <button type="submit" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition"><i data-lucide="search" class="w-4 h-4"></i></button>
                </form>

                <button onclick="toggleModal('addItemModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มสินค้า
                </button>
            </div>

            {{-- Table --}}
            <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase tracking-wider w-16">รหัส</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase tracking-wider">รูปภาพ</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase tracking-wider">ชื่อสินค้า / ประเภท</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase tracking-wider">ราคาเช่า</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase tracking-wider">คงเหลือ / หน่วย</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-indigo-400 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase tracking-wider">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 bg-gray-800">
                            @forelse($items as $item)
                            <tr class="hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 text-sm text-gray-400 font-mono">#{{ $item->id }}</td>
                                <td class="px-6 py-4">
                                    @php
                                    // 1. หารูปสินค้า (รูปหลัก หรือ รูปแรก)
                                    $mainImage = $item->images->firstWhere('is_main', true) ?? $item->images->first();
                                    if ($mainImage) {
                                    // ✅ มีรูป: เช็คว่าเป็น Cloudinary หรือ Local
                                    $imageUrl = str_starts_with($mainImage->path, 'http')
                                    ? $mainImage->path
                                    : asset('storage/' . $mainImage->path);
                                    } else {
                                    // ❌ ไม่มีรูป: ให้ดึง Logo ร้านมาแสดงแทน
                                    $imageUrl = asset('images/logo.png');
                                    }
                                    @endphp

                                    {{-- ส่วนแสดงผลรูปภาพ --}}
                                    <img src="{{ $imageUrl }}" class="w-12 h-12 rounded-lg object-cover border border-gray-600">
                                </td>
                                <td class="px-6 py-4">
                                    {{-- Tooltip อยู่ที่ชื่อ --}}
                                    <div class="font-bold text-indigo-300 text-base mb-1 cursor-help" title="{{ $item->description }}">
                                        {{ $item->item_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 bg-gray-700/50 px-2 py-0.5 rounded inline-block border border-gray-600">
                                        {{ $item->type->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-white font-mono">{{ number_format($item->price, 2) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $item->stock > 0 ? 'bg-green-900 text-green-300 border border-green-700' : 'bg-red-900 text-red-300 border border-red-700' }}">
                                            {{ number_format($item->stock) }}
                                        </span>
                                        <span class="text-sm text-gray-400">{{ $item->unit->name ?? 'ชิ้น' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($item->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-300 border border-green-700">
                                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></span>
                                        พร้อมใช้งาน
                                    </span>
                                    @elseif ($item->status === 'maintenance')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900 text-yellow-300 border border-yellow-700">
                                        <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1.5 animate-pulse"></span>
                                        ซ่อมบำรุง
                                    </span>
                                    @else {{-- inactive --}}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900 text-red-300 border border-red-700">
                                        ระงับการใช้งาน
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-medium">
                                    <button onclick="toggleModal('updateItemModal-{{ $item->id }}', true)" class="text-blue-400 hover:text-blue-300 mr-2"><i data-lucide="file-pen-line" class="w-5 h-5"></i></button>
                                    <button onclick="toggleModal('updateImageModal-{{ $item->id }}', true)" class="text-indigo-400 hover:text-indigo-300 mr-2"><i data-lucide="image" class="w-5 h-5"></i></button>
                                    <form id="delete-form-{{ $item->id }}"
                                        action="{{ route('manager.items.destroy', $item->id) }}"
                                        method="POST"
                                        class="inline-block">

                                        @csrf
                                        @method('DELETE')

                                        <button type="button"
                                            onclick="confirmDelete('delete-form-{{ $item->id }}')"
                                            class="text-red-400 hover:text-red-300">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">ไม่พบข้อมูลสินค้า</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($items->hasPages())
                <div class="px-6 py-4 bg-gray-800 border-t border-gray-700">{{ $items->links() }}</div>
                @endif
            </div>

            @include('manager.modals.add-tailwind', ['types' => $types, 'units' => $units])
            @foreach($items as $item)
            @include('manager.modals.update-tailwind', ['item' => $item, 'types' => $types, 'units' => $units])
            @endforeach
        </div>
    </div>
</x-app-layout>