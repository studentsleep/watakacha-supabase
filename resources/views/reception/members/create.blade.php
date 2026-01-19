<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-indigo-400 leading-tight flex items-center gap-2">
            <i data-lucide="user-plus" class="w-6 h-6"></i>
            ลงทะเบียนสมาชิกใหม่ (ลูกค้า)
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-900 min-h-screen flex flex-col items-center">

        {{-- การ์ดฟอร์ม --}}
        <div class="max-w-md w-full bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-700">

            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-500/10 text-indigo-400 mb-4 border border-indigo-500/20">
                    <i data-lucide="user" class="w-8 h-8"></i>
                </div>
                <h3 class="text-2xl font-bold text-white">สมัครสมาชิกด่วน</h3>
                <p class="text-gray-400 text-sm mt-1">สำหรับพนักงานต้อนรับ</p>
            </div>

            {{-- Alert Success --}}
            @if(session('status'))
            <div class="mb-6 bg-green-500/10 border border-green-500/50 text-green-400 p-4 rounded-xl flex items-center gap-3 animate-pulse">
                <i data-lucide="check-circle" class="w-6 h-6 flex-shrink-0"></i>
                <div>
                    <p class="font-bold">บันทึกข้อมูลเรียบร้อย!</p>
                </div>
            </div>
            @endif

            <form action="{{ route('reception.member.store') }}" method="POST" autocomplete="off">
                @csrf

                {{-- 1. เบอร์โทรศัพท์ --}}
                <div class="mb-6">
                    <label for="tel" class="block text-sm font-medium text-gray-300 mb-2">
                        เบอร์โทรศัพท์ (ใช้เป็น ID)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <input type="tel" name="tel" id="tel" required autofocus
                            class="w-full pl-10 pr-4 py-3 bg-gray-900 border border-gray-600 text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-lg tracking-wide placeholder-gray-600"
                            placeholder="08xxxxxxxx"
                            value="{{ old('tel') }}"
                            maxlength="10"
                            pattern="[0-9]*" inputmode="numeric">
                    </div>
                    @error('tel')
                    <p class="mt-2 text-sm text-red-400 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- 2. รหัสผ่าน --}}
                <div class="mb-8">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        รหัสผ่าน (วันเดือนปีเกิด 6 หลัก)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-10 pr-4 py-3 bg-gray-900 border border-gray-600 text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-lg tracking-widest placeholder-gray-600 text-center"
                            placeholder="YYMMDD"
                            maxlength="6"
                            inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>

                    {{-- กล่องคำเตือน Format --}}
                    <div class="mt-3 p-3 bg-yellow-500/10 rounded-lg border border-yellow-500/20 text-yellow-200/80 text-xs leading-relaxed flex gap-2">
                        <i data-lucide="info" class="w-4 h-4 mt-0.5 flex-shrink-0 text-yellow-500"></i>
                        <div>
                            <strong>รูปแบบ:</strong> ปี(2ตัวท้าย) เดือน วัน<br>
                            เช่น 19 ม.ค. 2026 = <strong class="text-white bg-yellow-600/50 px-1 rounded">260119</strong><br>
                            <span class="text-gray-400">(ลูกค้าสามารถเปลี่ยนรหัสเองได้ภายหลัง)</span>
                        </div>
                    </div>

                    @error('password')
                    <p class="mt-2 text-sm text-red-400 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-900/50 transition transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    ยืนยันการสมัคร
                </button>

            </form>
        </div>
    </div>
</x-app-layout>