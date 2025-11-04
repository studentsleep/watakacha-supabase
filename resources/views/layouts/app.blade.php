<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- ▼▼▼ 1. ต้องมี Script ไอคอน Lucide ▼▼▼ -->
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- ▼▼▼ 2. [แก้ไข] แยก @vite เป็น 2 บรรทัด ▼▼▼ --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    {{-- === จบส่วนที่แก้ไข === --}}

    <!-- Style สำหรับเมนูย่อย (Flyout) ที่เราใช้ใน Sidebar -->
    <style>
        .flyout-link {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.5rem 0.75rem;
            /* py-2 px-3 */
            color: #D1D5DB;
            /* text-gray-300 */
            border-radius: 0.375rem;
            /* rounded-md */
        }

        .flyout-link:hover {
            background-color: #374151;
            /* hover:bg-gray-700 */
            color: #FFFFFF;
            /* hover:text-white */
        }

        .flyout-link .icon-size {
            width: 1.25rem;
            /* w-5 */
            height: 1.25rem;
            /* h-5 */
            margin-right: 0.75rem;
            /* mr-3 */
        }
    </style>
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