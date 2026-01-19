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
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

{{--
    Logic: ตรวจสอบ localStorage ถ้าไม่มีค่า หรือไม่ใช่ 'false' ให้เปิด Sidebar เป็นค่าเริ่มต้น 
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

    {{-- ================================================================= --}}
    {{-- 🛠️ Scripts Zone --}}
    {{-- ================================================================= --}}

    {{-- 1. SweetAlert2 CDN (สำหรับ Alert สวยๆ) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 2. เริ่มทำงาน Lucide Icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // 3. ✅ ฟังก์ชันเปิด Modal แบบเก่า (Manager CRUD)
        window.toggleModal = function(modalID, show) {
            const modal = document.getElementById(modalID);
            if (modal) {
                if (show) {
                    modal.classList.remove('hidden');
                    modal.style.display = 'block';

                    // Re-render Icons ใน Modal
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

        // 4. ✅ ฟังก์ชันยืนยันการลบ (Global Delete Confirmation)
        // เรียกใช้โดย: onclick="confirmDelete('form-id')"
        window.confirmDelete = function(formId, title = 'ยืนยันการลบ?', text = 'ข้อมูลที่ถูกลบจะไม่สามารถกู้คืนได้!') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                // ปรับแต่งสีให้เข้ากับ Dark Mode
                background: '#1f2937',
                color: '#fff',
                confirmButtonColor: '#ef4444', // สีแดง
                cancelButtonColor: '#6b7280', // สีเทา
                confirmButtonText: 'ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                customClass: {
                    popup: 'rounded-xl border border-gray-700 shadow-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            })
        }

        // 5. ✅ แสดง Alert เมื่อทำรายการสำเร็จ (ดึงจาก session('status'))
        const sessionStatus = `{{ session('status') }}`;

        if (sessionStatus) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: sessionStatus, // เรียกใช้ตัวแปร JS ตรงนี้
                background: '#1f2937',
                color: '#fff',
                confirmButtonColor: '#4f46e5',
                timer: 2000,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-xl border border-gray-700 shadow-xl'
                }
            });
        }
    </script>
</body>

</html>