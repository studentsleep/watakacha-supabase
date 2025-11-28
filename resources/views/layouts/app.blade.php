<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- ▼▼▼ 1. แก้ไข x-data ให้จำค่าการเปิด/ปิดเมนูได้ ▼▼▼ --}}
<body class="font-sans antialiased"
      x-data="{ 
          sidebarOpen: localStorage.getItem('sidebarOpen') === 'true',
          init() {
              this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))
          }
      }">

    <div class="flex h-screen">

        @include('layouts.sidebar')

        {{-- ลบ :class="sidebarOpen" ออก เพราะไม่ได้ใช้ class นี้กับ div หลัก --}}
        <div class="flex-1 flex flex-col transition-all duration-300 bg-gray-100 dark:bg-gray-900">

            <div class="md:hidden">
                @include('layouts.navigation')
            </div>

            @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endif

            <main class="flex-1 overflow-x-hidden overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- ▼▼▼ 2. ลบ Script เดิมทิ้ง และใช้แค่นี้พอครับ ▼▼▼ --}}
    <script>
        // สั่งให้ Lucide ทำงานเมื่อโหลดหน้าเสร็จ
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>

</html>