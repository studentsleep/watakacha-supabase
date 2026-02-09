<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบสมาชิก</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* ซ่อน Scrollbar ตอน Modal ขึ้น */
        .no-scroll {
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen font-sans">

    {{-- 1. Loading Screen --}}
    <div id="loading" class="fixed inset-0 bg-white flex flex-col items-center justify-center z-50">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
        <p class="text-gray-500">กำลังตรวจสอบข้อมูล...</p>
    </div>

    {{-- 2. PDPA Consent Modal (หน้าต่างข้อตกลง) --}}
    <div id="consentModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800 text-center">ข้อกำหนดและเงื่อนไขการให้บริการ</h2>
            </div>

            {{-- Scrollable Content (เนื้อหา PDPA) --}}
            <div class="p-6 overflow-y-auto text-sm text-gray-600 leading-relaxed space-y-4">
                <p class="font-bold text-gray-800">นโยบายความเป็นส่วนตัว (Privacy Policy)</p>

                <p>ร้านเช่าชุดของเรา (ต่อไปนี้จะเรียกว่า "ผู้ให้บริการ") ให้ความสำคัญอย่างยิ่งต่อการคุ้มครองข้อมูลส่วนบุคคลของท่าน เพื่อให้เป็นไปตามพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 (PDPA) เราจึงขอแจ้งให้ท่านทราบถึงรายละเอียดดังต่อไปนี้:</p>

                <p><strong class="text-gray-800">1. การเก็บรวบรวมข้อมูล:</strong> ระบบจะทำการเก็บรวบรวมข้อมูลของท่าน ได้แก่ ชื่อ-นามสกุล, เบอร์โทรศัพท์, ข้อมูลการเช่าสินค้า, และ Line User ID เพื่อใช้ในการระบุตัวตนและเชื่อมโยงบัญชี</p>

                <p><strong class="text-gray-800">2. วัตถุประสงค์การใช้ข้อมูล:</strong> ข้อมูลของท่านจะถูกนำไปใช้เพื่อ:
                <ul class="list-disc pl-5 mt-1">
                    <li>ตรวจสอบสถานะและประวัติการเช่า-คืนสินค้า</li>
                    <li>แจ้งเตือนกำหนดการรับ-คืนชุด หรือสถานะการซ่อมบำรุง</li>
                    <li>สะสมคะแนนและสิทธิประโยชน์สมาชิก</li>
                </ul>
                </p>

                <p><strong class="text-gray-800">3. ระยะเวลาการจัดเก็บ:</strong> เราจะจัดเก็บข้อมูลของท่านไว้ตราบเท่าที่ท่านยังเป็นสมาชิก หรือมีความจำเป็นทางกฎหมาย</p>

                <p><strong class="text-gray-800">4. สิทธิของท่าน:</strong> ท่านมีสิทธิในการขอเข้าถึง แก้ไข หรือลบข้อมูลส่วนบุคคลของท่านได้ โดยติดต่อที่เคาน์เตอร์บริการ</p>

                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mt-4 text-xs">
                    การกดปุ่ม <strong>"ยอมรับและดำเนินการต่อ"</strong> ถือว่าท่านได้อ่านและตกลงยินยอมให้ทางร้านเก็บรวบรวมและใช้ข้อมูลส่วนบุคคลตามวัตถุประสงค์ข้างต้น
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 bg-white">
                <button onclick="declineConsent()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                    ไม่ยอมรับ
                </button>
                <button onclick="acceptConsent()" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold shadow-lg shadow-indigo-200 transition">
                    ยอมรับ
                </button>
            </div>
        </div>
    </div>

    {{-- 3. Login Form (หน้ากรอกเบอร์) --}}
    <div id="loginForm" class="hidden min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-sm border border-gray-100">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.131A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">ยืนยันตัวตน</h2>
                <p class="text-gray-500 text-sm mt-1">กรอกข้อมูลเพื่อเชื่อมต่อบัญชีสมาชิก</p>
            </div>

            @if($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-sm text-center border border-red-100 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('liff.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="line_user_id" id="line_user_id">

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">เบอร์โทรศัพท์</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <input type="tel" name="tel" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" placeholder="08xxxxxxxx" required>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">รหัสผ่าน (วันเกิด)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" placeholder="เช่น 190126 (YYMMDD)" required>
                    </div>
                    <p class="text-xs text-gray-400 mt-2 ml-1">* ใช้ปี ค.ศ. 2 ตัวท้าย + เดือน + วัน (รวม 6 หลัก)</p>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition transform active:scale-95">
                    เข้าสู่ระบบ
                </button>
            </form>
        </div>
    </div>

    <script>
        const LIFF_ID = "2009077441-uCh3VnXy"; // 🔴 อย่าลืมใส่ LIFF ID จริงที่นี่

        document.addEventListener('DOMContentLoaded', async function() {
            try {
                await liff.init({
                    liffId: LIFF_ID
                });
                if (!liff.isLoggedIn()) {
                    liff.login();
                    return;
                }

                const profile = await liff.getProfile();
                const userId = profile.userId;
                document.getElementById('line_user_id').value = userId;

                checkAutoLogin(userId);

            } catch (err) {
                console.error('LIFF Error', err);
                alert('ไม่สามารถเชื่อมต่อ LINE ได้');
                // กรณี Error ให้โชว์หน้า Login เลย (หรือจะโชว์ Error page ก็ได้)
                showConsentModal();
            }
        });

        function checkAutoLogin(lineUserId) {
            fetch("{{ route('liff.check') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        line_user_id: lineUserId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('loading').classList.add('hidden');

                    if (data.status === 'found') {
                        window.location.href = data.redirect;
                    } else {
                        // 🔴 ยังไม่เคยผูก -> แสดงหน้า Consent ก่อน
                        showConsentModal();
                    }
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('loading').classList.add('hidden');
                    showConsentModal();
                });
        }

        // --- Logic การแสดงผล Modal ---

        function showConsentModal() {
            document.getElementById('consentModal').classList.remove('hidden');
            document.body.classList.add('no-scroll');
        }

        function acceptConsent() {
            // ซ่อน Modal
            document.getElementById('consentModal').classList.add('hidden');
            document.body.classList.remove('no-scroll');
            // แสดงหน้า Login Form
            document.getElementById('loginForm').classList.remove('hidden');
        }

        function declineConsent() {
            // ปิดหน้าต่าง LIFF
            if (liff.isInClient()) {
                liff.closeWindow();
            } else {
                alert("คุณต้องยอมรับเงื่อนไขเพื่อใช้งาน");
                window.close();
            }
        }
    </script>
</body>

</html>