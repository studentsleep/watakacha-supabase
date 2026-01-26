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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Prompt', 'sans-serif']
                    },
                    colors: {
                        // ธีมสีทอง หรูหรา
                        gold: {
                            50: '#fffbf0',
                            100: '#fef2cd',
                            200: '#fde48d',
                            300: '#fcd04d',
                            400: '#fyb913',
                            500: '#eab308', // ทองหลัก
                            600: '#ca8a04', // ทองเข้ม
                            700: '#a16207',
                            800: '#854d0e',
                            900: '#713f12',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50">

    <div class="flex min-h-screen">
        <div class="hidden lg:flex w-1/2 bg-gray-900 relative items-center justify-center overflow-hidden">
            <div class="absolute inset-0">
                <img src="{{ asset('images/wa2.png') }}" class="w-full h-full object-cover opacity-60" alt="Banner">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-gray-900/40 to-transparent"></div>
            </div>
            <div class="relative z-10 text-white p-12 text-center max-w-lg">
                <div class="mb-6 inline-block p-3 border border-gold-400/30 rounded-full bg-black/20 backdrop-blur-sm">
                    <i data-lucide="crown" class="w-8 h-8 text-gold-400"></i>
                </div>
                <h2 class="text-4xl font-bold mb-4 font-serif tracking-wide text-gold-100">Welcome Back</h2>
                <p class="text-lg text-gray-300 font-light leading-relaxed">
                    "ที่สุดแห่งความหรูหรา เพื่อวันสำคัญของคุณ...<br>เข้าสู่ระบบเพื่อรับสิทธิพิเศษระดับ Exclusive"
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
            <div class="w-full max-w-md space-y-8">

                {{-- Logo Section --}}
                <div class="text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start gap-3 mb-6">
                        @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Watakacha Logo" class="h-16 w-auto drop-shadow-md">
                        @else
                        <div class="bg-gradient-to-br from-gold-400 to-gold-600 text-white p-3 rounded-xl shadow-lg">
                            <i data-lucide="gem" class="w-8 h-8"></i>
                        </div>
                        @endif
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">เข้าสู่ระบบสมาชิก</h1>
                    <p class="mt-2 text-gray-500 text-sm">จัดการข้อมูลการเช่าและสะสมแต้มของคุณ</p>
                </div>

                <form class="mt-8 space-y-6" action="{{ route('member.login') }}" method="POST">
                    @csrf

                    <div class="space-y-5">
                        {{-- Email/Username Input --}}
                        <div class="group">
                            <label for="login_id" class="block text-sm font-medium text-gray-700 mb-1 ml-1">บัญชีผู้ใช้</label>
                            <div class="relative transition-all duration-300 focus-within:transform focus-within:-translate-y-1">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="user" class="w-5 h-5 text-gray-400 group-focus-within:text-gold-600 transition-colors"></i>
                                </div>
                                <input type="text" name="login_id" id="login_id" required value="{{ old('login_id') }}"
                                    class="block w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-2xl bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500 focus:bg-white transition-all shadow-sm"
                                    placeholder="ชื่อผู้ใช้ หรือ เบอร์โทรศัพท์">
                            </div>
                        </div>

                        {{-- Password Input --}}
                        <div class="group">
                            <div class="flex items-center justify-between mb-1 ml-1">
                                <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                                <a href="#" class="text-xs font-medium text-gold-600 hover:text-gold-700 hover:underline">ลืมรหัสผ่าน?</a>
                            </div>
                            <div class="relative transition-all duration-300 focus-within:transform focus-within:-translate-y-1">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="lock" class="w-5 h-5 text-gray-400 group-focus-within:text-gold-600 transition-colors"></i>
                                </div>
                                <input type="password" name="password" id="password" required
                                    class="block w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-2xl bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500 focus:bg-white transition-all shadow-sm"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center ml-1">
                        <input id="remember-me" name="remember" type="checkbox"
                            class="h-4 w-4 text-gold-600 focus:ring-gold-500 border-gray-300 rounded cursor-pointer">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">จำการเข้าสู่ระบบ</label>
                    </div>

                    {{-- Button สีทอง --}}
                    <button type="submit"
                        class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-lg shadow-gold-500/20 text-sm font-bold text-white bg-gradient-to-r from-gold-500 to-yellow-600 hover:from-gold-600 hover:to-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500 transition-all transform hover:-translate-y-0.5 hover:shadow-xl active:scale-95">
                        เข้าสู่ระบบ
                    </button>

                    {{-- Register Link --}}
                    <div class="text-center text-sm text-gray-500 mt-6 pt-6 border-t border-gray-100">
                        ยังไม่มีบัญชีสมาชิก?
                        <a href="{{ route('member.register') }}" class="font-bold text-gold-600 hover:text-gold-800 ml-1 transition-colors">
                            สมัครสมาชิกใหม่
                        </a>
                    </div>
                </form>

                {{-- Back to Home --}}
                <div class="text-center">
                    <a href="{{ route('welcome') }}" class="inline-flex items-center text-xs font-medium text-gray-400 hover:text-gray-800 transition py-2 px-4 rounded-full hover:bg-gray-100">
                        <i data-lucide="arrow-left" class="w-3 h-3 mr-1"></i> กลับหน้าหลัก
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        document.addEventListener('DOMContentLoaded', function() {

            const errorMessage = "{{ $errors->first('login_id') }}";
            const successMessage = "{{ session('success') }}";

            // 1. ถ้ามี Error (ข้อความไม่ว่างเปล่า)
            if (errorMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'เข้าสู่ระบบไม่สำเร็จ',
                    text: errorMessage,
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#eab308',
                    background: '#fff',
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });
            }

            // 2. ถ้ามี Success
            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: successMessage,
                    showConfirmButton: false,
                    timer: 2000,
                    background: '#fff',
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });
            }
        });
    </script>
</body>

</html>