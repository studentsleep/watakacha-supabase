<style>
    .gold-glitter-text {
        /* 1. สร้างสีทองแบบไล่เฉด (มีสีขาวแทรกเพื่อให้ดูวิบวับ) */
        background: linear-gradient(to right,
                #BF953F,
                #FCF6BA,
                #B38728,
                #FBF5B7,
                #AA771C);
        background-size: 200% auto;

        /* 2. ตัดพื้นหลังให้เป็นรูปตัวอักษร */
        color: transparent;
        background-clip: text;
        -webkit-background-clip: text;

        /* 3. อนิเมชั่นขยับพื้นหลัง (สร้างความรู้สึกระยิบระยับ) */
        animation: shine 8s linear infinite;

        /* 4. แสงโกลว์ (Glow) สว่างๆ */
        text-shadow: 0 0 10px rgba(253, 246, 186, 0.5),
            0 0 20px rgba(191, 149, 63, 0.3);
    }

    @keyframes shine {
        to {
            background-position: 200% center;
        }
    }
</style>
<x-layouts.frontend>
    {{-- ========================================== --}}
    {{-- 1. BANNER SECTION (ภาพใหญ่ เต็มตา)       --}}
    {{-- ========================================== --}}
    <div class="relative h-[600px] lg:h-[700px] w-full overflow-hidden group">
        {{-- Background Image --}}
        <div class="absolute inset-0">
            <img src="{{ asset('images/banner.png') }}"
                class="w-full h-full object-cover transition-transform duration-[3000ms] group-hover:scale-105"
                alt="Banner"
                loading="eager"
                decoding="async">

            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/10"></div>
        </div>

        {{-- Contact Channels (Overlay ลอยอยู่ด้านล่างของภาพ) --}}
        <div class="absolute bottom-0 left-0 w-full z-10 pb-8">
            <div class="max-w-7xl mx-auto px-4 flex flex-col items-center justify-center">

                {{-- เส้นตกแต่ง --}}
                <div class="w-16 h-1 bg-white/50 rounded-full mb-6 backdrop-blur-sm"></div>

                <p class="text-white/90 text-sm font-light tracking-[0.25em] mb-6 uppercase text-shadow-sm">
                    Follow Us & Contact
                </p>

                {{-- Social Icons --}}
                <div class="flex items-center gap-4 sm:gap-6">

                    {{-- TikTok --}}
                    <a href="https://www.tiktok.com/@watakachastudio" target="_blank"
                        class="group/icon relative w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white/10 backdrop-blur-md border border-white/30 flex items-center justify-center text-white hover:bg-[#000000] hover:border-[#000000] hover:scale-110 transition-all duration-300 shadow-xl">
                        {{-- ใช้ SVG เพื่อความชัวร์ (รูปตัวโน้ตดนตรี) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 sm:w-6 sm:h-6">
                            <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" />
                        </svg>
                    </a>
                    {{-- Facebook --}}
                    <a href="https://www.facebook.com/WATAKACHA/" target="_blank"
                        class="group/icon relative w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white/10 backdrop-blur-md border border-white/30 flex items-center justify-center text-white hover:bg-[#1877F2] hover:border-[#1877F2] hover:scale-110 transition-all duration-300 shadow-xl">
                        <i data-lucide="facebook" class="w-5 h-5 sm:w-6 sm:h-6 group-hover/icon:fill-white"></i>
                    </a>

                    {{-- Line --}}
                    <a href="https://line.me/ti/p/@699mhyzz" target="_blank"
                        class="group/icon relative w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white/10 backdrop-blur-md border border-white/30 flex items-center justify-center text-white hover:bg-[#06C755] hover:border-[#06C755] hover:scale-110 transition-all duration-300 shadow-xl">
                        <i data-lucide="message-circle" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </a>

                    {{-- Instagram --}}
                    <a href="https://www.instagram.com/watakacha_wedding_studio/" target="_blank"
                        class="group/icon relative w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white/10 backdrop-blur-md border border-white/30 flex items-center justify-center text-white hover:bg-pink-600 hover:border-pink-600 hover:scale-110 transition-all duration-300 shadow-xl">
                        <i data-lucide="instagram" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </a>
                </div>

                {{-- ✅ Scroll Down Indicator (ส่วนที่เพิ่มใหม่) --}}
                <div class="mt-8 sm:mt-12 animate-bounce">
                    <a href="#content-start"
                        class="flex flex-col items-center gap-2 text-white/70 hover:text-white transition-colors duration-300 cursor-pointer group/scroll">
                        <span class="text-[10px] uppercase tracking-widest font-light opacity-0 group-hover/scroll:opacity-100 transition-opacity">Scroll</span>
                        <div class="w-10 h-10 rounded-full border border-white/30 flex items-center justify-center bg-white/5 backdrop-blur-sm group-hover/scroll:bg-white/20 transition-all">
                            <i data-lucide="chevron-down" class="w-5 h-5"></i>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 2. HERO CONTENT SECTION (ข้อความแนะนำ)   --}}
    {{-- ========================================== --}}
    <div id="content-start" class="bg-white relative"> {{-- ✅ เพิ่ม id="content-start" ตรงนี้ --}}
        {{-- Decorative Background Elements --}}
        <div class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 translate-x-1/3 translate-y-1/3 bg-purple-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 py-20 lg:py-32 text-center relative z-10">
            {{-- Badge --}}
            <span class="inline-block px-5 py-2 bg-gray-50 text-gray-900 border border-gray-200 rounded-full text-xs font-bold tracking-[0.15em] uppercase mb-8 shadow-sm">
                Watakacha Wedding & Studio
            </span>

            {{-- Title --}}
            <h1 class="gold-glitter-text text-4xl md:text-5xl lg:text-7xl font-bold leading-tight mb-8">
                เนรมิตวันพิเศษของคุณ <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-purple-600 relative">
                    {{-- ขีดเส้นใต้ตกแต่ง --}}
                    <svg class="absolute w-full h-3 -bottom-1 left-0 text-brand-200 -z-10" viewBox="0 0 100 10" preserveAspectRatio="none">
                        <path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="8" fill="none" />
                    </svg>
                </span>
            </h1>

            {{-- Description --}}
            <p class="text-lg md:text-xl text-gray-600 leading-relaxed mb-12 max-w-3xl mx-auto font-light">
                บริการเช่าชุดราตรี ชุดไทย สูท และอุปกรณ์เสริมครบวงจร พร้อมบริการแต่งหน้าและถ่ายภาพ
                ดูแลคุณด้วยความใส่ใจและเป็นกันเอง เพื่อให้คุณมั่นใจที่สุดในวันสำคัญ
            </p>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-5 justify-center">
                <a href="#catalog"
                    class="px-10 py-4 bg-gray-900 text-white font-bold rounded-full shadow-xl hover:bg-black hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3 text-lg">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    เลือกชมชุด
                </a>
                <a href="https://line.me/ti/p/@699mhyzz" target="_blank"
                    class="px-10 py-4 bg-white text-gray-900 border-2 border-gray-100 font-bold rounded-full shadow-md hover:border-gray-300 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3 text-lg">
                    <i data-lucide="message-circle" class="w-5 h-5 text-green-600"></i>
                    สอบถามผ่าน LINE
                </a>
            </div>
        </div>
    </div>

    {{-- Catalog Preview --}}
    <div id="catalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">คอลเลคชันแนะนำ</h2>
            <div class="w-24 h-1 bg-brand-500 mx-auto rounded-full"></div>
        </div>

        {{-- Grid Items --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($items as $item)
            @php
            // 1. หารูปภาพหลัก
            $mainImg = $item->images->where('is_main', true)->first() ?? $item->images->first();

            // 2. กำหนดรูป Default
            $imagePath = 'https://via.placeholder.com/400x533?text=No+Image';

            // 3. เช็ค Logic รูปภาพ (Cloudinary vs Local)
            if ($mainImg && $mainImg->path) {
            if (str_starts_with($mainImg->path, 'http')) {
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
                        - ใช้ฟังก์ชัน imageUrl() ที่เราเพิ่งเพิ่มไปใน Layout 
                    --}}
                @click="
                        selectedItem = {{ Js::from($item) }}; 
                        let firstImg = selectedItem.images.find(i => i.is_main) || selectedItem.images[0];
                        activeImage = firstImg ? imageUrl(firstImg.path) : 'https://via.placeholder.com/400x533?text=No+Image';
                        itemModalOpen = true;
                    ">

                <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
                    {{-- รูปภาพสินค้า --}}
                    <img src="{{ $imagePath }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110" alt="{{ $item->item_name }}" loading="lazy">

                    {{-- สถานะ Stock --}}
                    @if($item->stock > 0)
                    <span class="absolute top-3 right-3 bg-green-500/90 text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-sm">ว่าง</span>
                    @else
                    <span class="absolute top-3 right-3 bg-red-500/90 text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-sm">คิวเต็ม</span>
                    @endif
                </div>

                <div class="p-4">
                    <h3 class="font-bold text-gray-900 group-hover:text-brand-600 transition truncate">{{ $item->item_name }}</h3>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-brand-600 font-bold">{{ number_format($item->price) }}</p>
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