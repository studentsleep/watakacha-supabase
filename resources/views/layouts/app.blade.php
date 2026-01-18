<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- ป้องกันการกระพริบของ AlpineJS --}}
    <style> [x-cloak] { display: none !important; } </style>
</head>

{{-- 
    ▼▼▼ แก้ไข Logic: 
    localStorage.getItem('sidebarOpen') !== 'false' 
    (แปลว่า: ถ้าไม่เคยตั้งค่ามาก่อน หรือค่าไม่ใช่ 'false' ให้เป็น true เสมอ -> เปิด Sidebar เป็นค่าเริ่มต้น) 
--}}
<body class="font-sans antialiased"
      x-data="{ 
          sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
          init() {
              this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))
          }
      }" x-cloak>

    <div class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900">

        @include('layouts.sidebar')

        {{-- Main Content Wrapper --}}
        <div class="flex-1 flex flex-col overflow-hidden transition-all duration-300">

            {{-- Mobile Nav (แสดงเฉพาะจอเล็ก) --}}
            <div class="md:hidden">
                @include('layouts.navigation')
            </div>

            @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow-sm z-10">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endif

            {{-- Scrollable Content --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- ▼▼▼ Scripts รวม ▼▼▼ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. เริ่มทำงาน Lucide Icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // 2. ✅ [สำคัญมาก] ฟังก์ชันสำหรับเปิด Modal แบบเก่า (Manager CRUD)
        // ต้องมีฟังก์ชันนี้ ไม่อย่างนั้นปุ่ม "เพิ่ม/แก้ไข" ในหน้า Manager จะกดไม่ไป
        window.toggleModal = function(modalID, show) {
            const modal = document.getElementById(modalID);
            if (modal) {
                if (show) {
                    modal.classList.remove('hidden');
                    modal.style.display = 'block';
                    
                    // Re-render Icons ใน Modal (เผื่อมี icon ใหม่)
                    if (typeof lucide !== 'undefined') {
                        setTimeout(() => lucide.createIcons(), 100);
                    }
                } else {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                }
            } else {
                console.error('Modal ID not found:', modalID);
            }
        }
    </script>
</body>
</html>