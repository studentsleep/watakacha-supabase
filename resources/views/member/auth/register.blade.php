<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สมัครสมาชิก - Watakacha</title>
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
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12 bg-white">
            <div class="w-full max-w-md space-y-6">
                <div class="text-center lg:text-left">
                    <h1 class="text-3xl font-bold text-gray-900">สมัครสมาชิก</h1>
                    <p class="mt-2 text-gray-500 text-sm">สร้างบัญชีเพื่อสะสมแต้มและรับสิทธิพิเศษ</p>
                </div>

                {{-- Alert Error --}}
                @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form class="mt-8 space-y-4" action="{{ route('member.store') }}" method="POST">
                    @csrf

                    {{-- ✅ เพิ่มช่อง Username --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อผู้ใช้ (Username)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="user" class="w-5 h-5"></i>
                            </div>
                            <input type="text" name="username" required
                                class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand-500 focus:border-transparent bg-gray-50/50"
                                placeholder="ตั้งชื่อผู้ใช้ (ภาษาอังกฤษ)">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อจริง</label>
                            <input type="text" name="first_name" required class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">นามสกุล</label>
                            <input type="text" name="last_name" required class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทรศัพท์</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="phone" class="w-5 h-5"></i>
                            </div>
                            <input type="tel" name="tel" required class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:ring-2 focus:ring-brand-500" placeholder="08x-xxx-xxxx">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล (ถ้ามี)</label>
                        <input type="email" name="email" class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:ring-2 focus:ring-brand-500" placeholder="name@example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่าน</label>
                        <input type="password" name="password" required class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:ring-2 focus:ring-brand-500" placeholder="อย่างน้อย 8 ตัวอักษร">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ยืนยันรหัสผ่าน</label>
                        <input type="password" name="password_confirmation" required class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:ring-2 focus:ring-brand-500">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gray-900 hover:bg-black transition-all hover:-translate-y-0.5">
                            ลงทะเบียนสมาชิก
                        </button>
                    </div>

                    <div class="text-center text-sm text-gray-500 mt-6">
                        มีบัญชีอยู่แล้ว? <a href="{{ route('member.login') }}" class="font-bold text-brand-600 hover:text-brand-500 ml-1">เข้าสู่ระบบ</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Side Image --}}
        <div class="hidden lg:flex w-1/2 bg-brand-50 relative items-center justify-center overflow-hidden">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1585916420730-d7f95e942d43?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-bl from-brand-900/50 to-black/30 backdrop-blur-[1px]"></div>
            </div>
            <div class="relative z-10 text-white p-12 max-w-lg">
                <h2 class="text-4xl font-bold mb-6">เข้าร่วมกับเราวันนี้</h2>
                <ul class="space-y-4 text-lg text-white/90">
                    <li class="flex items-center gap-3">
                        <div class="bg-white/20 p-1.5 rounded-full"><i data-lucide="check" class="w-5 h-5"></i></div><span>สะสมแต้มแลกส่วนลด</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="bg-white/20 p-1.5 rounded-full"><i data-lucide="check" class="w-5 h-5"></i></div><span>จองคิวลองชุดล่วงหน้า</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="bg-white/20 p-1.5 rounded-full"><i data-lucide="check" class="w-5 h-5"></i></div><span>ติดตามสถานะคืนชุด</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>