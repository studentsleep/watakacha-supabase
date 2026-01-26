<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>คอลเลคชันชุดทั้งหมด - {{ config('app.name') }}</title>
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
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased"
    x-data="{ scrolled: true, mobileMenuOpen: false }"
    {{-- forced scrolled=true เพื่อให้เมนูเป็นสีขาวตลอดเวลา --}}>

    {{-- ✅ 1. NAVBAR (ยกมาจากหน้า Welcome เป๊ะๆ) --}}
    <nav class="fixed w-full z-40 transition-all duration-300 bg-white/90 backdrop-blur-md shadow-md py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                {{-- Logo --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('welcome') }}" class="flex items-center gap-2">
                        @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" class="h-10 w-auto">
                        @else
                        <div class="bg-gradient-to-tr from-brand-500 to-purple-600 text-white p-2 rounded-lg shadow-lg">
                            <i data-lucide="gem" class="w-6 h-6"></i>
                        </div>
                        @endif
                        <span class="text-2xl font-bold tracking-tight text-gray-900">Watakacha</span>
                    </a>
                </div>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('welcome') }}" class="font-medium text-gray-600 hover:text-brand-500 transition">หน้าแรก</a>
                    <a href="{{ route('catalog') }}" class="font-bold text-brand-600">ชุดทั้งหมด</a>
                    <a href="{{ route('promotions') }}" class="font-medium text-gray-600 hover:text-brand-500 transition">โปรโมชั่น</a>
                    <a href="{{ route('contact') }}" class="font-medium text-gray-600 hover:text-brand-500 transition">ติดต่อเรา</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="hidden md:flex items-center gap-3">
                    @guest('member')
                    <a href="{{ route('member.login') }}" class="px-4 py-2 rounded-full text-sm font-bold transition border border-brand-500 text-brand-600 hover:bg-brand-50">เข้าสู่ระบบ</a>
                    <a href="{{ route('member.register') }}" class="px-4 py-2 rounded-full text-sm font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lg">สมัครสมาชิก</a>
                    @endguest

                    @auth('member')
                    <div class="relative ml-3" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="flex items-center gap-3 bg-white border border-gray-200 rounded-full p-1 pr-4 shadow-sm">
                            <img class="h-9 w-9 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ Auth::guard('member')->user()->first_name }}&background=ec4899&color=fff">
                            <div class="text-left">
                                <span class="text-xs font-bold block">คุณ{{ Auth::guard('member')->user()->first_name }}</span>
                                <span class="text-[10px] text-yellow-600 font-bold">⭐ {{ number_format(Auth::guard('member')->user()->points ?? 0) }} แต้ม</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50">
                            <form method="POST" action="{{ route('member.logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">ออกจากระบบ</button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>

                {{-- Mobile Toggle --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-800"><i data-lucide="menu" class="w-6 h-6"></i></button>
            </div>
        </div>
    </nav>

    {{-- ✅ 2. CONTENT (ส่วนเนื้อหาหลัก) --}}
    <div class="pt-24 pb-12 min-h-screen"> {{-- pt-24 เว้นระยะให้ Navbar --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header & Search --}}
            <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4 border-b border-gray-200 pb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-8 h-8 text-brand-500"></i>
                        คอลเลคชันชุดทั้งหมด
                    </h1>
                    <p class="text-gray-500 mt-2">เลือกชมชุดราตรี ชุดไทย และสูท คุณภาพพรีเมียมกว่า {{ $items->total() }} รายการ</p>
                </div>

                <form action="{{ route('catalog') }}" method="GET" class="w-full md:w-96 relative group">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="ค้นหาชื่อชุด, สี, หรือสไตล์..."
                        class="w-full pl-11 pr-4 py-3 rounded-full border border-gray-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition shadow-sm bg-white">
                    <div class="absolute left-4 top-3.5 text-gray-400 group-focus-within:text-brand-500 transition">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </div>
                </form>
            </div>

            {{-- Grid Items --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($items as $item)
                @php
                $mainImg = $item->images->where('is_main', true)->first() ?? $item->images->first();
                $imagePath = ($mainImg && $mainImg->path) ? asset('storage/' . str_replace('public/', '', $mainImg->path)) : 'https://via.placeholder.com/400x533?text=No+Image';
                @endphp

                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                    {{-- Image Area --}}
                    <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
                        <img src="{{ $imagePath }}" alt="{{ $item->item_name }}" loading="lazy" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">

                        {{-- Status Badge --}}
                        @if($item->stock > 0)
                        <span class="absolute top-3 right-3 bg-white/90 backdrop-blur text-green-600 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> ว่าง
                        </span>
                        @else
                        <span class="absolute top-3 right-3 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-sm">
                            คิวเต็ม
                        </span>
                        @endif
                    </div>

                    {{-- Text Area --}}
                    <div class="p-5">
                        <h3 class="font-bold text-gray-900 group-hover:text-brand-600 transition truncate text-lg">{{ $item->item_name }}</h3>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $item->description }}</p>

                        <div class="flex justify-between items-end mt-4 pt-3 border-t border-gray-100">
                            <div>
                                <span class="text-xs text-gray-400 block">ราคาเช่า</span>
                                <span class="text-brand-600 font-bold text-xl">฿{{ number_format($item->price) }}</span>
                            </div>
                            <button class="p-2 bg-gray-100 rounded-full text-gray-600 hover:bg-brand-500 hover:text-white transition">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center">
                    <div class="inline-block p-6 rounded-full bg-gray-100 mb-4">
                        <i data-lucide="search-x" class="w-12 h-12 text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">ไม่พบสินค้าที่คุณค้นหา</h3>
                    <p class="text-gray-500 mt-2">ลองใช้คำค้นหาอื่น หรือดูสินค้าทั้งหมด</p>
                    <a href="{{ route('catalog') }}" class="mt-6 inline-block px-6 py-2 bg-brand-600 text-white font-bold rounded-full hover:bg-brand-700 transition">
                        ล้างคำค้นหา
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

    {{-- ✅ 3. FOOTER (ยกมาจากหน้า Welcome) --}}
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h3 class="text-2xl font-bold mb-4">Watakacha Rental</h3>
                <p class="text-gray-400">ร้านเช่าชุดที่ใส่ใจทุกรายละเอียด คัดสรรชุดสวยคุณภาพดี</p>
                <div class="mt-8 pt-8 border-t border-gray-800 text-xs text-gray-500">
                    &copy; {{ date('Y') }} Watakacha Rental. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>