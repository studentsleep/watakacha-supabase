<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-[50vh] text-center p-6">
        <div class="bg-red-100 p-4 rounded-full mb-4">
            <i data-lucide="shield-alert" class="w-12 h-12 text-red-600"></i>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-2">ไม่มีสิทธิ์เข้าถึง (Access Denied)</h1>

        <p class="text-gray-600 mb-6 max-w-md">
            สวัสดีคุณ <span class="font-bold text-indigo-600">{{ Auth::user()->first_name }}</span><br>
            บัญชีของคุณ (ตำแหน่ง: {{ Auth::user()->userType->name ?? 'N/A' }})<br>
            ไม่ได้รับอนุญาตให้เข้าใช้งานระบบจัดการหลังบ้าน
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-6 py-2 bg-gray-800 hover:bg-black text-white rounded-lg font-bold transition shadow-lg">
                ออกจากระบบ
            </button>
        </form>
    </div>
</x-guest-layout>