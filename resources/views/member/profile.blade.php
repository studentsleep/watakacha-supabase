@component('layouts.app')
<div class="pt-8 md:pt-12 pb-12 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- ส่วน Header และปุ่มย้อนกลับ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-3">
            <i data-lucide="user-cog" class="w-8 h-8 text-gray-100"></i>
            <h1 class="text-3xl font-bold text-gray-100">ข้อมูลส่วนตัว</h1>
        </div>
        <button onclick="window.history.back()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 text-gray-700 font-medium transition duration-200">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            ย้อนกลับ
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2"></i>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 md:p-8">
            <form action="{{ route('member.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อจริง <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $member->first_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition" required>
                        @error('first_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">นามสกุล <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $member->last_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition" required>
                        @error('last_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทรศัพท์ <span class="text-red-500">*</span></label>
                        <input type="text" name="tel" value="{{ old('tel', $member->tel) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition" required>
                        @error('tel') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                        <input type="email" name="email" value="{{ old('email', $member->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <hr class="my-8 border-gray-100">

                <h3 class="text-lg font-bold text-gray-900 mb-4">เปลี่ยนรหัสผ่าน <span class="text-sm font-normal text-gray-500">(หากไม่ต้องการเปลี่ยน ให้เว้นว่างไว้)</span></h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่านใหม่</label>
                        <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ยืนยันรหัสผ่านใหม่</label>
                        <input type="password" name="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-gray-900 font-bold py-3 px-8 rounded-xl shadow-lg shadow-brand-500/30 transition transform hover:-translate-y-0.5">
                        บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcomponent