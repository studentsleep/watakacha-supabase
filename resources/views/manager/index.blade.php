{{-- 
    นี่คือไฟล์ View หลักสำหรับหน้า Manager (เวอร์ชันใหม่)
    - ลบ Tabs ด้านบนออก
    - เปลี่ยน Header (h2) ให้แสดงชื่อตารางที่เลือกแบบ Dynamic
--}}

{{-- ▼▼▼ ส่วนที่แก้ไข (สร้าง Array สำหรับชื่อ Title) ▼▼▼ --}}
@php
    $tableTitles = [
        'users' => 'จัดการบัญชีผู้ใช้',
        'user_types' => 'จัดการประเภทผู้ใช้',
        'items' => 'จัดการสินค้า',
        'item_types' => 'จัดการประเภทสินค้า',
        'item_units' => 'จัดการหน่วยสินค้า',
        'makeups' => 'จัดการช่างแต่งหน้า',
        'photographers' => 'จัดการช่างภาพ',
        // (สามารถเพิ่มตารางอื่นๆ ที่นี่)
    ];
    // หากไม่พบ $table ใน Array ให้ใช้ "การจัดการข้อมูล" เป็นค่าเริ่มต้น
    $title = $tableTitles[$table] ?? 'การจัดการข้อมูล';
@endphp
{{-- === จบส่วนที่แก้ไข === --}}


<x-app-layout>
    <x-slot name="header">
        {{-- ▼▼▼ แสดง Title แบบ Dynamic ▼▼▼ --}}
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __($title) }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            
            {{-- ▼▼▼ ลบส่วน Tabs ทั้งหมด (div ที่มี border-b) ออกไปแล้ว ▼▼▼ --}}
            
            {{-- แสดงตารางตาม $table ที่เลือก (ส่วนนี้ยังคงเดิม) --}}
            <div class="mt-6">
                @if($table == 'users')
                    {{-- 
                        เรียก Partial ของ Users
                        - compact() คือการส่งตัวแปร $users และ $user_types ที่ได้จาก Controller
                        - เข้าไปให้ partial 'manager.partials.users' ใช้งาน
                    --}}
                    @include('manager.partials.users', compact('users', 'user_types'))
                
                @elseif($table == 'user_types')
                    @include('manager.partials.user-types', compact('user_types'))

                @elseif($table == 'items')
                    {{-- (ยังไม่ได้สร้างไฟล์ แต่หลักการเดียวกัน) --}}
                    {{-- @include('manager.partials.items', compact('items', 'units', 'types')) --}}
                    <p>หน้าจัดการสินค้า (Items) - กำลังก่อสร้าง</p>

                @elseif($table == 'item_types')
                     {{-- (ยังไม่ได้สร้างไฟล์ แต่หลักการเดียวกัน) --}}
                    <p>หน้าจัดการประเภทสินค้า (Item Types) - กำลังก่อสร้าง</p>

                @elseif($table == 'item_units')
                     {{-- (ยังไม่ได้สร้างไฟล์ แต่หลักการเดียวกัน) --}}
                    <p>หน้าจัดการหน่วยสินค้า (Item Units) - กำลังก่อสร้าง</p>
                
                @elseif($table == 'makeups')
                    <p>หน้าจัดการช่างแต่งหน้า (Makeups) - กำลังก่อสร้าง</p>
                
                @elseif($table == 'photographers')
                    <p>หน้าจัดการช่างภาพ (Photographers) - กำลังก่อสร้าง</p>

                @else
                    <p>กรุณาเลือกตารางที่ต้องการจัดการจากเมนูด้านข้าง</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

