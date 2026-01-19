<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-indigo-400 leading-tight flex items-center gap-2">
            <i data-lucide="users" class="w-6 h-6"></i>
            บัญชีสมาชิก (Members)
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filter --}}
            <div class="mb-6 bg-gray-800 p-4 rounded-xl shadow border border-gray-700 flex justify-between items-center gap-4">
                <form method="GET" action="{{ route('manager.members.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาสมาชิก..." class="w-full pl-10 pr-4 py-2 bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i data-lucide="search" class="w-4 h-4"></i></div>
                    </div>
                    <button type="submit" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg"><i data-lucide="search" class="w-4 h-4"></i></button>
                </form>
                <button onclick="toggleModal('addMemberModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มสมาชิก
                </button>
            </div>

            <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase w-16">รหัส</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ชื่อผู้ใช้ / ชื่อ-นามสกุล</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ติดต่อ (Tel / Line)</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">แต้มสะสม</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-indigo-400 uppercase">สถานะ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                        @forelse($members as $member)
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono">#{{ $member->member_id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-indigo-300 text-base mb-1" title="Member">{{ $member->username }}</div>
                                <div class="text-sm text-gray-400">{{ $member->first_name }} {{ $member->last_name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                {{-- เบอร์โทร --}}
                                <div class="flex items-center gap-2 mb-1.5">
                                    <i data-lucide="phone" class="w-3.5 h-3.5 text-gray-500"></i>
                                    <span class="text-gray-300">{{ $member->tel }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mb-1.5 ml-5">{{ $member->email }}</div>

                                {{-- สถานะ LINE (เพิ่มใหม่) --}}
                                @if($member->line_user_id)
                                {{-- กรณีเชื่อมต่อแล้ว --}}
                                <div class="flex items-center gap-1.5 text-green-400 bg-green-900/20 px-2 py-0.5 rounded-md w-fit border border-green-900/30" title="ผูกบัญชี LINE แล้ว">
                                    <i data-lucide="link" class="w-3 h-3"></i>
                                    <span class="text-[10px] font-medium">เชื่อม LINE แล้ว</span>
                                </div>
                                @else
                                {{-- กรณีไม่ได้เชื่อมต่อ --}}
                                <div class="flex items-center gap-1.5 text-gray-500 bg-gray-700/30 px-2 py-0.5 rounded-md w-fit border border-gray-700" title="ลูกค้ายังไม่เคยกดเข้าใช้งานผ่าน LINE">
                                    <i data-lucide="unlink" class="w-3 h-3"></i>
                                    <span class="text-[10px]">ยังไม่เชื่อม LINE</span>
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-yellow-400 font-bold font-mono">{{ number_format($member->points) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $member->status == 'active' ? 'bg-green-900 text-green-300 border border-green-700' : 'bg-red-900 text-red-300 border border-red-700' }}">
                                    {{ $member->status == 'active' ? 'กำลังใช้งาน' : 'ระงับการใช้งาน' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                <button onclick="toggleModal('updateMemberModal-{{ $member->member_id }}', true)" class="text-blue-400 hover:text-blue-300 mr-2"><i data-lucide="file-pen-line" class="w-5 h-5"></i></button>
                                <form id="delete-form-{{ $member->member_id }}"
                                    action="{{ route('manager.members.destroy', $member->member_id) }}"
                                    method="POST"
                                    class="inline-block">

                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                        onclick="confirmDelete('delete-form-{{ $member->member_id }}')"
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
                @if($members->hasPages()) <div class="px-6 py-4 bg-gray-800 border-t border-gray-700">{{ $members->links() }}</div> @endif
            </div>

            @include('manager.modals.add-member')
            @foreach($members as $member) @include('manager.modals.update-member', ['member' => $member]) @endforeach
        </div>
    </div>
</x-app-layout>