<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📅 ปฏิทินงานเช่า (Rental Calendar)
            </h2>
            {{-- Legend บอกสี --}}
            <div class="flex gap-3 text-xs text-slate-400">
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#4285F4]"></span> กำลังเช่า</div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#EA4335]"></span> เกินกำหนด</div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#9AA0A6]"></span> คืนแล้ว</div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded border border-yellow-400 bg-[#FEF3C7]"></span> ช่วงดูแลชุด</div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div id="calendar" class="min-h-[700px] font-sans"></div>
                </div>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'standard',
                height: 'auto',
                locale: 'th',

                dayMaxEvents: 3,
                expandRows: true,
                fixedWeekCount: false,

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                },

                buttonText: {
                    today: 'วันนี้',
                    month: 'มุมมองเดือน',
                    list: 'รายการ'
                },

                events: '{{ route("reception.calendar.events") }}',

                // ✅ 1. ปรับแต่งการแสดงผล
                eventContent: function(arg) {

                    // -------------------------------------------------
                    // A. มุมมองรายการ (List View)
                    // -------------------------------------------------
                    if (arg.view.type === 'listMonth') {
                        let props = arg.event.extendedProps;
                        let rawTitle = arg.event.title;

                        // แยก ID, ชื่อ, ชุด (เหมือนเดิม)
                        let idMatch = rawTitle.match(/#(\d+)/);
                        let id = idMatch ? idMatch[0] : '';
                        let content = rawTitle.replace(/#\d+\s*/, '');

                        // สร้างวันที่แบบย่อ
                        const options = {
                            day: 'numeric',
                            month: 'short'
                        };
                        let dateText = arg.event.start.toLocaleDateString('th-TH', options);
                        if (arg.event.end) {
                            let realEnd = new Date(arg.event.end);
                            realEnd.setDate(realEnd.getDate() - 1);
                            if (arg.event.start.getTime() !== realEnd.getTime()) {
                                dateText += ` - ${realEnd.toLocaleDateString('th-TH', options)}`;
                            }
                        }

                        let tel = props.tel ? `<span class="text-xs ml-2 text-slate-400 group-hover:text-gray-500">📞 ${props.tel}</span>` : '';

                        let div = document.createElement('div');
                        // เพิ่ม cursor-pointer ให้รู้ว่ากดได้
                        div.className = 'flex flex-col py-1 cursor-pointer group transition-colors duration-200';

                        div.innerHTML = `
                            <div class="text-base text-slate-200 group-hover:text-black">
                                <span class="font-bold text-slate-100 group-hover:text-black">${id}</span> 
                                <span class="font-normal">${content}</span>
                                ${tel}
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5 group-hover:text-gray-600">🗓️ ${dateText}</div>
                        `;

                        // 🔴 [จุดสำคัญที่เพิ่ม] ฝังคำสั่งคลิกโดยตรงที่ตัว DIV นี้เลย
                        div.addEventListener('click', function(e) {
                            // หยุดไม่ให้ event ซ้อนทับ (เผื่อมี)
                            e.stopPropagation();
                            // สั่งให้เปลี่ยนหน้าทันที
                            if (arg.event.url) {
                                window.location.href = arg.event.url;
                            }
                        });

                        return {
                            domNodes: [div]
                        };
                    }

                    // -------------------------------------------------
                    // B. มุมมองตาราง (Grid View)
                    // -------------------------------------------------
                    if (arg.event.display === 'background') return;

                    let title = arg.event.title;
                    let div = document.createElement('div');
                    div.className = 'fc-event-main-frame flex items-center px-1 overflow-hidden text-xs cursor-pointer'; // เพิ่ม cursor-pointer
                    div.innerHTML = `<div class="fc-event-title-container font-medium truncate">${title}</div>`;

                    // 🔴 ฝังคลิกให้ Grid View ด้วยเพื่อความชัวร์
                    div.addEventListener('click', function(e) {
                        e.stopPropagation();
                        if (arg.event.url) {
                            window.location.href = arg.event.url;
                        }
                    });

                    return {
                        domNodes: [div]
                    }
                },

                // ✅ 2. Event Click หลัก (เก็บไว้เป็น Fallback เผื่อคลิกนอก div ที่เราสร้าง)
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                },

                eventMouseEnter: function(info) {
                    if (info.event.display !== 'background') {
                        info.el.style.cursor = 'pointer';
                        info.el.title = info.event.title;
                    }
                }
            });

            calendar.render();
        });
    </script>

    <style>
        a.fc-event {
            text-decoration: none;
        }

        /* หัวปฏิทินสีแดง */
        .fc-toolbar-title {
            color: #EF4444 !important;
            font-weight: 800 !important;
        }

        /* วันที่ปัจจุบัน */
        .fc-day-today {
            background-color: transparent !important;
        }

        .fc-day-today .fc-daygrid-day-number {
            background-color: #EF4444 !important;
            color: white !important;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px auto 0 auto;
        }

        /* ปุ่มกด */
        .fc-button {
            background-color: white !important;
            color: #4b5563 !important;
            border-color: #d1d5db !important;
            font-weight: 600 !important;
        }

        .fc-button-active {
            background-color: #EF4444 !important;
            color: white !important;
            border-color: #EF4444 !important;
        }

        /* List View Styling */
        .fc-list-event-time {
            display: none;
        }

        /* ซ่อน All-day */
        .fc-list-event-graphic {
            vertical-align: top;
            padding-top: 12px !important;
        }

        /* ✅ Hover Effect สำหรับแถวใน List View */
        .fc-list-event:hover td {
            background-color: #ffffff !important;
            /* พื้นหลังขาวเมื่อชี้ */
        }

        /* เปลี่ยนสีจุดเมื่อ Hover (Optional: ถ้าอยากให้จุดชัดขึ้น) */
        /* .fc-list-event:hover .fc-list-event-dot { border-color: #000 !important; } */

        /* อื่นๆ */
        .fc-col-header-cell-cushion {
            padding: 10px 0;
            color: #4b5563;
            font-weight: 600;
            text-decoration: none !important;
        }

        .fc-daygrid-day-number {
            text-decoration: none !important;
            color: #374151;
            font-weight: 500;
            padding: 8px;
        }

        .fc-daygrid-more-link {
            text-decoration: none !important;
            color: #4b5563;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .fc-bg-event {
            opacity: 0.6;
        }
    </style>
</x-app-layout>