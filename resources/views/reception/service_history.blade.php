<x-app-layout>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #receipt-modal,
            #receipt-modal * {
                visibility: visible;
            }

            #receipt-modal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                background: white;
                box-shadow: none !important;
                border: none !important;
            }

            .no-print,
            button {
                display: none !important;
            }

            .shadow-2xl,
            .bg-gray-800 {
                box-shadow: none !important;
            }
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-pink-100 rounded-lg text-pink-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
            </div>
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ประวัติการบริการ (Services History)
            </h2>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showModal: false, selectedService: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🔍 Filter Bar --}}
            <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 no-print">
                <form action="{{ route('reception.serviceHistory') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:flex-grow">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">ค้นหา (ลูกค้า / ชื่อช่าง)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="พิมพ์ชื่อ..."
                                class="w-full pl-10 rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500 h-11 transition-all">
                        </div>
                    </div>
                    <div class="w-full md:w-48">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">ประเภทบริการ</label>
                        <select name="type" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm h-11 focus:border-pink-500 focus:ring-pink-500 cursor-pointer">
                            <option value="all">ทั้งหมด</option>
                            <option value="makeup" {{ request('type') == 'makeup' ? 'selected' : '' }}>💄 แต่งหน้า</option>
                            <option value="photo" {{ request('type') == 'photo' ? 'selected' : '' }}>📷 ถ่ายภาพ</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full md:w-auto px-6 h-11 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                        </svg>
                        กรองข้อมูล
                    </button>
                    @if(request()->has('type') || request()->has('search'))
                    <a href="{{ route('reception.serviceHistory') }}" class="w-full md:w-auto px-4 h-11 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition flex items-center justify-center">
                        ล้างค่า
                    </a>
                    @endif
                </form>
            </div>

            {{-- 📋 Data Table --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden no-print">
                <div class="overflow-x-auto">
                    @if($services->isEmpty())
                    <div class="text-center py-16 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-lg font-medium">ไม่พบประวัติการใช้บริการ</p>
                        <p class="text-sm">ลองเปลี่ยนเงื่อนไขการค้นหาดูนะครับ</p>
                    </div>
                    @else
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reference ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ลูกค้า</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">วันที่ทำรายการ</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">รายละเอียดบริการ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach($services as $service)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150 group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">#{{ $service->rental_id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-tr from-pink-400 to-purple-500 text-white flex items-center justify-center text-xs font-bold">
                                            {{ mb_substr($service->member ? $service->member->first_name : ($service->description ? 'G' : '?'), 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $service->member ? $service->member->first_name . ' ' . $service->member->last_name : 'ลูกค้าทั่วไป (Guest)' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $service->member ? $service->member->tel : ($service->description ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex flex-col">
                                        <span class="font-medium">
                                            {{ \Carbon\Carbon::parse($service->rental_date)->addYears(543)->locale('th')->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                </td>

                                {{-- ✅ รายละเอียดบริการ (UI เดิมที่ต้องการ) --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="space-y-3">
                                        @if($service->makeup_id)
                                        <div class="flex items-start gap-3">
                                            <div class="p-1.5 bg-pink-50 text-pink-500 rounded-lg mt-0.5 border border-pink-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                                    ช่าง {{ $service->makeupArtist->first_name ?? '-' }}
                                                </div>
                                                <div class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded inline-block mt-1">
                                                    {{ number_format($service->makeupArtist->price ?? 0) }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if($service->photographer_id)
                                        <div class="flex items-start gap-3">
                                            <div class="p-1.5 bg-blue-50 text-blue-500 rounded-lg mt-0.5 border border-blue-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                                    ช่าง {{ $service->photographer->first_name ?? '-' }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $service->photographerPackage->package_name ?? '-' }}</div>
                                                <div class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded inline-block mt-1">
                                                    {{ number_format($service->photographerPackage->price ?? 0) }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                                    @if($service->status == 'returned')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        สำเร็จ
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5 animate-pulse"></span>
                                        กำลังดำเนินการ
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button @click="showModal = true; selectedService = {{ Js::from($service) }}"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        ใบเสร็จ
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                @if($services->hasPages())
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $services->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- 🧾 RECEIPT MODAL --}}
        <div x-show="showModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div id="receipt-modal" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">

                    <div class="bg-gray-900 text-white p-6 relative">
                        <div class="absolute top-4 right-4 cursor-pointer opacity-70 hover:opacity-100 no-print" @click="showModal = false">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-white tracking-wide">ใบเสร็จรับเงิน</h3>
                                <p class="text-xs text-gray-400 uppercase tracking-widest mt-1">RECEIPT / TAX INVOICE</p>
                            </div>
                            <div class="text-right">
                                <div class="bg-white/10 px-2 py-1 rounded text-xs font-mono">#<span x-text="selectedService?.rental_id"></span></div>
                            </div>
                        </div>
                        <div class="mt-6 border-t border-gray-700 pt-4 flex justify-between items-end">
                            <div>
                                <h4 class="font-bold text-sm">ร้านเช่าชุด Watakacha</h4>
                                <p class="text-xs text-gray-400 mt-0.5">สาขาลำพูน โทร. 081-234-5678</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">วันที่ทำรายการ</p>
                                <p class="text-sm font-medium" x-text="selectedService ? new Date(selectedService.created_at).toLocaleDateString('th-TH') : '-'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-white relative">
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none">
                            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L1 21h22L12 2zm0 3.5L18.5 19H5.5L12 5.5z" />
                            </svg>
                        </div>

                        <div class="flex justify-between mb-6 pb-4 border-b border-gray-100">
                            <div class="text-sm">
                                <p class="text-xs text-gray-500 font-bold uppercase mb-1">ลูกค้า (Customer)</p>
                                <template x-if="selectedService?.member">
                                    <div>
                                        <p class="font-bold text-gray-800" x-text="selectedService.member.first_name + ' ' + selectedService.member.last_name"></p>
                                        <p class="text-gray-500 text-xs mt-0.5" x-text="'Tel: ' + selectedService.member.tel"></p>
                                    </div>
                                </template>
                                <template x-if="!selectedService?.member">
                                    <div>
                                        <p class="font-bold text-gray-800">ลูกค้าทั่วไป</p>
                                        <p class="text-gray-500 text-xs mt-0.5" x-text="selectedService?.description || '-'"></p>
                                    </div>
                                </template>
                            </div>
                            <div class="text-right text-sm">
                                <p class="text-xs text-gray-500 font-bold uppercase mb-1">วันที่ใช้บริการ</p>
                                <p class="text-gray-800" x-text="selectedService ? new Date(selectedService.rental_date).toLocaleDateString('th-TH') : '-'"></p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-gray-500 border-b border-gray-200">
                                        <th class="text-left py-2 font-medium text-xs uppercase">รายการ</th>
                                        <th class="text-right py-2 font-medium text-xs uppercase w-20">ค่าบริการ</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    <template x-if="selectedService?.makeup_id">
                                        <tr class="border-b border-gray-50">
                                            <td class="py-2 pr-2">
                                                <div class="font-medium" x-text="'แต่งหน้า โดย ' + selectedService.makeup_artist?.first_name"></div>
                                            </td>
                                            <td class="text-right py-2 align-top font-medium" x-text="new Intl.NumberFormat().format(selectedService.makeup_artist?.price || 0)"></td>
                                        </tr>
                                    </template>
                                    <template x-if="selectedService?.photographer_id">
                                        <tr class="border-b border-gray-50">
                                            <td class="py-2 pr-2">
                                                <div class="font-medium" x-text="'ถ่ายภาพ โดย ' + selectedService.photographer?.first_name"></div>
                                                <div class="text-[10px] text-gray-400" x-text="selectedService.photographer_package?.package_name"></div>
                                            </td>
                                            <td class="text-right py-2 align-top font-medium" x-text="new Intl.NumberFormat().format(selectedService.photographer_package?.price || 0)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- ตัดยอดสุทธิใน Body ออก --}}
                        {{-- <div class="flex justify-end mb-6">...</div> --}}

                        <div class="text-center pt-4 border-t border-dashed border-gray-200">
                            <div class="flex justify-center mb-3">
                                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${encodeURIComponent('ServiceID:' + selectedService?.rental_id)}`" alt="QR Code" class="w-16 h-16 mix-blend-multiply opacity-80 border p-1 rounded bg-white">
                            </div>
                            <p class="font-bold text-gray-800 text-xs">ขอบคุณที่ใช้บริการ</p>
                            <p class="text-[10px] text-gray-400 mt-1">เอกสารนี้ออกโดยระบบอัตโนมัติ</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex gap-3 no-print border-t border-gray-100">
                        <button @click="window.print()" class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg shadow-sm hover:bg-gray-50 transition flex items-center justify-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>พิมพ์ใบเสร็จ</button>
                        <button @click="showModal = false" class="flex-1 py-2.5 bg-gray-900 text-white font-bold rounded-lg shadow hover:bg-black transition">ปิดหน้าต่าง</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>