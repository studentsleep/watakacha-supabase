<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>โปรโมชั่น - {{ config('app.name') }}</title>
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

<body class="bg-gray-50 text-gray-800 font-sans antialiased" x-data="{ scrolled: true, mobileMenuOpen: false }">

    {{-- NAVBAR (เหมือนเดิม) --}}
    <nav class="fixed w-full z-40 transition-all duration-300 bg-white/90 backdrop-blur-md shadow-md py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <a href="{{ route('welcome') }}" class="flex items-center gap-2">
                        @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" class="h-10 w-auto">
                        @else
                        <i data-lucide="gem" class="w-6 h-6 text-brand-500"></i>
                        @endif
                        <span class="text-2xl font-bold tracking-tight text-gray-900">Watakacha</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('welcome') }}" class="font-medium text-gray-600 hover:text-brand-500 transition">หน้าแรก</a>
                    <a href="{{ route('catalog') }}" class="font-medium text-gray-600 hover:text-brand-500 transition">ชุดทั้งหมด</a>
                    <a href="{{ route('promotions') }}" class="font-bold text-brand-600">โปรโมชั่น</a>
                    <a href="{{ route('contact') }}" class="font-medium text-gray-600 hover:text-brand-500 transition">ติดต่อเรา</a>
                </div>
                <div class="hidden md:flex items-center gap-3">
                    @guest('member')
                    <a href="{{ route('member.login') }}" class="px-4 py-2 rounded-full text-sm font-bold border border-brand-500 text-brand-600 hover:bg-brand-50">เข้าสู่ระบบ</a>
                    <a href="{{ route('member.register') }}" class="px-4 py-2 rounded-full text-sm font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lg">สมัครสมาชิก</a>
                    @endguest
                    @auth('member')
                    <span class="text-sm font-bold">สวัสดี, คุณ{{ Auth::guard('member')->user()->first_name }}</span>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- CONTENT --}}
    <div class="pt-24 pb-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">โปรโมชั่นสุดพิเศษ</h1>
                <div class="w-24 h-1.5 bg-brand-500 mx-auto rounded-full"></div>
                <p class="text-gray-500 mt-4">สิทธิพิเศษสำหรับลูกค้าคนสำคัญ ห้ามพลาด!</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($promotions as $promo)
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
                    <div class="bg-gradient-to-r from-brand-500 to-purple-600 p-8 text-white text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mr-6 -mt-6 w-24 h-24 bg-white/20 rounded-full blur-2xl group-hover:scale-150 transition duration-700"></div>
                        <i data-lucide="tag" class="w-8 h-8 text-white/50 mx-auto mb-2"></i>
                        <h2 class="text-4xl font-extrabold">
                            {{ number_format($promo->discount_value) }}
                            <span class="text-2xl">{{ $promo->discount_type == 'percentage' ? '%' : '฿' }}</span>
                        </h2>
                        <p class="font-medium opacity-90 tracking-widest mt-1">DISCOUNT</p>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $promo->promotion_name }}</h3>
                        <p class="text-gray-600 text-sm mb-4 min-h-[40px]">{{ $promo->description }}</p>

                        <div class="flex items-center justify-center text-sm text-gray-500 mb-6 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <i data-lucide="calendar-clock" class="w-4 h-4 mr-2 text-brand-500"></i>
                            หมดเขต: <span class="font-bold ml-1 text-gray-700">{{ \Carbon\Carbon::parse($promo->end_date)->translatedFormat('d M Y') }}</span>
                        </div>

                        <a href="{{ route('catalog') }}" class="block w-full py-3 text-center bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition shadow-lg hover:shadow-xl">
                            ใช้โปรโมชั่นนี้
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-24 bg-white rounded-3xl shadow-sm border border-gray-100">
                    <div class="inline-block p-6 bg-gray-50 rounded-full mb-4">
                        <i data-lucide="gift" class="w-16 h-16 text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">ยังไม่มีโปรโมชั่นในขณะนี้</h3>
                    <p class="text-gray-500 mt-2">ติดตามข่าวสารใหม่ๆ ได้เร็วๆ นี้นะคะ</p>
                    <a href="{{ route('welcome') }}" class="inline-block mt-6 text-brand-600 font-bold hover:underline">กลับหน้าหลัก</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-2xl font-bold mb-4">Watakacha Rental</h3>
            <div class="text-xs text-gray-500 mt-8 pt-8 border-t border-gray-800">
                &copy; {{ date('Y') }} Watakacha Rental. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>