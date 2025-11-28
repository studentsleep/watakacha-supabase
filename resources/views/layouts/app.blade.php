<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- ▼▼▼ 2. Font Prompt จะถูกโหลดผ่านไฟล์ css นี้แทนครับ ▼▼▼ --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

</head>

<body class="font-sans antialiased" x-data="{ sidebarOpen: false }">

    {{-- (โค้ดส่วน <body> ที่เราแก้ครั้งก่อน ถูกต้องแล้ว) --}}

    <div class="flex h-screen">

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- ส่วนเนื้อหาหลัก (Main Content) -->
        <div class="flex-1 flex flex-col transition-all duration-300 bg-gray-100 dark:bg-gray-900"
            :class="sidebarOpen">

            <!-- Top Navigation Bar (สำหรับมือถือ) -->
            <div class="md:hidden">
                @include('layouts.navigation')
            </div>

            <!-- Page Heading (ส่วนหัวเรื่อง) -->
            @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endif

            <!-- Page Content (เนื้อหา) -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- ▼▼▼ [แก้ไข] สั่งให้ Lucide วาดไอคอน หลังจากที่ DOM โหลดเสร็จ ▼▼▼ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. สั่งให้ Alpine.js เริ่มทำงาน (หลังจากที่ users.blade.php โหลดเสร็จแล้ว)
            window.Alpine.start();

            // 2. สั่งให้ Lucide วาดไอคอน
            lucide.createIcons();
        });
    </script>
    {{-- === จบส่วนที่แก้ไข === --}}
</body>

</html>