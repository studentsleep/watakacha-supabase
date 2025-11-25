{{--
    Sidebar ที่แก้ไขใหม่
    - เพิ่มปุ่ม Toggle (ไอคอน menu/panel-left-close) สำหรับ @click="sidebarOpen = !sidebarOpen"
    - ปุ่มนี้จะอยู่ตรงกลางเมื่อ Sidebar ย่อ (justify-center)
--}}
<aside
    class="flex-shrink-0 bg-gray-900 text-gray-200 p-4 hidden md:block transition-all duration-300 flex flex-col h-full"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
    style="z-index: 50;">

    {{-- ส่วนเมนูด้านบน (โลโก้ + เมนูหลัก) --}}
    <div>
        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center mb-4">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-200" />
            <span class="ml-2 text-xl font-bold text-white"
                x-show="sidebarOpen" x-transition>
                {{-- เปลี่ยนชื่อตามสิทธิ์ --}}
                {{ Auth::user()->user_type_id == 1 ? 'Manager' : 'Reception' }}
            </span>
        </a>

        {{-- ▼▼▼ ปุ่ม Toggle Sidebar ▼▼▼ --}}
        <div class="mb-4" :class="sidebarOpen ? 'flex justify-end' : 'flex justify-center'">
            <button
                @click="sidebarOpen = !sidebarOpen; $nextTick(() => lucide.createIcons())"
                class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-md"
                :title="sidebarOpen ? 'ย่อเมนู' : 'ขยายเมนู'">

                {{-- แสดงไอคอน 'panel-left-close' เมื่อ Sidebar เปิด --}}
                <i data-lucide="panel-left-close" class="w-5 h-5" x-show="sidebarOpen"></i>

                {{-- แสดงไอคอน 'menu' เมื่อ Sidebar ปิด --}}
                <i data-lucide="menu" class="w-5 h-5" x-show="!sidebarOpen"></i>
            </button>
        </div>
        {{-- === ▲▲▲ จบส่วนปุ่ม Toggle ▲▲▲ === --}}


        {{-- เมนู --}}
        <nav>
            <ul class="space-y-2">

                {{-- 1. เมนู Dashboard (ลิงก์ตรง) --}}
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'"
                        :title="sidebarOpen ? '' : 'แดชบอร์ด'">

                        <i data-lucide="home" class="w-5 h-5"></i>

                        <span class="ml-3" x-show="sidebarOpen" x-transition>
                            แดชบอร์ด
                        </span>
                    </a>
                </li>

                @if(Auth::user()->user_type_id == 1)
                {{-- 2. เมนู "การจัดการ" (แบบมีเมนูย่อย) --}}
                <li x-data="{ open: false }" class="relative">
                    <button @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false"
                        class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'"
                        :title="sidebarOpen ? '' : 'การจัดการ'">

                        <i data-lucide="database" class="w-5 h-5"></i>

                        <span class="ml-3" x-show="sidebarOpen" x-transition>
                            การจัดการ
                        </span>

                        <i data-lucide="chevron-right" class="w-4 h-4 ml-auto" x-show="!open && sidebarOpen"></i>
                    </button>

                    <div x-show="open"
                        @mouseenter="open = true" @mouseleave="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute left-full top-0 w-64 ml-2 p-4 bg-gray-800 rounded-lg shadow-lg"
                        style="display: none;">

                        {{-- [แก้ไข] อัปเดตลิงก์ทั้งหมด --}}
                        <ul class="space-y-2">
                            <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">ผู้ใช้</span></li>
                            <li><a href="{{ route('manager.index', ['table' => 'member_accounts']) }}" class="flyout-link"><i data-lucide="users" class="icon-size"></i> บัญชีสมาชิก</a></li>
                            <li><a href="{{ route('manager.index', ['table' => 'users']) }}" class="flyout-link"><i data-lucide="shield" class="icon-size"></i> บัญชีผู้ใช้ (Admin)</a></li>
                            <li><a href="{{ route('manager.index', ['table' => 'user_types']) }}" class="flyout-link"><i data-lucide="users-round" class="icon-size"></i> ประเภทผู้ใช้</a></li>

                            <li><span class="block px-2 pt-3 pb-1 text-xs font-semibold text-gray-500 uppercase">สินค้า</span></li>
                            <li><a href="{{ route('manager.index', ['table' => 'items']) }}" class="flyout-link"><i data-lucide="package" class="icon-size"></i> สินค้า (Items)</a></li>
                            <li><a href="{{ route('manager.index', ['table' => 'item_types']) }}" class="flyout-link"><i data-lucide="list-tree" class="icon-size"></i> ประเภทสินค้า</a></li>
                            <li><a href="{{ route('manager.index', ['table' => 'item_units']) }}" class="flyout-link"><i data-lucide="box-select" class="icon-size"></i> หน่วยสินค้า</a></li>
                            <li><a href="{{ route('manager.index', ['table' => 'promotions']) }}" class="flyout-link"><i data-lucide="percent-circle" class="icon-size"></i> โปรโมชั่น</a></li>

                            <li><span class="block px-2 pt-3 pb-1 text-xs font-semibold text-gray-500 uppercase">บริการ</span></li>
                            <li><a href="{{ route('manager.index', ['table' => 'care_shops']) }}" class="flyout-link"><i data-lucide="washing-machine" class="icon-size"></i> ร้านดูแลชุด</a></li>
                            <li><a href="{{ route('manager.index', ['table' => 'makeup_artists']) }}" class="flyout-link"><i data-lucide="sparkles" class="icon-size"></i> ช่างแต่งหน้า</a></li>
                            <li><a href="{{ route('manager.index', ['table' => 'photographers']) }}" class="flyout-link"><i data-lucide="camera" class="icon-size"></i> ช่างภาพ</a></li>
                            <li><a href="{{ route('manager.index', ['table' => 'photographer_packages']) }}" class="flyout-link"><i data-lucide="layers" class="icon-size"></i> แพ็คเกจช่างภาพ</a></li>

                        </ul>
                    </div>
                </li>

                {{-- 3. [เพิ่ม] เมนู "ประวัติ" (แบบมีเมนูย่อย) --}}
                <li x-data="{ open: false }" class="relative">
                    <button @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false"
                        class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'"
                        :title="sidebarOpen ? '' : 'ประวัติ'">

                        <i data-lucide="history" class="w-5 h-5"></i>

                        <span class="ml-3" x-show="sidebarOpen" x-transition>
                            ประวัติ
                        </span>

                        <i data-lucide="chevron-right" class="w-4 h-4 ml-auto" x-show="!open && sidebarOpen"></i>
                    </button>

                    <div x-show="open"
                        @mouseenter="open = true" @mouseleave="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute left-full top-0 w-64 ml-2 p-4 bg-gray-800 rounded-lg shadow-lg"
                        style="display: none;">

                        <ul class="space-y-2">
                            <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">ประวัติข้อมูล</span></li>

                            {{-- ลิงก์ไปหน้าประวัติการเช่า --}}
                            <li>
                                <a href="{{ route('manager.index', ['table' => 'rentals']) }}" class="flyout-link">
                                    <i data-lucide="clipboard-list" class="icon-size"></i> ประวัติการเช่า
                                </a>
                            </li>

                            {{-- ลิงก์ไปหน้าประวัติการใช้แต้ม --}}
                            <li>
                                <a href="{{ route('manager.index', ['table' => 'point_transactions']) }}" class="flyout-link">
                                    <i data-lucide="star" class="icon-size"></i> ประวัติการใช้แต้ม
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('manager.index', ['table' => 'services']) }}" class="flyout-link">
                                    <i data-lucide="library" class="icon-size"></i> ประวัติการบริการ
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                {{-- ▼▼▼ ส่วนของ USER TYPE 2: RECEPTION (พนักงานต้อนรับ) ▼▼▼ --}}
                @elseif(Auth::user()->user_type_id == 2)

                {{-- เมนู "บริการเช่า-คืน" --}}
                <li>
                    <a href="{{ route('reception.rental') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-green-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>เช่าชุด (Rental)</span>
                    </a>
                </li>

                {{-- [เพิ่ม] เมนู "คืนชุด" (Return) --}}
                <li>
                    <a href="{{ route('reception.return') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-blue-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="corner-down-left" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>คืนชุด (Return)</span>
                    </a>
                </li>

                {{-- เมนู "ประวัติ" (ของ Reception - ดูได้อย่างเดียว) --}}
                <li x-data="{ open: false }" class="relative">
                    <button @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false"
                        class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="history" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>ประวัติ</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-auto" x-show="!open && sidebarOpen"></i>
                    </button>
                    <div x-show="open" @mouseenter="open = true" @mouseleave="open = false" class="absolute left-full top-0 w-64 ml-2 p-4 bg-gray-800 rounded-lg shadow-lg" style="display: none;">
                        <ul class="space-y-2">
                            {{-- ลิงก์ไปหน้าประวัติที่เราเพิ่งสร้าง --}}
                            <li><a href="{{ route('reception.history') }}" class="flyout-link"><i data-lucide="clipboard-list" class="icon-size"></i> ประวัติการเช่า-คืน</a></li>
                        </ul>
                    </div>
                </li>
                @endif
            </ul>
        </nav>
    </div>

    {{-- ส่วน Profile ด้านล่าง (เหมือนเดิม) --}}
    <div class="mt-auto border-t border-gray-700 pt-4" x-data="{ profileOpen: false }">
        <div class="relative">

            <!-- เมนูป๊อปอัพ (แสดงเมื่อคลิก) -->
            <div x-show="profileOpen"
                @click.away="profileOpen = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute bottom-full left-0 right-0 mb-2 w-full bg-gray-800 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5"
                style="display: none;">

                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                    {{ __('Profile') }}
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>

            <!-- ปุ่มสำหรับเปิดเมนู Profile -->
            <button @click="profileOpen = !profileOpen"
                class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                :class="sidebarOpen ? '' : 'justify-center'"
                :title="sidebarOpen ? '' : '{{ Auth::user()->name }}'">

                <i data-lucide="user-circle" class="w-6 h-6"></i>

                <div class="ml-3 text-left" x-show="sidebarOpen" x-transition>

                    <!-- บรรทัดที่ 1 (ชื่อผู้ใช้) -->
                    <span class="text-sm font-medium">{{ Auth::user()->name }}</span>


                    <!-- บรรทัดที่ 2 (ชื่อจริง-นามสกุล) -->
                    <span class="ml-3" x-show="sidebarOpen" x-transition>
                        {{-- ใช้ mb_substr เพื่อความปลอดภัยกับอักษรภาษาไทย --}}
                        สวัสดีคุณ {{ mb_strtoupper(mb_substr(Auth::user()->first_name ?? '', 0, 1, 'UTF-8'), 'UTF-8') }}{{ mb_strtoupper(mb_substr(Auth::user()->last_name ?? '', 0, 1, 'UTF-8'), 'UTF-8') }}
                    </span>

                </div>

                <i data-lucide="chevron-up" class="w-4 h-4 ml-auto" x-show="sidebarOpen"></i>
            </button>
        </div>
    </div>

</aside>