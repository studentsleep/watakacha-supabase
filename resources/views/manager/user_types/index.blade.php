<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-indigo-400 leading-tight flex items-center gap-2">
            <i data-lucide="contact" class="w-6 h-6"></i>
            ประเภทผู้ใช้ (User Types)
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-gray-800 p-4 rounded-xl shadow border border-gray-700 flex justify-between items-center gap-4">
                <form method="GET" action="{{ route('manager.user_types.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหา..." class="pl-4 py-2 bg-gray-700 border-gray-600 text-white rounded-lg">
                    <button type="submit" class="px-3 py-2 bg-gray-700 text-white rounded-lg"><i data-lucide="search" class="w-4 h-4"></i></button>
                </form>
                <button onclick="toggleModal('addUserTypeModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มประเภท
                </button>
            </div>

            <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase w-16">รหัส</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">ชื่อประเภท</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-400 uppercase">รายละเอียด</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-400 uppercase">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                        @forelse($user_types as $type)
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono">#{{ $type->user_type_id }}</td>
                            <td class="px-6 py-4 font-bold text-indigo-300 text-base" title="Role">{{ $type->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400 max-w-sm truncate">{{ $type->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-medium">
                                <button onclick="toggleModal('updateUserTypeModal-{{ $type->user_type_id }}', true)" class="text-blue-400 hover:text-blue-300 mr-2"><i data-lucide="file-pen-line" class="w-5 h-5"></i></button>
                                <form id="delete-form-{{ $type->user_type_id }}"

                                    action="{{ route('manager.user_types.destroy', $type->user_type_id) }}"

                                    method="POST"

                                    class="inline-block">



                                    @csrf

                                    @method('DELETE')



                                    <button type="button"

                                        onclick="confirmDelete('delete-form-{{ $type->user_type_id }}')"

                                        class="text-red-400 hover:text-red-300">

                                        <i data-lucide="trash-2" class="w-5 h-5"></i>

                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">ไม่พบข้อมูล</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($user_types->hasPages()) <div class="px-6 py-4 bg-gray-800 border-t border-gray-700">{{ $user_types->links() }}</div> @endif
            </div>
            @include('manager.modals.add-user-type')
            @foreach($user_types as $type) @include('manager.modals.update-user-type', ['type' => $type]) @endforeach
        </div>
    </div>
</x-app-layout>