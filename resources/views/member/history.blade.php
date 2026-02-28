@component('layouts.app')
<div class="pt-24 pb-12 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="flex items-center gap-3 mb-8">
        <i data-lucide="history" class="w-8 h-8 text-brand-600"></i>
        <h1 class="text-3xl font-bold text-gray-900">ประวัติการเช่าชุด</h1>
    </div>

    @if($rentals->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="shopping-bag" class="w-10 h-10 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">ยังไม่มีประวัติการเช่า</h3>
        <p class="text-gray-500 mb-6">คุณยังไม่เคยทำรายการเช่าชุดกับทางร้าน</p>
        <a href="{{ route('catalog') }}" class="inline-block bg-brand-600 text-white font-bold py-2 px-6 rounded-full hover:bg-brand-700 transition">
            ดูแคตตาล็อกชุด
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($rentals as $rental)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 border-b border-gray-100 pb-4 mb-4">
                <div>
                    <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">บิล #{{ $rental->rental_id }}</span>
                    <h3 class="font-bold text-gray-900 mt-2">วันที่รับ: {{ \Carbon\Carbon::parse($rental->rental_date)->addYears(543)->format('d M Y') }}</h3>
                    <p class="text-sm text-gray-500">กำหนดคืน: {{ \Carbon\Carbon::parse($rental->return_date)->addYears(543)->format('d M Y') }}</p>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-lg font-bold text-brand-600">฿{{ number_format($rental->total_amount) }}</p>

                    {{-- แสดงป้ายสถานะ --}}
                    @if($rental->status == 'pending')
                    <span class="inline-block mt-1 text-xs font-bold text-yellow-700 bg-yellow-100 px-2 py-1 rounded-full">รอรับชุด/รอชำระเงิน</span>
                    @elseif($rental->status == 'rented')
                    <span class="inline-block mt-1 text-xs font-bold text-blue-700 bg-blue-100 px-2 py-1 rounded-full">กำลังเช่า</span>
                    @elseif($rental->status == 'returned')
                    <span class="inline-block mt-1 text-xs font-bold text-green-700 bg-green-100 px-2 py-1 rounded-full">คืนชุดแล้ว</span>
                    @else
                    <span class="inline-block mt-1 text-xs font-bold text-red-700 bg-red-100 px-2 py-1 rounded-full">ยกเลิก</span>
                    @endif
                </div>
            </div>

            <div class="space-y-3">
                @foreach($rental->items as $detail)
                <div class="flex items-center gap-4">
                    <div class="w-12 h-16 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                        @if(isset($detail->item->images[0]))
                        <img src="{{ asset('storage/' . str_replace('public/', '', $detail->item->images[0]->path)) }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400"><i data-lucide="image" class="w-5 h-5"></i></div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $detail->item->item_name ?? 'อุปกรณ์เสริม' }}</p>
                        <p class="text-xs text-gray-500">จำนวน: {{ $detail->quantity }} รายการ</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $rentals->links() }}
    </div>
    @endif
</div>
@endcomponent