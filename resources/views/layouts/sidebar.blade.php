<aside
    class="flex-shrink-0 bg-gray-900 text-gray-200 p-4 hidden md:block transition-all duration-300 flex flex-col h-full"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
    style="z-index: 50;">

    <style>
        .flyout-link {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.5rem 0.75rem;
            color: #D1D5DB;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .flyout-link:hover {
            background-color: #374151;
            color: #FFFFFF;
        }

        .flyout-link .icon-size {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.75rem;
        }

        .hover-blue:hover {
            color: #60a5fa !important;
        }

        .hover-blue:hover i {
            color: #60a5fa !important;
        }

        .hover-indigo:hover {
            color: #818cf8 !important;
        }

        .hover-indigo:hover i {
            color: #818cf8 !important;
        }

        .hover-pink:hover {
            color: #f472b6 !important;
        }

        .hover-pink:hover i {
            color: #f472b6 !important;
        }

        .hover-yellow:hover {
            color: #facc15 !important;
        }

        .hover-yellow:hover i {
            color: #facc15 !important;
        }
    </style>

    @if(Auth::guard('member')->check())
    {{-- ========================================== --}}
    {{-- 👤 เมนูสำหรับ สมาชิก (MEMBER)                  --}}
    {{-- ========================================== --}}
    <div>
        <a href="{{ route('welcome') }}" class="flex items-center justify-center mb-4">
            <x-application-logo class="block h-9 w-auto fill-current text-brand-500" />
            <span class="ml-2 text-lg font-bold text-white" x-show="sidebarOpen" x-transition>
                สมาชิกร้าน
            </span>
        </a>

        <div class="mb-4" :class="sidebarOpen ? 'flex justify-end' : 'flex justify-center'">
            <button @click="sidebarOpen = !sidebarOpen; $nextTick(() => lucide.createIcons())"
                class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-md">
                <i data-lucide="panel-left-close" class="w-5 h-5" x-show="sidebarOpen"></i>
                <i data-lucide="menu" class="w-5 h-5" x-show="!sidebarOpen"></i>
            </button>
        </div>

        <nav>
            <ul class="space-y-2 text-sm">
                <li>
                    <a href="{{ route('welcome') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'" title="หน้าแรก">
                        <i data-lucide="home" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>หน้าแรกเว็บไซต์</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('member.profile') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-brand-600 hover:text-white rounded-md transition-colors"
                        :class="sidebarOpen ? '' : 'justify-center'" title="ข้อมูลส่วนตัว">
                        <i data-lucide="user-cog" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>ข้อมูลส่วนตัว</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('member.history') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-brand-600 hover:text-white rounded-md transition-colors"
                        :class="sidebarOpen ? '' : 'justify-center'" title="ประวัติการเช่าชุด">
                        <i data-lucide="history" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>ประวัติการเช่าชุด</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="mt-auto border-t border-gray-700 pt-4" x-data="{ profileOpen: false }">
        <div class="relative">
            <div x-show="profileOpen" @click.away="profileOpen = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                class="absolute bottom-full left-0 right-0 mb-2 w-full bg-gray-800 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5"
                style="display: none;">

                <form method="POST" action="{{ route('member.logout') }}">
                    @csrf
                    <a href="{{ route('member.logout') }}"
                        class="block w-full text-left px-4 py-2 text-xs text-red-400 hover:bg-gray-700 hover:text-red-300"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <i data-lucide="log-out" class="inline w-4 h-4 mr-1"></i> ออกจากระบบ
                    </a>
                </form>
            </div>

            <button @click="profileOpen = !profileOpen"
                class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                :class="sidebarOpen ? '' : 'justify-center'">

                <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-transparent group-hover:ring-brand-400 transition-all">
                    {{ mb_strtoupper(mb_substr(Auth::guard('member')->user()->first_name ?? 'M', 0, 1)) }}
                </div>

                <div class="ml-3 text-left overflow-hidden" x-show="sidebarOpen" x-transition>
                    <span class="block text-xs font-medium text-white truncate w-32">{{ Auth::guard('member')->user()->first_name }}</span>
                    <span class="block text-[10px] text-yellow-400 truncate w-32">
                        ⭐ {{ number_format(Auth::guard('member')->user()->points ?? 0) }} แต้ม
                    </span>
                </div>

                <i data-lucide="chevron-up" class="w-4 h-4 ml-auto" x-show="sidebarOpen"></i>
            </button>
        </div>
    </div>

    @else
    {{-- ========================================== --}}
    {{-- 👔 เมนูสำหรับ พนักงานและผู้จัดการ (ADMIN/RECEPTION) --}}
    {{-- ========================================== --}}
    <div>
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center mb-4">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-200" />
            <span class="ml-2 text-lg font-bold text-white"
                x-show="sidebarOpen" x-transition>
                {{ Auth::user()->user_type_id == 1 ? 'ผู้จัดการ' : 'พนักงานต้อนรับ' }}
            </span>
        </a>

        <div class="mb-4" :class="sidebarOpen ? 'flex justify-end' : 'flex justify-center'">
            <button
                @click="sidebarOpen = !sidebarOpen; $nextTick(() => lucide.createIcons())"
                class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-md"
                :title="sidebarOpen ? 'ย่อเมนู' : 'ขยายเมนู'">
                <i data-lucide="panel-left-close" class="w-5 h-5" x-show="sidebarOpen"></i>
                <i data-lucide="menu" class="w-5 h-5" x-show="!sidebarOpen"></i>
            </button>
        </div>

        <nav>
            <ul class="space-y-2 text-sm">

                {{-- Dashboard --}}
                @if(Auth::user()->user_type_id == 1)
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'"
                        :title="sidebarOpen ? '' : 'แดชบอร์ด'">
                        <i data-lucide="home" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>แดชบอร์ด</span>
                    </a>
                </li>
                @endif

                @if(Auth::user()->user_type_id == 1)

                {{-- 🔵 กลุ่ม 1: จัดการผู้ใช้ (สีฟ้า) --}}
                <li x-data="{ open: false }" class="relative" @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false">
                    <button class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md hover-blue"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="users-round" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>จัดการผู้ใช้</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-auto" x-show="!open && sidebarOpen"></i>
                    </button>

                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute left-full top-0 w-64 pl-2"
                        style="display: none;">
                        <div class="bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-700">
                            <ul class="space-y-2 text-sm">
                                <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">ระบบสมาชิก</span></li>
                                <li><a href="{{ route('manager.members.index') }}" class="flyout-link"><i data-lucide="users" class="icon-size"></i> บัญชีสมาชิก</a></li>
                                <li><span class="block px-2 pt-3 pb-1 text-xs font-semibold text-gray-500 uppercase">ระบบพนักงาน</span></li>
                                <li><a href="{{ route('manager.users.index') }}" class="flyout-link"><i data-lucide="shield" class="icon-size"></i> บัญชีผู้ใช้</a></li>
                                <li><a href="{{ route('manager.user_types.index') }}" class="flyout-link"><i data-lucide="contact" class="icon-size"></i> ประเภทผู้ใช้</a></li>
                            </ul>
                        </div>
                    </div>
                </li>

                {{-- 🟣 กลุ่ม 2: จัดการสินค้า (สีม่วง/Indigo) --}}
                <li x-data="{ open: false }" class="relative" @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false">
                    <button class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md hover-indigo"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="package" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>จัดการสินค้า</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-auto" x-show="!open && sidebarOpen"></i>
                    </button>

                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute left-full top-0 w-64 pl-2"
                        style="display: none;">
                        <div class="bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-700">
                            <ul class="space-y-2 text-sm">
                                <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">คลังสินค้า</span></li>
                                <li><a href="{{ route('manager.items.index') }}" class="flyout-link"><i data-lucide="shopping-bag" class="icon-size"></i> รายการสินค้า</a></li>
                                <li><a href="{{ route('manager.accessories.index') }}" class="flyout-link"><i data-lucide="headphones" class="icon-size"></i> อุปกรณ์เสริม</a></li>
                                <li><a href="{{ route('manager.item_types.index') }}" class="flyout-link"><i data-lucide="list-tree" class="icon-size"></i> ประเภทสินค้า</a></li>
                                <li><a href="{{ route('manager.units.index') }}" class="flyout-link"><i data-lucide="box-select" class="icon-size"></i> หน่วยนับ</a></li>
                                <li><span class="block px-2 pt-3 pb-1 text-xs font-semibold text-gray-500 uppercase">การตลาด</span></li>
                                <li><a href="{{ route('manager.promotions.index') }}" class="flyout-link"><i data-lucide="percent-circle" class="icon-size"></i> โปรโมชั่น</a></li>
                            </ul>
                        </div>
                    </div>
                </li>

                {{-- 🩷 กลุ่ม 3: จัดการบริการ (สีชมพู) --}}
                <li x-data="{ open: false }" class="relative" @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false">
                    <button class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md hover-pink"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="briefcase" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>จัดการบริการ</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-auto" x-show="!open && sidebarOpen"></i>
                    </button>

                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute left-full top-0 w-64 pl-2"
                        style="display: none;">
                        <div class="bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-700">
                            <ul class="space-y-2 text-sm">
                                <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">พาร์ทเนอร์</span></li>
                                <li><a href="{{ route('manager.care_shops.index') }}" class="flyout-link"><i data-lucide="washing-machine" class="icon-size"></i> ร้านซักรีด/ดูแลชุด</a></li>
                                <li><a href="{{ route('manager.makeup_artists.index') }}" class="flyout-link"><i data-lucide="sparkles" class="icon-size"></i> ช่างแต่งหน้า</a></li>
                                <li><a href="{{ route('manager.photographers.index') }}" class="flyout-link"><i data-lucide="camera" class="icon-size"></i> ช่างภาพ</a></li>
                                <li><span class="block px-2 pt-1 pb-1 text-xs font-semibold text-gray-500 uppercase">แพ็คเกจ</span></li>
                                <li><a href="{{ route('manager.photographer_packages.index') }}" class="flyout-link"><i data-lucide="layers" class="icon-size"></i> แพ็คเกจถ่ายภาพ</a></li>
                                <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">บำรุงรักษา</span></li>
                                <li>
                                    <a href="{{ route('maintenance.index') }}" class="flyout-link">
                                        <i data-lucide="wrench" class="icon-size"></i> จัดการการซัก-ซ่อม
                                    </a>
                                </li>
                                <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">ค่าแรงช่าง</span></li>
                                <li>
                                    <a href="{{ route('service_costs.index') }}" class="flyout-link">
                                        <i data-lucide="coins" class="icon-size"></i> จัดการค่าแรงช่าง
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                {{-- ประวัติ (ของ Manager) --}}
                <li x-data="{ open: false }" class="relative" @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false">
                    <button class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="history" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>ประวัติ</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-auto" x-show="!open && sidebarOpen"></i>
                    </button>

                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute left-full top-0 w-64 pl-2"
                        style="display: none;">
                        <div class="bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-700">
                            <ul class="space-y-2 text-sm">
                                <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">ประวัติข้อมูล</span></li>
                                <li><a href="{{ route('reception.history') }}" class="flyout-link"><i data-lucide="clipboard-list" class="icon-size"></i> ประวัติการเช่า-คืน</a></li>
                                <li><a href="{{ route('reception.pointHistory') }}" class="flyout-link"><i data-lucide="star" class="icon-size"></i> ประวัติการใช้แต้ม</a></li>
                                <li><a href="{{ route('reception.paymentHistory') }}" class="flyout-link"><i data-lucide="banknote" class="icon-size"></i> ประวัติการชำระเงิน</a></li>
                                <li><a href="{{ route('reception.calendar') }}" class="flyout-link"><i data-lucide="calendar" class="icon-size"></i> ปฏิทินงานเช่า</a></li>
                            </ul>
                        </div>
                    </div>
                </li>

                @elseif(Auth::user()->user_type_id == 2)

                <li>
                    <a href="{{ route('reception.member.create') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-pink-600 hover:text-white rounded-md transition-colors"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>สมัครสมาชิก</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('reception.rental') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-green-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>เช่าชุด</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('reception.return') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-blue-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="corner-down-left" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>คืนชุด</span>
                    </a>
                </li>

                <li><a href="{{ route('reception.history') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-purple-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'"><i data-lucide="clipboard-list" class="w-5 h-5"></i><span class="ml-3" x-show="sidebarOpen" x-transition>จัดการการเช่า-คืน</span></a></li>
                <li>
                    <a href="{{ route('maintenance.index') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-purple-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="wrench" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>จัดการการซัก-ซ่อม</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('service_costs.index') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-purple-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="coins" class="w-5 h-5"></i><span class="ml-3" x-show="sidebarOpen" x-transition>จัดการค่าแรงช่าง</span>
                    </a>
                </li>

                <li x-data="{ open: false }" class="relative" @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false">
                    <button class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="history" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>ประวัติ</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-auto" x-show="!open && sidebarOpen"></i>
                    </button>
                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute left-full bottom-0 w-64 pl-2"
                        style="display: none;">
                        <div class="bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-700">
                            <ul class="space-y-2 text-sm">
                                <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">ประวัติข้อมูล</span></li>
                                <li><a href="{{ route('reception.pointHistory') }}" class="flyout-link"><i data-lucide="star" class="icon-size"></i> ประวัติการใช้แต้ม</a></li>
                                <li><a href="{{ route('reception.paymentHistory') }}" class="flyout-link"><i data-lucide="banknote" class="icon-size"></i> ประวัติการชำระเงิน</a></li>
                                <li><a href="{{ route('reception.calendar') }}" class="flyout-link"><i data-lucide="calendar" class="icon-size"></i> ปฏิทินงานเช่า</a></li>
                            </ul>
                        </div>
                    </div>
                </li>

                @endif
            </ul>
        </nav>
    </div>

    <div class="mt-auto border-t border-gray-700 pt-4" x-data="{ profileOpen: false }">
        <div class="relative">
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

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                        class="block w-full text-left px-4 py-2 text-xs text-gray-300 hover:bg-gray-700 hover:text-white"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('ออกจากระบบ') }}
                    </a>
                </form>
            </div>

            <button @click="profileOpen = !profileOpen"
                class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                :class="sidebarOpen ? '' : 'justify-center'"
                :title="sidebarOpen ? '' : '{{ Auth::user()->name }}'">

                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-gray-700 to-gray-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-transparent group-hover:ring-gray-500 transition-all">
                    {{ mb_strtoupper(mb_substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
                </div>

                <div class="ml-3 text-left overflow-hidden" x-show="sidebarOpen" x-transition>
                    <span class="block text-xs font-medium text-white truncate w-32">{{ Auth::user()->first_name }}</span>
                    <span class="block text-[10px] text-gray-400 truncate w-32">
                        {{ Auth::user()->user_type_id == 1 ? 'ผู้จัดการ' : 'พนักงาน' }}
                    </span>
                </div>

                <i data-lucide="chevron-up" class="w-4 h-4 ml-auto" x-show="sidebarOpen"></i>
            </button>
        </div>
    </div>
    @endif
</aside>