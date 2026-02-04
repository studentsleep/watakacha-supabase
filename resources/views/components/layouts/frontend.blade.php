<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Watakacha Rental') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Icons & Scripts --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Styles --}}
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

<body class="bg-gray-50 text-gray-800 font-sans antialiased"
    x-data="{ 
        scrolled: false, 
        mobileMenuOpen: false, 
        searchOpen: false,
        itemModalOpen: false,
        selectedItem: null,
        activeImage: ''
    }"
    @scroll.window="scrolled = (window.pageYOffset > 20)">

    {{-- ====================================================================== --}}
    {{-- 🟢 1. NAVBAR --}}
    {{-- ====================================================================== --}}
    <nav class="fixed w-full z-40 transition-all duration-300"
        :class="scrolled ? 'bg-white/90 backdrop-blur-md shadow-md py-3' : 'bg-transparent py-5'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">

                {{-- 💎 LOGO --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('welcome') }}" class="flex items-center gap-2">
                        @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Watakacha Logo" class="h-10 w-auto">
                        @else
                        <div class="bg-gradient-to-tr from-brand-500 to-purple-600 text-white p-2 rounded-lg shadow-lg">
                            <i data-lucide="gem" class="w-6 h-6"></i>
                        </div>
                        @endif
                        <span class="text-2xl font-bold tracking-tight" :class="scrolled ? 'text-gray-900' : 'text-gray-900 lg:text-white'">
                            Watakacha
                        </span>
                    </a>
                </div>

                {{-- 🖥️ Desktop Menu --}}
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('welcome') }}" class="font-medium hover:text-yellow-500 transition" :class="scrolled ? 'text-gray-600' : 'text-white drop-shadow-md'">หน้าแรก</a>
                    <a href="{{ route('catalog') }}" class="font-medium hover:text-yellow-500 transition" :class="scrolled ? 'text-gray-600' : 'text-white drop-shadow-md'">ชุดทั้งหมด</a>
                    <a href="{{ route('promotions') }}" class="font-medium hover:text-yellow-500 transition" :class="scrolled ? 'text-gray-600' : 'text-white drop-shadow-md'">โปรโมชั่น</a>
                    <a href="{{ route('contact') }}" class="font-medium hover:text-yellow-500 transition" :class="scrolled ? 'text-gray-600' : 'text-white drop-shadow-md'">ติดต่อเรา</a>
                </div>

                {{-- 🔐 Auth Buttons --}}
                <div class="hidden md:flex items-center gap-3">
                    @guest('member')
                    <a href="{{ route('member.login') }}"
                        class="px-4 py-2 rounded-full text-sm font-bold transition border"
                        :class="scrolled 
                                ? 'border-brand-600 text-brand-600 hover:bg-brand-600 hover:bg-white hover:text-yellow-600' 
                                : 'border-white text-white hover:bg-white hover:bg-white hover:text-yellow-600'">
                        เข้าสู่ระบบ
                    </a>

                    <a href="{{ route('member.register') }}"
                        class="px-4 py-2 rounded-full text-sm font-bold shadow-lg transition transform hover:-translate-y-0.5 border"
                        :class="scrolled 
                                ? 'bg-brand-600 border-brand-600 text-brand-600 hover:bg-white hover:bg-white hover:text-yellow-600' 
                                : 'bg-brand-400 border-brand-400 text-white hover:bg-white hover:bg-white hover:text-yellow-600'">
                        สมัครสมาชิก
                    </a>
                    @endguest

                    @auth('member')
                    <div class="relative ml-3" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="flex items-center gap-3 bg-white/90 backdrop-blur rounded-full p-1 pr-4 border border-gray-200 shadow-sm hover:shadow-md transition">
                            <img class="h-9 w-9 rounded-full object-cover border-2 border-brand-100"
                                src="https://ui-avatars.com/api/?name={{ Auth::guard('member')->user()->first_name }}+{{ Auth::guard('member')->user()->last_name }}&background=ec4899&color=fff">
                            <div class="flex flex-col items-start text-left">
                                <span class="text-xs font-bold text-gray-800 leading-tight">คุณ{{ Auth::guard('member')->user()->first_name }}</span>
                                <span class="text-[10px] font-bold text-yellow-600 flex items-center gap-1 bg-yellow-50 px-1.5 rounded-full mt-0.5">
                                    ⭐{{ number_format(Auth::guard('member')->user()->points ?? 0) }} แต้ม
                                </span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 py-1 z-50 divide-y divide-gray-100">
                            <div class="px-4 py-3">
                                <p class="text-xs text-gray-500">บัญชีผู้ใช้</p>
                                <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::guard('member')->user()->username }}</p>
                            </div>
                            <div class="py-1">
                                <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-700"><i data-lucide="user" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-brand-500"></i> ข้อมูลส่วนตัว</a>
                                <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-700"><i data-lucide="history" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-brand-500"></i> ประวัติการเช่า</a>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('member.logout') }}">
                                    @csrf
                                    <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i data-lucide="log-out" class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500"></i> ออกจากระบบ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>

                {{-- 📱 Mobile Toggle --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-md" :class="scrolled ? 'text-gray-800' : 'text-white'">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </nav>

    {{-- 📱 Mobile Menu Overlay --}}
    <div x-show="mobileMenuOpen" x-transition class="fixed inset-0 z-50 bg-white p-6 md:hidden">
        <div class="flex justify-between items-center mb-8">
            <span class="text-xl font-bold text-gray-900">เมนู</span>
            <button @click="mobileMenuOpen = false"><i data-lucide="x" class="w-6 h-6 text-gray-500"></i></button>
        </div>
        <div class="flex flex-col space-y-4 text-lg">
            @auth('member')
            <div class="bg-brand-50 p-4 rounded-xl flex items-center gap-3 mb-2">
                <img class="h-10 w-10 rounded-full border border-white shadow-sm" src="https://ui-avatars.com/api/?name={{ Auth::guard('member')->user()->first_name }}&background=ec4899&color=fff">
                <div>
                    <p class="font-bold text-gray-900">คุณ{{ Auth::guard('member')->user()->first_name }}</p>
                    <p class="text-xs text-brand-600 font-bold">⭐ {{ number_format(Auth::guard('member')->user()->points ?? 0) }} แต้ม</p>
                </div>
            </div>
            <a href="#" class="text-gray-800 font-medium ml-2">ประวัติการเช่า</a>
            <form method="POST" action="{{ route('member.logout') }}">@csrf<button class="text-red-600 font-bold ml-2">ออกจากระบบ</button></form>
            <hr class="my-2">
            @endauth

            <a href="{{ route('welcome') }}" class="text-gray-800 font-medium">หน้าแรก</a>
            <a href="{{ route('catalog') }}" class="text-gray-800 font-medium">ชุดทั้งหมด</a>
            <a href="{{ route('promotions') }}" class="text-gray-800 font-medium">โปรโมชั่น</a>
            <a href="{{ route('contact') }}" class="text-gray-800 font-medium">ติดต่อเรา</a>

            @guest('member')
            <hr>
            <a href="{{ route('member.login') }}" class="text-brand-600 font-bold">เข้าสู่ระบบ</a>
            <a href="{{ route('member.register') }}" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-center shadow">สมัครสมาชิกใหม่</a>
            @endguest
        </div>
    </div>

    {{-- ====================================================================== --}}
    {{-- 🟡 2. CONTENT (เนื้อหาจากแต่ละหน้าจะมาโผล่ตรงนี้) --}}
    {{-- ====================================================================== --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ====================================================================== --}}
    {{-- 🔵 3. FOOTER --}}
    {{-- ====================================================================== --}}
    <footer id="contact" class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 border-b border-gray-800 pb-12">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" class="h-8 w-auto brightness-0 invert">
                        @else
                        <i data-lucide="gem" class="w-6 h-6 text-brand-500"></i>
                        @endif
                        <span class="text-xl font-bold">Watakacha Wedding & Studio</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        ร้านเช่าชุดที่ใส่ใจทุกรายละเอียด คัดสรรชุดสวยคุณภาพดี เพื่อให้คุณมั่นใจที่สุดในวันสำคัญ
                    </p>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">ติดต่อเรา</h3>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li class="flex items-center gap-2"><i data-lucide="map-pin" class="w-4 h-4"></i> ต.ต้นธง อ.เมือง จ.ลำพูน</li>
                        <li class="flex items-center gap-2"><i data-lucide="phone" class="w-4 h-4"></i> 082 280 6989</li>
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

    {{-- ====================================================================== --}}
    {{-- 🟣 4. MODAL (ใส่ไว้ที่นี่ เพื่อให้ใช้ได้ทุกหน้า) --}}
    {{-- ====================================================================== --}}
    <div x-show="itemModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="itemModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="itemModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="itemModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">

                <button @click="itemModalOpen = false" class="absolute top-4 right-4 z-10 p-2 bg-white/50 hover:bg-white rounded-full transition"><i data-lucide="x" class="w-6 h-6 text-gray-600"></i></button>

                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="bg-gray-100 p-4 flex flex-col justify-center">
                        <div class="aspect-[3/4] w-full rounded-xl overflow-hidden shadow-sm mb-4">
                            <img :src="activeImage" class="w-full h-full object-cover" alt="Main Image" onerror="this.src='https://via.placeholder.com/400x533?text=No+Image'">
                        </div>
                        <div class="flex gap-2 overflow-x-auto scrollbar-hide py-2">
                            <template x-if="selectedItem?.images">
                                <template x-for="img in selectedItem.images" :key="img.id">
                                    <div @click="activeImage = '{{ asset('storage') }}/' + img.path.replace('public/', '')" class="w-16 h-20 shrink-0 rounded-lg overflow-hidden cursor-pointer border-2 transition" :class="activeImage.includes(img.path.replace('public/', '')) ? 'border-brand-500' : 'border-transparent'">
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
                            <p class="text-3xl font-bold text-brand-600 mt-4">฿<span x-text="new Intl.NumberFormat().format(selectedItem?.price)"></span></p>
                            <hr class="my-6 border-gray-100">
                            <h4 class="font-bold text-gray-900 mb-2">รายละเอียดชุด</h4>
                            <p class="text-gray-600 leading-relaxed text-sm" x-text="selectedItem?.description || 'ไม่มีรายละเอียดเพิ่มเติม'"></p>
                            <div class="mt-6 space-y-2">
                                <div class="flex items-center gap-3 text-sm text-gray-600"><i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i> บริการซักแห้งฟรี</div>
                                <div class="flex items-center gap-3 text-sm text-gray-600"><i data-lucide="clock" class="w-4 h-4 text-brand-500"></i> ระยะเวลาเช่า 7 วัน</div>
                            </div>
                        </div>
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <button class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-black-500 font-bold rounded-xl shadow-lg shadow-brand-500/30 transition flex justify-center items-center gap-2">
                                <i data-lucide="message-circle" class="w-5 h-5"></i> สนใจเช่าชุดนี้ (ทัก LINE)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Load Icons --}}
    <script>
        lucide.createIcons();
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => {
                if (Alpine.store('itemModalOpen') || true) {
                    setTimeout(() => lucide.createIcons(), 50);
                }
            });
        });
    </script>
</body>

</html>