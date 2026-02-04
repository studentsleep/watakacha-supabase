<x-layouts.frontend>
    <div x-init="scrolled = true"></div>
    <div class="pt-24 pb-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">โปรโมชั่นสุดพิเศษ</h1>
                <div class="w-24 h-1.5 bg-brand-500 mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($promotions as $promo)
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                    <div class="bg-gradient-to-r from-brand-500 to-purple-600 p-8 text-red-500 text-center">
                        <p class="font-medium opacity-90 tracking-widest mt-1 text-black p-2">ส่วนลด</p>
                        <h2 class="text-4xl font-extrabold">{{ number_format($promo->discount_value) }} <span class="text-2xl">{{ $promo->discount_type == 'percentage' ? '%' : '฿' }}</span></h2>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $promo->promotion_name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $promo->description }}</p>
                        <div class="flex items-center justify-center text-sm text-gray-500 mb-6 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <i data-lucide="calendar-clock" class="w-4 h-4 mr-2 text-brand-500"></i> หมดเขต: <span class="font-bold ml-1 text-gray-700">{{ \Carbon\Carbon::parse($promo->end_date)->translatedFormat('d M Y') }}</span>
                        </div>
                        <a href="{{ route('catalog') }}" class="block w-full py-3 text-center bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition shadow-lg">ใช้โปรโมชั่นนี้</a>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-24">
                    <h3 class="text-xl font-bold text-gray-900">ยังไม่มีโปรโมชั่น</h3>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.frontend>