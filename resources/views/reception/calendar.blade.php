<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📅 ปฏิทินงานเช่า (Rental Calendar)
            </h2>
            {{-- Legend บอกสี --}}
            <div class="flex gap-3 text-xs">
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
                
                // ✅ Google Calendar Style Settings
                dayMaxEvents: 3, // แสดงสูงสุด 3 รายการต่อวัน (เกินกว่านี้จะเป็นปุ่ม +more)
                expandRows: true, // ขยายแถวให้เต็มความสูง
                fixedWeekCount: false, // ไม่ต้องโชว์แถวว่างของเดือนถัดไปเยอะเกิน

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth' // ตัดมุมมองสัปดาห์ออกถ้าไม่จำเป็น จะได้ดูง่ายๆ
                },

                buttonText: {
                    today: 'วันนี้',
                    month: 'มุมมองเดือน',
                    list: 'มุมมองรายการ'
                },

                events: '{{ route("reception.calendar.events") }}',

                // ปรับแต่งหน้าตา Event
                eventContent: function(arg) {
                    // ถ้าเป็น background event (Maintenance) ไม่ต้องทำอะไร
                    if (arg.event.display === 'background') return;

                    let title = arg.event.title;
                    // สร้าง HTML สำหรับแท่งบาร์
                    let arrayOfDomNodes = []
                    let div = document.createElement('div');
                    div.className = 'fc-event-main-frame flex items-center px-1 overflow-hidden text-xs';
                    div.innerHTML = `<div class="fc-event-title-container font-medium truncate">${title}</div>`;
                    arrayOfDomNodes.push(div);
                    return { domNodes: arrayOfDomNodes }
                },

                eventClick: function(info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.open(info.event.url, "_blank");
                    }
                },
                
                // Hover แล้วเปลี่ยน cursor
                eventMouseEnter: function(info) {
                    if(info.event.display !== 'background') {
                        info.el.style.cursor = 'pointer';
                        // ใส่ Tooltip ง่ายๆ (Browser Default)
                        info.el.title = info.event.title; 
                    }
                }
            });

            calendar.render();
        });
    </script>

    {{-- ปรับแต่ง CSS ของ FullCalendar ให้สวยขึ้น --}}
    <style>
        /* ลบขีดเส้นใต้ลิงก์ */
        a.fc-event { text-decoration: none; }
        
        /* ปรับแต่งส่วนหัววัน (Mon, Tue...) */
        .fc-col-header-cell-cushion {
            padding: 10px 0;
            color: #4b5563;
            font-weight: 600;
            text-decoration: none !important;
        }
        
        /* ปรับแต่งเลขวันที่ */
        .fc-daygrid-day-number {
            text-decoration: none !important;
            color: #374151;
            font-weight: 500;
            padding: 8px;
        }

        /* ปรับแต่งปุ่ม +more */
        .fc-daygrid-more-link {
            text-decoration: none !important;
            color: #4b5563;
            font-weight: bold;
            font-size: 0.8rem;
        }

        /* ทำให้ Background Maintenance ดูนุ่มนวล */
        .fc-bg-event {
            opacity: 0.6; 
        }
    </style>
</x-app-layout>