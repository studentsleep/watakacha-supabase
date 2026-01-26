<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ติดต่อเรา - {{ config('app.name') }}</title>
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

    {{-- ✅ 1. NAVBAR (มาตรฐานเดียวกับหน้าอื่น) --}}
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
                    <a href="{{ route('catalog') }}" class="font-medium text-gray-600 hover:text-brand-500 transition">ชุดทั้งหมด</a>
                    <a href="{{ route('promotions') }}" class="font-medium text-gray-600 hover:text-brand-500 transition">โปรโมชั่น</a>
                    <a href="{{ route('contact') }}" class="font-bold text-brand-600">ติดต่อเรา</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="hidden md:flex items-center gap-3">
                    @guest('member')
                    <a href="{{ route('member.login') }}" class="px-4 py-2 rounded-full text-sm font-bold transition border border-brand-500 text-brand-600 hover:bg-brand-50">เข้าสู่ระบบ</a>
                    <a href="{{ route('member.register') }}" class="px-4 py-2 rounded-full text-sm font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lg">สมัครสมาชิก</a>
                    @endguest

                    @auth('member')
                    <div class="relative ml-3" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="flex items-center gap-3 bg-white border border-gray-200 rounded-full p-1 pr-4 shadow-sm hover:shadow-md transition">
                            <img class="h-9 w-9 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ Auth::guard('member')->user()->first_name }}&background=ec4899&color=fff">
                            <div class="text-left">
                                <span class="text-xs font-bold block">คุณ{{ Auth::guard('member')->user()->first_name }}</span>
                                <span class="text-[10px] text-yellow-600 font-bold">⭐ {{ number_format(Auth::guard('member')->user()->points ?? 0) }} แต้ม</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50">
                            <div class="px-4 py-2 border-b border-gray-100 text-xs text-gray-500">บัญชีของฉัน</div>
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

    {{-- Mobile Menu --}}
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
            <form method="POST" action="{{ route('member.logout') }}">@csrf<button class="text-red-600 font-bold">ออกจากระบบ</button></form>
            <hr class="my-2">
            @endauth
            <a href="{{ route('welcome') }}">หน้าแรก</a>
            <a href="{{ route('catalog') }}">ชุดทั้งหมด</a>
            <a href="{{ route('promotions') }}">โปรโมชั่น</a>
            <a href="{{ route('contact') }}" class="text-brand-600 font-bold">ติดต่อเรา</a>
            @guest('member')
            <hr>
            <a href="{{ route('member.login') }}" class="text-brand-600 font-bold">เข้าสู่ระบบ</a>
            <a href="{{ route('member.register') }}">สมัครสมาชิก</a>
            @endguest
        </div>
    </div>

    {{-- ✅ 2. CONTENT (ส่วนเนื้อหา) --}}
    <div class="pt-24 pb-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">ติดต่อเรา</h1>
                <div class="w-24 h-1.5 bg-brand-500 mx-auto rounded-full"></div>
                <p class="text-gray-500 mt-4 text-lg">ยินดีให้บริการและคำปรึกษา แวะมาลองชุดสวยๆ ที่ร้านได้เลยค่ะ</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

                {{-- ฝั่งซ้าย: ข้อมูลการติดต่อ --}}
                <div class="space-y-6">
                    {{-- Card 1: Address --}}
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-brand-50 text-brand-600 rounded-2xl">
                                <i data-lucide="map-pin" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">ที่อยู่ร้าน</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    ต.ต้นธง อ.เมือง จ.ลำพูน 51000<br>
                                    <span class="text-sm text-gray-400">(อยู่ใกล้กับวัดต้นธง ห่างจากตัวเมืองลำพูนเพียง 10 นาที)</span>
                                </p>
                                <a href="https://maps.google.com" target="_blank" class="inline-flex items-center mt-3 text-brand-600 font-bold hover:underline">
                                    นำทางด้วย Google Maps <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Contact Info --}}
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-green-50 text-green-600 rounded-2xl">
                                <i data-lucide="phone-call" class="w-8 h-8"></i>
                            </div>
                            <div class="w-full">
                                <h3 class="text-xl font-bold text-gray-900 mb-4">ช่องทางติดต่อ</h3>
                                <ul class="space-y-4">
                                    <li class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="phone" class="w-5 h-5 text-gray-400"></i>
                                            <span class="font-medium">เบอร์โทรศัพท์</span>
                                        </div>
                                        <span class="text-gray-700 font-bold">093-130-9899</span>
                                    </li>
                                    <li class="flex items-center justify-between p-3 bg-green-50/50 rounded-xl border border-green-100">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="message-circle" class="w-5 h-5 text-green-500"></i>
                                            <span class="font-medium text-green-900">LINE ID</span>
                                        </div>
                                        <span class="text-green-700 font-bold">@watakacha</span>
                                    </li>
                                    <li class="flex items-center justify-between p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="facebook" class="w-5 h-5 text-blue-500"></i>
                                            <span class="font-medium text-blue-900">Facebook</span>
                                        </div>
                                        <span class="text-blue-700 font-bold">Watakacha Rental</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Opening Hours --}}
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-orange-50 text-orange-500 rounded-2xl">
                                <i data-lucide="clock" class="w-8 h-8"></i>
                            </div>
                            <div class="w-full">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">เวลาทำการ</h3>
                                <div class="mt-4 space-y-2">
                                    <div class="flex justify-between text-gray-600">
                                        <span>วันจันทร์ - ศุกร์</span>
                                        <span class="font-bold text-gray-900">09:00 - 20:00 น.</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>วันเสาร์ - อาทิตย์</span>
                                        <span class="font-bold text-gray-900">10:00 - 21:00 น.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ฝั่งขวา: แผนที่ Google Map --}}
                <div class="h-full min-h-[400px] lg:min-h-[600px] bg-white rounded-3xl shadow-lg border-4 border-white overflow-hidden relative">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3780.273646698664!2d98.99592837519456!3d18.65178298747761!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30da3a7e93766629%3A0x6272333333333333!2sWat%20Ton%20Thong!5e0!3m2!1sen!2sth!4v1700000000000!5m2!1sen!2sth"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        class="absolute inset-0 w-full h-full"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

            </div>
        </div>
    </div>

    {{-- ✅ 3. FOOTER --}}
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-6 flex justify-center items-center gap-2">
                @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" class="h-8 w-auto brightness-0 invert">
                @else
                <i data-lucide="gem" class="w-6 h-6 text-brand-500"></i>
                @endif
                <span class="text-xl font-bold">Watakacha Rental</span>
            </div>
            <p class="text-gray-400">ร้านเช่าชุดที่ใส่ใจทุกรายละเอียด คัดสรรชุดสวยคุณภาพดี เพื่อให้คุณมั่นใจที่สุดในวันสำคัญ</p>
            <div class="text-xs text-gray-600 mt-8 pt-8 border-t border-gray-800">
                &copy; {{ date('Y') }} Watakacha Rental. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>