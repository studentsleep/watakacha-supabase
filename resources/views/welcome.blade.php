<x-layouts.frontend>
    {{-- Banner Section --}}
    <div class="relative h-[600px] lg:h-[700px] w-full overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/banner.png') }}" class="w-full h-full object-cover" alt="Banner" loading="lazy" decoding="async">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 h-full flex items-center">
            <div class="max-w-2xl text-white space-y-6">
                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold tracking-wider uppercase border border-white/30">
                    Watakacha Wedding & Studio
                </span>
                <h1 class="text-4xl lg:text-6xl font-bold leading-tight">
                    เนรมิตวันพิเศษของคุณ <br> </h1>
                <p class="text-lg text-gray-200">
                    บริการเช่าชุดราตรี ชุดไทย สูท และอุปกรณ์เสริมครบวงจร พร้อมบริการแต่งหน้าและถ่ายภาพ จบครบในที่เดียว
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="https://line.me/ti/p/@yourlineid" target="_blank" class="px-8 py-3 bg-white hover:bg-green-500 text-gray-900 font-bold rounded-full shadow-lg transition flex items-center justify-center gap-2">
                        <i data-lucide="message-circle" class="w-5 h-5 text-green-600"></i> สอบถามผ่าน LINE
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Catalog Preview --}}
    <div id="catalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">คอลเลคชันแนะนำ</h2>
            <div class="w-24 h-1 bg-brand-500 mx-auto rounded-full"></div>
        </div>

        {{-- Grid Items (เหมือนหน้า Catalog เป๊ะ) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($items as $item)
            @php
            $imagePath = 'https://via.placeholder.com/400x533?text=No+Image';
            $mainImg = $item->images->where('is_main', true)->first() ?? $item->images->first();
            if ($mainImg && $mainImg->path) { $imagePath = asset('storage/' . str_replace('public/', '', $mainImg->path)); }
            @endphp
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 cursor-pointer"
                @click="selectedItem = {{ Js::from($item) }}; 
                        let firstImg = selectedItem.images.find(i => i.is_main) || selectedItem.images[0];
                        activeImage = firstImg ? '{{ asset('storage') }}/' + firstImg.path.replace('public/', '') : 'https://via.placeholder.com/400x533?text=No+Image';
                        itemModalOpen = true">
                <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
                    <img src="{{ $imagePath }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    @if($item->stock > 0) <span class="absolute top-3 right-3 bg-green-500/90 text-white text-[10px] font-bold px-2 py-1 rounded-md">ว่าง</span> @else <span class="absolute top-3 right-3 bg-red-500/90 text-white text-[10px] font-bold px-2 py-1 rounded-md">คิวเต็ม</span> @endif
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 group-hover:text-brand-600 transition truncate">{{ $item->item_name }}</h3>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-brand-600 font-bold">฿{{ number_format($item->price) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('catalog') }}" class="inline-block px-8 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-full hover:bg-gray-50 transition shadow-sm">ดูทั้งหมด</a>
        </div>
    </div>
</x-layouts.frontend>