<x-layouts.frontend>
    {{-- Background Decoration --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-brand-500/10 rounded-full blur-3xl opacity-50 mix-blend-multiply"></div>
        <div class="absolute bottom-0 right-0 w-[800px] h-[600px] bg-purple-500/10 rounded-full blur-3xl opacity-50 mix-blend-multiply"></div>
    </div>

    <div x-init="scrolled = true"></div>

    <div class="pt-32 pb-20 min-h-screen relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header Section --}}
            <div class="text-center mb-16 relative">
                <span class="text-brand-600 font-semibold tracking-wider uppercase text-sm mb-2 block">Get in Touch</span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight">
                    ติดต่อ <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-purple-600">เรา</span>
                </h1>
                <div class="w-32 h-1.5 bg-gradient-to-r from-brand-500 to-purple-500 mx-auto rounded-full shadow-lg"></div>
                <p class="mt-4 text-gray-500 max-w-2xl mx-auto text-lg">
                    พร้อมเนรมิตวันสำคัญของคุณให้สมบูรณ์แบบ ติดต่อสอบถามรายละเอียดหรือนัดหมายเข้าชมสตูดิโอ
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

                {{-- Left Column: Info & Social --}}
                <div class="lg:col-span-5 space-y-8">

                    {{-- Card 1: Address & Time --}}
                    <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-gray-100 hover:shadow-2xl transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-bl-[100px] -z-0 transition-transform group-hover:scale-110"></div>

                        <div class="relative z-10 space-y-8">
                            {{-- Address --}}
                            <div class="flex items-start gap-5">
                                <div class="flex-shrink-0 w-14 h-14 bg-white border border-gray-100 shadow-md rounded-2xl flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-colors duration-300">
                                    <i data-lucide="map-pin" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">ที่อยู่สตูดิโอ</h3>
                                    <p class="text-gray-600 leading-relaxed text-base">
                                        499/130 หมู่บ้านรุ่งเรือง ซ. 8 <br>
                                        อำเภอสันทราย เชียงใหม่ 50210
                                    </p>
                                    <a href="https://maps.google.com" target="_blank" class="inline-flex items-center gap-1 mt-2 text-sm font-semibold text-brand-600 hover:text-brand-700 hover:underline">
                                        ดูแผนที่ Google Maps <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- Working Hours --}}
                            <div class="flex items-start gap-5">
                                <div class="flex-shrink-0 w-14 h-14 bg-white border border-gray-100 shadow-md rounded-2xl flex items-center justify-center text-orange-500 group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300">
                                    <i data-lucide="clock" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">เวลาทำการ</h3>
                                    <p class="text-gray-600 font-medium">เปิดทุกวัน</p>
                                    <p class="text-brand-600 font-bold text-lg">09:00 - 20:00 น.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Contact Channels --}}
                    <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-gray-100 hover:shadow-2xl transition-all duration-300">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i data-lucide="phone-call" class="w-6 h-6 text-brand-500"></i> ช่องทางติดต่อด่วน
                        </h3>
                        <ul class="space-y-4">
                            <li class="group flex items-center justify-between p-4 bg-gray-50 hover:bg-white hover:shadow-lg hover:scale-[1.02] border border-transparent hover:border-brand-100 rounded-2xl transition-all duration-300 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm text-gray-700 group-hover:text-brand-600"><i data-lucide="phone" class="w-5 h-5"></i></div>
                                    <span class="font-medium text-gray-700">โทรศัพท์</span>
                                </div>
                                <span class="text-gray-900 font-bold text-lg tracking-wide">082 280 6989</span>
                            </li>
                            <li class="group flex items-center justify-between p-4 bg-[#06C755]/10 hover:bg-[#06C755] hover:shadow-lg hover:scale-[1.02] hover:text-white rounded-2xl transition-all duration-300 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm text-[#06C755]"><i data-lucide="message-circle" class="w-5 h-5"></i></div>
                                    <span class="font-medium text-[#06C755] group-hover:text-white">LINE ID</span>
                                </div>
                                <span class="text-[#06C755] group-hover:text-white font-bold text-lg">@watakacha</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Card 3: Social Media (Highlight) --}}
                    <div class="relative overflow-hidden rounded-[2rem] shadow-2xl group">
                        {{-- Gradient Background --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-600 via-purple-600 to-pink-600 opacity-90 transition-opacity duration-300"></div>
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-20"></div>

                        <div class="relative z-10 p-8 text-center">
                            <h3 class="text-2xl font-bold text-white mb-2">ติดตามผลงานของเรา</h3>
                            <p class="text-white/80 mb-8 text-sm">อัปเดตเทรนด์และผลงานใหม่ๆ ได้ที่โซเชียลมีเดีย</p>

                            <div class="flex justify-center items-center gap-3 sm:gap-4 flex-wrap">
                                {{-- TikTok --}}
                                <a href="https://www.tiktok.com/@watakachastudio" target="_blank"
                                    class="group/icon relative w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white hover:bg-black hover:border-black hover:scale-110 transition-all duration-300 shadow-xl" title="TikTok">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 sm:w-6 sm:h-6">
                                        <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" />
                                    </svg>
                                </a>

                                {{-- Facebook --}}
                                <a href="https://www.facebook.com/WATAKACHA/" target="_blank"
                                    class="group/icon relative w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white hover:bg-[#1877F2] hover:border-[#1877F2] hover:scale-110 transition-all duration-300 shadow-xl" title="Facebook">
                                    <i data-lucide="facebook" class="w-5 h-5 sm:w-6 sm:h-6 group-hover/icon:fill-white"></i>
                                </a>

                                {{-- Line --}}
                                <a href="https://line.me/ti/p/@watakacha" target="_blank"
                                    class="group/icon relative w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white hover:bg-[#06C755] hover:border-[#06C755] hover:scale-110 transition-all duration-300 shadow-xl" title="Line">
                                    <i data-lucide="message-circle" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                                </a>

                                {{-- Instagram --}}
                                <a href="https://www.instagram.com/watakacha_wedding_studio/" target="_blank"
                                    class="group/icon relative w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white hover:bg-pink-600 hover:border-pink-600 hover:scale-110 transition-all duration-300 shadow-xl" title="Instagram">
                                    <i data-lucide="instagram" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Map --}}
                <div class="lg:col-span-7 h-full min-h-[500px] lg:min-h-[800px]">
                    <div class="h-full bg-white p-2 rounded-[2.5rem] shadow-2xl border border-gray-100 relative group overflow-hidden">
                        {{-- Decorative Spinner/Loading effect styling can go here --}}
                        <div class="w-full h-full rounded-[2rem] overflow-hidden relative z-10">
                            {{-- Google Maps Embed (Using San Sai coordinates as placeholder) --}}
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3776.0465545584594!2d99.0185!3d18.8415!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTjCsDUwJzI5LjQiTiA5OcKwMDEnMDYuNiJF!5e0!3m2!1sen!2sth!4v1600000000000!5m2!1sen!2sth"
                                width="100%"
                                height="100%"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                class="absolute inset-0 w-full h-full grayscale-[20%] hover:grayscale-0 transition-all duration-700"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>

                            {{-- Map Overlay Info --}}
                            <div class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-lg border border-white/50 translate-y-full opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500">
                                <p class="text-sm text-gray-600 text-center">
                                    <i data-lucide="navigation" class="w-4 h-4 inline text-brand-500 mr-1"></i>
                                    หมู่บ้านรุ่งเรือง ซ. 8 อำเภอสันทราย เชียงใหม่
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.frontend>