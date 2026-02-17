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
        .no-scroll {
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen font-sans">

    {{-- Loading Screen --}}
    <div id="loading" class="fixed inset-0 bg-white flex flex-col items-center justify-center z-50">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
        <p class="text-gray-500">กำลังตรวจสอบข้อมูล...</p>
    </div>

    {{-- PDPA Consent Modal --}}
    <div id="consentModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800 text-center">ข้อกำหนดและเงื่อนไข</h2>
            </div>
            <div class="p-6 overflow-y-auto text-sm text-gray-600 leading-relaxed space-y-4">
                <p>ทางร้านขอเก็บข้อมูล Username และ Line ID เพื่อใช้ในการเชื่อมต่อบัญชีสมาชิก ตรวจสอบสถานะการเช่า และสะสมคะแนน</p>
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mt-4 text-xs">
                    กด <strong>"ยอมรับ"</strong> เพื่อดำเนินการต่อ
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 bg-white">
                <button onclick="declineConsent()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">ไม่ยอมรับ</button>
                <button onclick="acceptConsent()" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold shadow-lg transition">ยอมรับ</button>
            </div>
        </div>
    </div>

    {{-- Main Box (มีทั้ง Login และ Register) --}}
    <div id="loginForm" class="hidden min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-sm border border-gray-100">

            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800" id="formTitle">เข้าสู่ระบบ</h2>
            </div>

            {{-- Tabs สลับ Login / Register --}}
            <div class="flex border-b border-gray-200 mb-6">
                <button type="button" onclick="switchTab('login')" id="tabLogin" class="w-1/2 py-2 text-center font-bold text-indigo-600 border-b-2 border-indigo-600 transition">มีบัญชีแล้ว</button>
                <button type="button" onclick="switchTab('register')" id="tabRegister" class="w-1/2 py-2 text-center font-bold text-gray-400 border-b-2 border-transparent transition hover:text-indigo-500">สมัครสมาชิก</button>
            </div>

            @if($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-sm text-center border border-red-100">
                {{ $errors->first() }}
            </div>
            @endif

            {{-- 🟢 ฟอร์มที่ 1: เข้าสู่ระบบ (เหมือนเดิม) --}}
            <form id="formSectionLogin" action="{{ route('liff.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="line_user_id" class="line_user_id_field">

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">ชื่อผู้ใช้ (Username)</label>
                    <input type="text" name="username" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" placeholder="Username" required>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">รหัสผ่าน (Password)</label>
                    <input type="password" name="password" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" placeholder="Password" required>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-xl hover:bg-indigo-700 transition">
                    ผูกบัญชีและเข้าสู่ระบบ
                </button>
            </form>

            {{-- 🟢 ฟอร์มที่ 2: สมัครสมาชิก (ซ่อนไว้ก่อน) --}}
            <form id="formSectionRegister" action="{{ route('liff.register.submit') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="line_user_id" class="line_user_id_field">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">ชื่อผู้ใช้ (สำหรับเข้าสู่ระบบครั้งหน้า)</label>
                    <input type="text" name="username" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" placeholder="Username (Eng)" required>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">ชื่อจริง</label>
                        <input type="text" name="first_name" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">นามสกุล</label>
                        <input type="text" name="last_name" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">เบอร์โทรศัพท์</label>
                    <input type="tel" name="tel" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">รหัสผ่าน</label>
                    <input type="password" name="password" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" placeholder="อย่างน้อย 6 ตัวอักษร" required>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white font-bold py-3.5 rounded-xl hover:bg-green-700 transition">
                    สมัครสมาชิกและผูกบัญชี
                </button>
            </form>
        </div>
    </div>

    <script>
        // ✅ แก้ LIFF ID ให้ถูกต้อง
        const LIFF_ID = "2009077441-uCh3VnXy";

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

                // 🟢 [ส่วนที่แก้] ค้นหา input ที่มี class .line_user_id_field ทั้งหมดและใส่ค่า
                document.querySelectorAll('.line_user_id_field').forEach(input => {
                    input.value = profile.userId;
                });

                checkAutoLogin(profile.userId);
            } catch (err) {
                console.error('LIFF Error', err);
                alert('Connection Error: ' + err.message);
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
                        showConsentModal();
                    }
                })
                .catch(err => {
                    document.getElementById('loading').classList.add('hidden');
                    showConsentModal();
                });
        }

        function showConsentModal() {
            document.getElementById('consentModal').classList.remove('hidden');
            document.body.classList.add('no-scroll');
        }

        function acceptConsent() {
            document.getElementById('consentModal').classList.add('hidden');
            document.body.classList.remove('no-scroll');
            document.getElementById('loginForm').classList.remove('hidden');
        }

        function declineConsent() {
            if (liff.isInClient()) {
                liff.closeWindow();
            } else {
                window.close();
            }
        }

        // 🟢 [ส่วนที่เพิ่ม] ฟังก์ชันสลับหน้าจอ Login / Register
        function switchTab(tab) {
            if (tab === 'login') {
                document.getElementById('formSectionLogin').classList.remove('hidden');
                document.getElementById('formSectionRegister').classList.add('hidden');

                document.getElementById('tabLogin').classList.add('text-indigo-600', 'border-indigo-600');
                document.getElementById('tabLogin').classList.remove('text-gray-400', 'border-transparent');
                document.getElementById('tabRegister').classList.add('text-gray-400', 'border-transparent');
                document.getElementById('tabRegister').classList.remove('text-indigo-600', 'border-indigo-600');
                document.getElementById('formTitle').innerText = 'เข้าสู่ระบบ';
            } else {
                document.getElementById('formSectionRegister').classList.remove('hidden');
                document.getElementById('formSectionLogin').classList.add('hidden');

                document.getElementById('tabRegister').classList.add('text-indigo-600', 'border-indigo-600');
                document.getElementById('tabRegister').classList.remove('text-gray-400', 'border-transparent');
                document.getElementById('tabLogin').classList.add('text-gray-400', 'border-transparent');
                document.getElementById('tabLogin').classList.remove('text-indigo-600', 'border-indigo-600');
                document.getElementById('formTitle').innerText = 'สมัครสมาชิกใหม่';
            }
        }
    </script>
</body>

</html>