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

    {{-- Login Form --}}
    <div id="loginForm" class="hidden min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-sm border border-gray-100">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 mb-3">
                    {{-- Icon User --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">เข้าสู่ระบบ</h2>
                <p class="text-gray-500 text-sm mt-1">กรอกชื่อผู้ใช้และรหัสผ่านมาตรฐานของคุณ</p>
            </div>

            @if($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-sm text-center border border-red-100">
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('liff.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="line_user_id" id="line_user_id">

                {{-- 1. ช่อง Username --}}
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">ชื่อผู้ใช้ (Username)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        {{-- เปลี่ยน type="tel" เป็น text และ name="username" --}}
                        <input type="text" name="username" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" placeholder="Username" required>
                    </div>
                </div>

                {{-- 2. ช่อง Password --}}
                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">รหัสผ่าน (Password)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" placeholder="Password" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition transform active:scale-95">
                    ผูกบัญชีและเข้าสู่ระบบ
                </button>
            </form>
        </div>
    </div>

    <script>
        // ✅ แก้ LIFF ID ให้ถูกต้อง (ลบ https://... ออก)
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
                document.getElementById('line_user_id').value = profile.userId;
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
    </script>
</body>

</html>