<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-indigo-400 leading-tight flex items-center gap-2">
            <i data-lucide="shield" class="w-6 h-6"></i>
            พนักงาน (Users)
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filter --}}
            <div class="mb-6 bg-gray-800 p-4 rounded-xl shadow border border-gray-700 flex justify-between items-center gap-4">
                <form method="GET" action="{{ route('manager.users.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาพนักงาน..." class="w-full pl-10 pr-4 py-2 bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i data-lucide="search" class="w-4 h-4"></i></div>
                    </div>
                    <button type="submit" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg"><i data-lucide="search" class="w-4 h-4"></i></button>
                </form>
                <button onclick="toggleModal('addUserModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มพนักงาน
                </button>
            </div>

            <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase w-16">รหัส</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ชื่อผู้ใช้ / ชื่อจริง</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ติดต่อ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ตำแหน่ง</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-indigo-400 uppercase">สถานะ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono">#{{ $user->user_id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-indigo-300 text-base mb-1" title="Username">{{ $user->username }}</div>
                                <div class="text-sm text-gray-400">{{ $user->first_name }} {{ $user->last_name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                <div>{{ $user->email }}</div>
                                <div class="text-xs text-gray-500">{{ $user->tel ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                // 1. รับค่าและแปลงเป็นตัวพิมพ์เล็ก
                                $typeName = $user->userType->name ?? 'N/A';
                                $checkName = strtolower($typeName);

                                // 2. ตั้งค่า Default (กรณีทั่วไปให้เป็นสีเทา)
                                $label = $typeName;
                                $colorClass = 'bg-gray-700 text-gray-300 border-gray-600';

                                // 3. ตรวจสอบเงื่อนไขเพื่อเปลี่ยนสีและข้อความ
                                switch ($checkName) {
                                case 'manager':
                                $label = 'ผู้จัดการ';
                                // ✨ สีทอง (พื้นหลังเหลืองเข้ม / ตัวหนังสือเหลืองทอง / ขอบเหลือง)
                                $colorClass = 'bg-yellow-900/40 text-yellow-400 border-yellow-600';
                                break;

                                case 'reception':
                                $label = 'พนักงานต้อนรับ';
                                // ถ้าอยากให้พนักงานต้อนรับสีต่างด้วย (เช่น สีฟ้า) แก้บรรทัดล่างได้ครับ
                                $colorClass = 'bg-blue-900/30 text-blue-300 border-blue-700';
                                break;
                                }
                                @endphp

                                {{-- แสดงผล --}}
                                <span class="px-2 py-1 rounded text-xs border {{ $colorClass }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $user->status == 'active' ? 'bg-green-900 text-green-300 border border-green-700' : 'bg-red-900 text-red-300 border border-red-700' }}">
                                    {{ $user->status == 'active' ? 'กำลังใช้งาน' : 'ระงับการใช้งาน' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                <button onclick="toggleModal('updateUserModal-{{ $user->user_id }}', true)" class="text-blue-400 hover:text-blue-300 mr-2"><i data-lucide="file-pen-line" class="w-5 h-5"></i></button>
                                <form id="delete-form-{{ $user->user_id }}"
                                    action="{{ route('manager.users.destroy', $user->user_id) }}"
                                    method="POST"
                                    class="inline-block">

                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                        onclick="confirmDelete('delete-form-{{ $user->user_id }}')"
                                        class="text-red-400 hover:text-red-300">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">ไม่พบข้อมูล</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($users->hasPages()) <div class="px-6 py-4 bg-gray-800 border-t border-gray-700">{{ $users->links() }}</div> @endif
            </div>

            @include('manager.modals.add-user')
            @foreach($users as $user) @include('manager.modals.update-user', ['user' => $user]) @endforeach
        </div>
    </div>
</x-app-layout>