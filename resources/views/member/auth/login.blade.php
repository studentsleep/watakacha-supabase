<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เข้าสู่ระบบสมาชิก - Watakacha</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>

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
</head>

<body class="bg-white">

    <div class="flex min-h-screen">
        <div class="hidden lg:flex w-1/2 bg-gray-100 relative items-center justify-center overflow-hidden">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1550614000-4b9519e003ac?q=80&w=2000&auto=format&fit=crop"
                    class="w-full h-full object-cover" alt="Login Background">
                <div class="absolute inset-0 bg-gradient-to-tr from-brand-900/60 to-purple-900/40 backdrop-blur-[1px]"></div>
            </div>
            <div class="relative z-10 text-white p-12 text-center">
                <h2 class="text-4xl font-bold mb-4">ยินดีต้อนรับกลับมา</h2>
                <p class="text-lg text-white/90 font-light">
                    "ชุดสวยทำให้วันสำคัญของคุณ...สมบูรณ์แบบยิ่งขึ้น"
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
            <div class="w-full max-w-md space-y-8">

                {{-- Logo Mobile --}}
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 bg-brand-50 p-2 rounded-xl text-brand-600 mb-6">
                        <i data-lucide="gem" class="w-6 h-6"></i>
                        <span class="font-bold tracking-tight">Watakacha Member</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">เข้าสู่ระบบ</h1>
                    <p class="mt-2 text-gray-500 text-sm">กรุณากรอกข้อมูลเพื่อเข้าใช้งานบัญชีของคุณ</p>
                </div>

                <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST"> @csrf

                    <div class="space-y-5">
                        {{-- Email --}}
                        <div>
                            <label for="login_id" class="block text-sm font-medium text-gray-700 mb-1">ชื่อผู้ใช้ หรือ เบอร์โทรศัพท์</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i data-lucide="user" class="w-5 h-5"></i>
                                </div>
                                {{-- ✅ เปลี่ยน name="email" เป็น name="login_id" --}}
                                <input type="text" name="login_id" id="login_id" required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl ..."
                                    placeholder="Username หรือ 08xxxxxxxx">
                            </div>
                        </div>

                        {{-- Password --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                                <a href="#" class="text-xs font-medium text-brand-600 hover:text-brand-500">ลืมรหัสผ่าน?</a>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i data-lucide="lock" class="w-5 h-5"></i>
                                </div>
                                <input type="password" name="password" id="password" required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition bg-gray-50/50"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox"
                            class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-600">จำการเข้าสู่ระบบ</label>
                    </div>

                    {{-- Button --}}
                    <button type="submit"
                        class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-purple-600 hover:from-brand-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all transform hover:-translate-y-0.5">
                        เข้าสู่ระบบ
                    </button>

                    {{-- Register Link --}}
                    <div class="text-center text-sm text-gray-500 mt-6">
                        ยังไม่มีบัญชีสมาชิก?
                        <a href="{{ route('member.register') }}" class="font-bold text-brand-600 hover:text-brand-500 ml-1">
                            สมัครสมาชิกใหม่
                        </a>
                    </div>
                </form>

                {{-- Back to Home --}}
                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <a href="{{ route('welcome') }}" class="inline-flex items-center text-xs font-medium text-gray-400 hover:text-gray-600 transition">
                        <i data-lucide="arrow-left" class="w-3 h-3 mr-1"></i> กลับหน้าหลัก
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>