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
    </style>

    <div>
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center mb-4">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-200" />
            <span class="ml-2 text-lg font-bold text-white"
                x-show="sidebarOpen" x-transition>
                {{ Auth::user()->user_type_id == 1 ? 'Manager' : 'Reception' }}
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
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'"
                        :title="sidebarOpen ? '' : 'แดชบอร์ด'">
                        <i data-lucide="home" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>แดชบอร์ด</span>
                    </a>
                </li>

                @if(Auth::user()->user_type_id == 1)
                
                <li x-data="{ open: false }" class="relative" @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false">
                    <button class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
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
                                <li><a href="{{ route('manager.index', ['table' => 'member_accounts']) }}" class="flyout-link"><i data-lucide="users" class="icon-size"></i> บัญชีสมาชิก</a></li>
                                <li><span class="block px-2 pt-3 pb-1 text-xs font-semibold text-gray-500 uppercase">ระบบพนักงาน</span></li>
                                <li><a href="{{ route('manager.index', ['table' => 'users']) }}" class="flyout-link"><i data-lucide="shield" class="icon-size"></i> บัญชีผู้ใช้</a></li>
                                <li><a href="{{ route('manager.index', ['table' => 'user_types']) }}" class="flyout-link"><i data-lucide="contact" class="icon-size"></i> ประเภทผู้ใช้</a></li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li x-data="{ open: false }" class="relative" @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false">
                    <button class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
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
                                <li><a href="{{ route('manager.index', ['table' => 'items']) }}" class="flyout-link"><i data-lucide="shopping-bag" class="icon-size"></i> รายการสินค้า</a></li>
                                <li><a href="{{ route('manager.index', ['table' => 'item_types']) }}" class="flyout-link"><i data-lucide="list-tree" class="icon-size"></i> ประเภทสินค้า</a></li>
                                <li><a href="{{ route('manager.index', ['table' => 'item_units']) }}" class="flyout-link"><i data-lucide="box-select" class="icon-size"></i> หน่วยนับ</a></li>
                                <li><span class="block px-2 pt-3 pb-1 text-xs font-semibold text-gray-500 uppercase">การตลาด</span></li>
                                <li><a href="{{ route('manager.index', ['table' => 'promotions']) }}" class="flyout-link"><i data-lucide="percent-circle" class="icon-size"></i> โปรโมชั่น</a></li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li x-data="{ open: false }" class="relative" @mouseenter="open = true; $nextTick(() => lucide.createIcons())" @mouseleave="open = false">
                    <button class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
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
                                <li><a href="{{ route('manager.index', ['table' => 'care_shops']) }}" class="flyout-link"><i data-lucide="washing-machine" class="icon-size"></i> ร้านซักรีด/ดูแลชุด</a></li>
                                <li><a href="{{ route('manager.index', ['table' => 'makeup_artists']) }}" class="flyout-link"><i data-lucide="sparkles" class="icon-size"></i> ช่างแต่งหน้า</a></li>
                                <li><a href="{{ route('manager.index', ['table' => 'photographers']) }}" class="flyout-link"><i data-lucide="camera" class="icon-size"></i> ช่างภาพ</a></li>
                                <li><span class="block px-2 pt-3 pb-1 text-xs font-semibold text-gray-500 uppercase">แพ็คเกจ</span></li>
                                <li><a href="{{ route('manager.index', ['table' => 'photographer_packages']) }}" class="flyout-link"><i data-lucide="layers" class="icon-size"></i> แพ็คเกจถ่ายภาพ</a></li>
                            </ul>
                        </div>
                    </div>
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
                        class="absolute left-full top-0 w-64 pl-2"
                        style="display: none;">
                        <div class="bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-700">
                            <ul class="space-y-2 text-sm">
                                <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">ประวัติข้อมูล</span></li>
                                <li><a href="{{ route('reception.history') }}" class="flyout-link"><i data-lucide="clipboard-list" class="icon-size"></i> ประวัติการเช่า-คืน</a></li>
                                <li><a href="{{ route('manager.index', ['table' => 'point_transactions']) }}" class="flyout-link"><i data-lucide="star" class="icon-size"></i> ประวัติการใช้แต้ม</a></li>
                                <li><a href="{{ route('reception.serviceHistory') }}" class="flyout-link"><i data-lucide="sparkles" class="icon-size"></i> ประวัติการบริการ</a></li>
                            </ul>
                        </div>
                    </div>
                </li>

                @elseif(Auth::user()->user_type_id == 2)
                
                <li>
                    <a href="{{ route('reception.rental') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-green-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>เช่าชุด (Rental)</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('reception.return') }}"
                        class="flex items-center px-4 py-2 text-gray-300 hover:bg-blue-700 hover:text-white rounded-md"
                        :class="sidebarOpen ? '' : 'justify-center'">
                        <i data-lucide="corner-down-left" class="w-5 h-5"></i>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>คืนชุด (Return)</span>
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
                         class="absolute left-full top-0 w-64 pl-2"
                         style="display: none;">
                        <div class="bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-700">
                            <ul class="space-y-2 text-sm">
                                <li><span class="block px-2 py-1 text-xs font-semibold text-gray-500 uppercase">ประวัติข้อมูล</span></li>
                                <li><a href="{{ route('reception.history') }}" class="flyout-link"><i data-lucide="clipboard-list" class="icon-size"></i> ประวัติการเช่า-คืน</a></li>
                                <li><a href="{{ route('manager.index', ['table' => 'point_transactions']) }}" class="flyout-link"><i data-lucide="star" class="icon-size"></i> ประวัติการใช้แต้ม</a></li>
                                <li><a href="{{ route('reception.serviceHistory') }}" class="flyout-link"><i data-lucide="sparkles" class="icon-size"></i> ประวัติการบริการ</a></li>
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

                <!-- <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-gray-300 hover:bg-gray-700 hover:text-white">
                    {{ __('Profile') }}
                </a> -->

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                        class="block w-full text-left px-4 py-2 text-xs text-gray-300 hover:bg-gray-700 hover:text-white"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>

            <button @click="profileOpen = !profileOpen"
                class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
                :class="sidebarOpen ? '' : 'justify-center'"
                :title="sidebarOpen ? '' : '{{ Auth::user()->name }}'">

                <i data-lucide="user-circle" class="w-6 h-6"></i>

                <div class="ml-3 text-left" x-show="sidebarOpen" x-transition>
                    <span class="text-xs font-medium">{{ Auth::user()->name }}</span>
                    <span class="ml-1 text-xs text-gray-400" x-show="sidebarOpen" x-transition>
                        {{ mb_strtoupper(mb_substr(Auth::user()->first_name ?? '', 0, 1, 'UTF-8'), 'UTF-8') }}{{ mb_strtoupper(mb_substr(Auth::user()->last_name ?? '', 0, 1, 'UTF-8'), 'UTF-8') }}
                    </span>
                </div>

                <i data-lucide="chevron-up" class="w-4 h-4 ml-auto" x-show="sidebarOpen"></i>
            </button>
        </div>
    </div>
</aside>