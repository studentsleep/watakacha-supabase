<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- 
        ▼▼▼ ลบ div class="py-12" และ div class="max-w-7xl..." ออก ▼▼▼
        เนื่องจาก layout 'app.blade.php' ของคุณมี div เหล่านี้ครอบ {{ $slot }} ไว้อยู่แล้ว
        การใส่ซ้ำจะทำให้เกิดการเว้นระยะห่างซ้ำซ้อน
    --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            {{ __("You're logged in!") }}
        </div>
    </div>

</x-app-layout>