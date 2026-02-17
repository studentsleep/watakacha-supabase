<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 🟢 เพิ่มบรรทัดนี้เข้าไป เพื่อให้ Render ส่ง Cookie ได้สำเร็จ
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'liff/*',

        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            // ถ้า URL ที่เข้า พยายามเข้าโซน member หรือ liff ให้เด้งไปหน้าล็อกอินลูกค้า
            if ($request->is('member/*') || $request->is('liff/*')) {
                return route('liff.login'); // หรือถ้าอยากให้เด้งไปหน้าเว็บปกติ ให้เปลี่ยนเป็น route('member.login')
            }

            // นอกนั้น (โซน Admin/พนักงาน) ให้เด้งไปหน้า Login ปกติ
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
