<x-layouts.frontend>
    {{-- Force ให้ Navbar เป็นสีขาวตลอด --}}
    <div x-init="scrolled = true"></div>

    <div class="pt-24 pb-12 min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8 pb-6 border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-8 h-8 text-brand-500"></i>
                    คอลเลคชันชุดทั้งหมด
                </h1>
                <p class="text-gray-500 mt-2">เลือกชมชุดราตรี ชุดไทย และสูท คุณภาพพรีเมียมกว่า {{ $items->total() }} รายการ</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                {{-- 🔍 SIDEBAR FILTER (ส่วนที่เพิ่มใหม่) --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- กล่องค้นหา --}}
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i data-lucide="search" class="w-4 h-4"></i> ค้นหา
                        </h3>
                        <form action="{{ route('catalog') }}" method="GET">
                            @if(request('type_id'))
                            <input type="hidden" name="type_id" value="{{ request('type_id') }}">
                            @endif
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="ชื่อ, ประเภท, หน่วย..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition text-sm">
                                <div class="absolute left-3 top-3 text-gray-400">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <button type="submit" class="w-full mt-3 bg-brand-600 text-white py-2 rounded-xl text-sm font-bold hover:bg-brand-700 transition">
                                ค้นหาเลย
                            </button>
                        </form>
                    </div>

                    {{-- กล่องหมวดหมู่ --}}
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <i data-lucide="filter" class="w-4 h-4"></i> หมวดหมู่
                            </h3>
                            @if(request('type_id') || request('search'))
                            <a href="{{ route('catalog') }}" class="text-xs text-red-500 hover:underline">ล้างค่า</a>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <a href="{{ route('catalog', ['search' => request('search')]) }}"
                                class="block px-3 py-2 rounded-lg text-sm transition flex justify-between items-center {{ !request('type_id') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                                <span>ทั้งหมด</span>
                            </a>
                            @foreach($types as $type)
                            <a href="{{ route('catalog', ['type_id' => $type->id, 'search' => request('search')]) }}"
                                class="block px-3 py-2 rounded-lg text-sm transition flex justify-between items-center {{ request('type_id') == $type->id ? 'bg-brand-50 text-brand-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                                <span>{{ $type->name }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- 🛍️ PRODUCT GRID (ส่วนแสดงสินค้า) --}}
                <div class="lg:col-span-3">
                    {{-- ผลลัพธ์การค้นหา --}}
                    @if(request('search'))
                    <div class="mb-4 text-sm text-gray-500">
                        ผลการค้นหาสำหรับ "<strong>{{ request('search') }}</strong>" พบ {{ $items->total() }} รายการ
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($items as $item)
                        @php
                        // 1. หารูปภาพหลัก
                        $mainImg = $item->images->where('is_main', true)->first() ?? $item->images->first();

                        // 2. กำหนดรูป Default
                        $imagePath = 'https://via.placeholder.com/400x533?text=No+Image';

                        // 3. เช็ค Logic รูปภาพ (Cloudinary vs Local)
                        if ($mainImg && $mainImg->path) {
                        if (Str::startsWith($mainImg->path, 'http')) {
                        // ถ้าเป็นลิงก์ Cloudinary (ขึ้นต้นด้วย http) ใช้ได้เลย
                        $imagePath = $mainImg->path;
                        } else {
                        // ถ้าเป็น Local Storage
                        $imagePath = asset('storage/' . str_replace('public/', '', $mainImg->path));
                        }
                        }
                        @endphp

                        <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 cursor-pointer"
                            {{-- 
                                    4. แก้ไขส่วน Alpine.js ตรงนี้ 
                                    - ใช้ฟังก์ชัน imageUrl() ที่เราเพิ่มไปใน Layout 
                                --}}
                            @click="
                                    selectedItem = {{ Js::from($item) }}; 
                                    let firstImg = selectedItem.images.find(i => i.is_main) || selectedItem.images[0];
                                    activeImage = firstImg ? imageUrl(firstImg.path) : 'https://via.placeholder.com/400x533?text=No+Image';
                                    itemModalOpen = true;
                                ">

                            <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
                                {{-- รูปภาพสินค้า --}}
                                <img src="{{ $imagePath }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110" loading="lazy" decoding="async">

                                {{-- Badge Status --}}
                                @if($item->stock > 0)
                                <span class="absolute top-3 right-3 bg-green-500/90 text-white text-[10px] font-bold px-2 py-1 rounded-md backdrop-blur-sm shadow-sm">ว่าง</span>
                                @else
                                <span class="absolute top-3 right-3 bg-red-500/90 text-white text-[10px] font-bold px-2 py-1 rounded-md backdrop-blur-sm shadow-sm">คิวเต็ม</span>
                                @endif

                                {{-- Badge Type (โชว์ประเภทสินค้า) --}}
                                <span class="absolute bottom-3 left-3 bg-black/50 text-white text-[10px] px-2 py-1 rounded-full backdrop-blur-sm border border-white/20">
                                    {{ $item->type->name ?? 'ทั่วไป' }}
                                </span>
                            </div>

                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 group-hover:text-brand-600 transition truncate">{{ $item->item_name }}</h3>
                                <div class="flex justify-between items-center mt-2">
                                    <p class="text-brand-600 font-bold text-lg">{{ number_format($item->price) }}</p>
                                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                                        {{ $item->unit->name ?? 'ชิ้น' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-gray-300">
                            <div class="inline-block p-4 bg-gray-50 rounded-full mb-3">
                                <i data-lucide="search-x" class="w-10 h-10 text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">ไม่พบสินค้า</h3>
                            <p class="text-gray-500 mt-2">ลองเปลี่ยนคำค้นหา หรือเลือกหมวดหมู่อื่น</p>
                            <a href="{{ route('catalog') }}" class="mt-4 inline-block px-6 py-2 bg-brand-600 text-white font-bold rounded-full hover:bg-brand-700 transition">
                                ดูสินค้าทั้งหมด
                            </a>
                        </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-12">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.frontend>