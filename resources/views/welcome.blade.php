<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Watakacha Rental') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Prompt', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            500: '#ec4899',
                            600: '#db2777',
                            900: '#831843'
                        }
                    }
                }
            }
        }
    </script>
    @endif

    <style>
        [x-cloak] {
            display: none !important;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased" x-data="{ 
    scrolled: false,
    mobileMenuOpen: false,
    searchOpen: false,
    itemModalOpen: false,
    selectedItem: null,
    activeImage: ''
}" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <nav class="fixed w-full z-40 transition-all duration-300"
        :class="scrolled ? 'bg-white/90 backdrop-blur-md shadow-md py-3' : 'bg-transparent py-5'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">

                {{-- ✅ LOGO ร้าน --}}
                <div class="flex items-center gap-2">
                    {{--
                        วิธีใช้: เอารูปโลโก้ชื่อ 'logo.png' ไปวางไว้ที่ folder 'public/images/' 
                        ถ้าไม่มีรูป ระบบจะแสดง Icon แทนครับ
                    --}}
                    <a href="#" class="flex items-center gap-2">
                        @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Watakacha Logo" class="h-10 w-auto">
                        @else
                        {{-- Fallback Logo (ถ้ายังไม่มีไฟล์รูป) --}}
                        <div class="bg-gradient-to-tr from-brand-500 to-purple-600 text-white p-2 rounded-lg shadow-lg">
                            <i data-lucide="gem" class="w-6 h-6"></i>
                        </div>
                        @endif

                        <span class="text-2xl font-bold tracking-tight" :class="scrolled ? 'text-gray-900' : 'text-gray-900 lg:text-white'">
                            Watakacha
                        </span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('welcome') }}" class="font-medium hover:text-brand-500 transition" ...>หน้าแรก</a>
                    {{-- ✅ ลิงก์ไปหน้า Catalog --}}
                    <a href="{{ route('catalog') }}" class="font-medium hover:text-brand-500 transition" ...>ชุดทั้งหมด</a>
                    {{-- ✅ ลิงก์ไปหน้า Promotions --}}
                    <a href="{{ route('promotions') }}" class="font-medium hover:text-brand-500 transition" ...>โปรโมชั่น</a>
                    {{-- ✅ ลิงก์ไปหน้า Contact --}}
                    <a href="{{ route('contact') }}" class="font-medium hover:text-brand-500 transition" ...>ติดต่อเรา</a>
                </div>

                <div class="hidden md:flex items-center gap-3">
                    {{-- 🔴 กรณี: ยังไม่ได้ล็อกอิน (Guest) --}}
                    @guest('member') {{-- ✅ ระบุ guard member --}}
                    <a href="{{ route('member.login') }}" class="px-4 py-2 rounded-full text-sm font-bold transition border"
                        :class="scrolled ? 'border-brand-500 text-brand-600 hover:bg-brand-50' : 'border-white text-white hover:bg-white/20'">
                        เข้าสู่ระบบ
                    </a>
                    <a href="{{ route('member.register') }}" class="px-4 py-2 rounded-full text-sm font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lg transition transform hover:-translate-y-0.5">
                        สมัครสมาชิก
                    </a>
                    @endguest

                    {{-- 🟢 กรณี: ล็อกอินแล้ว (Member) --}}
                    @auth('member') {{-- ✅ ระบุ guard member --}}
                    <div class="relative ml-3" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" type="button"
                                class="flex items-center gap-3 bg-white/90 backdrop-blur rounded-full p-1 pr-4 border border-gray-200 shadow-sm hover:shadow-md transition">

                                {{-- Avatar --}}
                                <img class="h-9 w-9 rounded-full object-cover border-2 border-brand-100"
                                    {{-- ✅ ดึงชื่อจาก guard member --}}
                                    src="https://ui-avatars.com/api/?name={{ Auth::guard('member')->user()->first_name }}+{{ Auth::guard('member')->user()->last_name }}&background=ec4899&color=fff"
                                    alt="Profile">

                                <div class="flex flex-col items-start text-left">
                                    {{-- ชื่อ --}}
                                    <span class="text-xs font-bold text-gray-800 leading-tight">
                                        คุณ{{ Auth::guard('member')->user()->first_name }}
                                    </span>
                                    {{-- แต้ม --}}
                                    <span class="text-[10px] font-bold text-yellow-600 flex items-center gap-1 bg-yellow-50 px-1.5 rounded-full mt-0.5">
                                        <i data-lucide="coins" class="w-3 h-3"></i>
                                        {{ number_format(Auth::guard('member')->user()->points ?? 0) }} แต้ม
                                    </span>
                                </div>

                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                            </button>
                        </div>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" @click.away="open = false" x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 py-1 z-50 divide-y divide-gray-100">

                            <div class="px-4 py-3">
                                <p class="text-xs text-gray-500">บัญชีผู้ใช้</p>
                                <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::guard('member')->user()->username }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ Auth::guard('member')->user()->email }}</p>
                            </div>

                            <div class="py-1">
                                <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-700">
                                    <i data-lucide="user" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-brand-500"></i>
                                    ข้อมูลส่วนตัว
                                </a>
                                <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-700">
                                    <i data-lucide="history" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-brand-500"></i>
                                    ประวัติการเช่า
                                </a>
                            </div>

                            <div class="py-1">
                                {{-- ✅ Logout Route --}}
                                <form method="POST" action="{{ route('member.logout') }}">
                                    @csrf
                                    <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i data-lucide="log-out" class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500"></i>
                                        ออกจากระบบ
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-md" :class="scrolled ? 'text-gray-800' : 'text-white'">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </nav>

    <div x-show="mobileMenuOpen" x-transition class="fixed inset-0 z-50 bg-white p-6 md:hidden">
        <div class="flex justify-between items-center mb-8">
            <span class="text-xl font-bold text-gray-900">เมนู</span>
            <button @click="mobileMenuOpen = false"><i data-lucide="x" class="w-6 h-6 text-gray-500"></i></button>
        </div>
        <div class="flex flex-col space-y-4 text-lg">

            {{-- ส่วนแสดงผลสมาชิกในมือถือ --}}
            @auth('member') {{-- ✅ ระบุ guard member --}}
            <div class="bg-brand-50 p-4 rounded-xl flex items-center gap-3 mb-2">
                <img class="h-10 w-10 rounded-full border border-white shadow-sm"
                    src="https://ui-avatars.com/api/?name={{ Auth::guard('member')->user()->first_name }}&background=ec4899&color=fff">
                <div>
                    <p class="font-bold text-gray-900">คุณ{{ Auth::guard('member')->user()->first_name }}</p>
                    <p class="text-xs text-brand-600 font-bold bg-white px-2 py-0.5 rounded-full inline-block shadow-sm">
                        ⭐ {{ number_format(Auth::guard('member')->user()->points ?? 0) }} แต้ม
                    </p>
                </div>
            </div>
            <a href="#" class="text-gray-800 font-medium ml-2">จัดการข้อมูลส่วนตัว</a>
            <a href="#" class="text-gray-800 font-medium ml-2">ประวัติการเช่า</a>

            <form method="POST" action="{{ route('member.logout') }}">
                @csrf
                <button class="text-red-600 font-bold ml-2">ออกจากระบบ</button>
            </form>
            <hr class="my-2">
            @endauth

            <a href="#" class="text-gray-800 font-medium">หน้าแรก</a>
            <a href="#catalog" class="text-gray-800 font-medium">ชุดทั้งหมด</a>
            <a href="#promotions" class="text-gray-800 font-medium">โปรโมชั่น</a>

            @guest('member') {{-- ✅ ระบุ guard member --}}
            <hr>
            <a href="{{ route('member.login') }}" class="text-brand-600 font-bold">เข้าสู่ระบบ (สมาชิก)</a>
            <a href="{{ route('member.register') }}" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-center shadow">สมัครสมาชิกใหม่</a>
            @endguest
        </div>
    </div>

    <div class="relative h-[600px] lg:h-[700px] w-full overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/banner.png') }}" class="w-full h-full object-cover" alt="Banner">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 h-full flex items-center">
            <div class="max-w-2xl text-white space-y-6">
                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold tracking-wider uppercase border border-white/30">
                    Watakacha Rental Service
                </span>
                <h1 class="text-4xl lg:text-6xl font-bold leading-tight">
                    เนรมิตวันพิเศษของคุณ <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-300 to-purple-300">ด้วยชุดที่ใช่ ในสไตล์คุณ</span>
                </h1>
                <p class="text-lg text-gray-200">
                    บริการเช่าชุดราตรี ชุดไทย สูท และอุปกรณ์เสริมครบวงจร พร้อมบริการแต่งหน้าและถ่ายภาพ จบครบในที่เดียว
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="#catalog" class="px-8 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-full shadow-lg shadow-brand-500/30 transition transform hover:-translate-y-1 text-center">
                        เลือกชมชุด
                    </a>
                    <a href="https://line.me/ti/p/@yourlineid" target="_blank" class="px-8 py-3 bg-white hover:bg-gray-100 text-gray-900 font-bold rounded-full shadow-lg transition flex items-center justify-center gap-2">
                        <i data-lucide="message-circle" class="w-5 h-5 text-green-600"></i> สอบถามผ่าน LINE
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="catalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">คอลเลคชันแนะนำ</h2>
            <div class="w-24 h-1 bg-brand-500 mx-auto rounded-full"></div>
            <p class="mt-4 text-gray-500">คัดสรรชุดคุณภาพดี ดีไซน์ทันสมัย เพื่อคุณโดยเฉพาะ</p>
        </div>


        {{-- Grid Items --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Loop Items --}}
            @if(isset($items) && count($items) > 0)
            @foreach($items as $item)
            @php
            // ✅ Logic หา Path รูปภาพ (เหมือนหน้า Maintenance)
            $imagePath = null;
            // หาภาพหลักก่อน
            $mainImg = $item->images->where('is_main', true)->first();
            // ถ้าไม่มีภาพหลัก เอาภาพแรก
            if (!$mainImg) {
            $mainImg = $item->images->first();
            }

            if ($mainImg && $mainImg->path) {
            // ลบ public/ ออกจาก path (ถ้ามี) แล้วสร้าง URL
            $cleanPath = str_replace('public/', '', $mainImg->path);
            $imagePath = asset('storage/' . $cleanPath);
            } else {
            // Placeholder ถ้าไม่มีรูป
            $imagePath = 'https://via.placeholder.com/400x533?text=No+Image';
            }
            @endphp

            {{-- Card --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 cursor-pointer"
                @click="selectedItem = {{ Js::from($item) }}; 
                        // ✅ Alpine Logic: เซ็ตภาพแรกให้ Modal
                        let firstImg = selectedItem.images.find(i => i.is_main) || selectedItem.images[0];
                        if(firstImg) {
                            activeImage = '{{ asset('storage') }}/' + firstImg.path.replace('public/', '');
                        } else {
                            activeImage = 'https://via.placeholder.com/400x533?text=No+Image';
                        }
                        itemModalOpen = true">

                <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
                    <img src="{{ $imagePath }}"
                        alt="{{ $item->item_name }}"
                        class="w-full h-full object-cover transition duration-700 group-hover:scale-110"
                        loading="lazy"
                        decoding="async"
                        onerror="this.onerror=null; this.src='https://via.placeholder.com/400x533?text=No+Image';">

                    {{-- Badge Status --}}
                    @if($item->stock > 0)
                    <span class="absolute top-3 right-3 bg-green-500/90 text-white text-[10px] font-bold px-2 py-1 rounded-md backdrop-blur-sm">
                        ว่าง
                    </span>
                    @else
                    <span class="absolute top-3 right-3 bg-red-500/90 text-white text-[10px] font-bold px-2 py-1 rounded-md backdrop-blur-sm">
                        คิวเต็ม
                    </span>
                    @endif
                </div>

                <div class="p-4">
                    <h3 class="font-bold text-gray-900 group-hover:text-brand-600 transition truncate">{{ $item->item_name }}</h3>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-brand-600 font-bold">฿{{ number_format($item->price) }}</p>
                        <span class="text-xs text-gray-400">เช่า 7 วัน</span>
                    </div>
                </div>
            </div>
            @endforeach
            @else
            {{-- Mockup Data (กรณีไม่มีข้อมูลจริง) --}}
            <div class="col-span-full text-center py-10 text-gray-500">
                ไม่พบข้อมูลสินค้า
            </div>
            @endif
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('catalog') }}" class="inline-block px-8 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-full hover:bg-gray-50 transition shadow-sm">
                ดูทั้งหมด
            </a>
        </div>
    </div>

    <footer id="contact" class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 border-b border-gray-800 pb-12">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" class="h-8 w-auto brightness-0 invert"> {{-- Logo ขาว --}}
                        @else
                        <i data-lucide="gem" class="w-6 h-6 text-brand-500"></i>
                        @endif
                        <span class="text-xl font-bold">Watakacha</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        ร้านเช่าชุดที่ใส่ใจทุกรายละเอียด คัดสรรชุดสวยคุณภาพดี เพื่อให้คุณมั่นใจที่สุดในวันสำคัญ
                    </p>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">ติดต่อเรา</h3>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li class="flex items-center gap-2"><i data-lucide="map-pin" class="w-4 h-4"></i> ต.ต้นธง อ.เมือง จ.ลำพูน</li>
                        <li class="flex items-center gap-2"><i data-lucide="phone" class="w-4 h-4"></i> 093-130-9899</li>
                        <li class="flex items-center gap-2"><i data-lucide="clock" class="w-4 h-4"></i> เปิดทุกวัน 09:00 - 20:00 น.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">ช่องทางออนไลน์</h3>
                    <div class="flex gap-4">
                        <a href="#" class="p-2 bg-gray-800 rounded-full hover:bg-green-600 transition"><i data-lucide="message-circle" class="w-5 h-5"></i></a>
                        <a href="#" class="p-2 bg-gray-800 rounded-full hover:bg-blue-600 transition"><i data-lucide="facebook" class="w-5 h-5"></i></a>
                        <a href="#" class="p-2 bg-gray-800 rounded-full hover:bg-pink-600 transition"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center pt-8 text-gray-600 text-xs">
                &copy; {{ date('Y') }} Watakacha Rental. All rights reserved.
            </div>
        </div>
    </footer>

    <div x-show="itemModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div x-show="itemModalOpen"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity"
            @click="itemModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="itemModalOpen"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">

                <button @click="itemModalOpen = false" class="absolute top-4 right-4 z-10 p-2 bg-white/50 hover:bg-white rounded-full transition">
                    <i data-lucide="x" class="w-6 h-6 text-gray-600"></i>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="bg-gray-100 p-4 flex flex-col justify-center">
                        <div class="aspect-[3/4] w-full rounded-xl overflow-hidden shadow-sm mb-4">
                            <img :src="activeImage" class="w-full h-full object-cover" alt="Main Image"
                                onerror="this.src='https://via.placeholder.com/400x533?text=No+Image'">
                        </div>

                        <div class="flex gap-2 overflow-x-auto scrollbar-hide py-2">
                            <template x-if="selectedItem?.images">
                                <template x-for="img in selectedItem.images" :key="img.id">
                                    {{-- ✅ Alpine Logic: เปลี่ยนจาก img.image_path เป็น img.path --}}
                                    <div @click="activeImage = '{{ asset('storage') }}/' + img.path.replace('public/', '')"
                                        class="w-16 h-20 shrink-0 rounded-lg overflow-hidden cursor-pointer border-2 transition"
                                        :class="activeImage.includes(img.path.replace('public/', '')) ? 'border-brand-500' : 'border-transparent'">
                                        <img :src="'{{ asset('storage') }}/' + img.path.replace('public/', '')" class="w-full h-full object-cover">
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>

                    <div class="p-8 flex flex-col h-full">
                        <div class="flex-grow">
                            <span class="bg-brand-100 text-brand-700 text-xs font-bold px-2 py-1 rounded uppercase tracking-wide">New Arrival</span>
                            <h2 class="text-2xl font-bold text-gray-900 mt-2" x-text="selectedItem?.item_name"></h2>
                            <p class="text-3xl font-bold text-brand-600 mt-4">
                                ฿<span x-text="new Intl.NumberFormat().format(selectedItem?.price)"></span>
                            </p>

                            <hr class="my-6 border-gray-100">

                            <h4 class="font-bold text-gray-900 mb-2">รายละเอียดชุด</h4>
                            <p class="text-gray-600 leading-relaxed text-sm" x-text="selectedItem?.description || 'ไม่มีรายละเอียดเพิ่มเติม'"></p>

                            <div class="mt-6 space-y-2">
                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i> บริการซักแห้งฟรี
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i> ปรับแก้ทรงฟรี 1 ครั้ง (ชั่วคราว)
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                    <i data-lucide="clock" class="w-4 h-4 text-brand-500"></i> ระยะเวลาเช่า 7 วัน
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <button class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg shadow-brand-500/30 transition flex justify-center items-center gap-2">
                                <i data-lucide="message-circle" class="w-5 h-5"></i>
                                สนใจเช่าชุดนี้ (ทัก LINE)
                            </button>
                            <p class="text-center text-xs text-gray-400 mt-3">แคปหน้าจอชุดนี้ส่งให้แอดมินทาง LINE ได้เลยค่ะ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>