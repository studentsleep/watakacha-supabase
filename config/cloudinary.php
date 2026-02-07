<?php

/*
|--------------------------------------------------------------------------
| Robust Cloudinary Configuration
|--------------------------------------------------------------------------
*/

// 1. กำหนดค่าเริ่มต้นเป็น null
$cloudName = env('CLOUDINARY_CLOUD_NAME', null);
$apiKey = env('CLOUDINARY_API_KEY', null);
$apiSecret = env('CLOUDINARY_API_SECRET', null);
$cloudUrl = env('CLOUDINARY_URL', null);

// 2. ถ้าไม่มีค่าแยก แต่มี URL ให้แกะค่าออกมา (Parse URL)
if (empty($cloudName) && !empty($cloudUrl)) {
    $parsed = parse_url($cloudUrl);
    if (isset($parsed['host'])) $cloudName = $parsed['host'];
    if (isset($parsed['user'])) $apiKey = $parsed['user'];
    if (isset($parsed['pass'])) $apiSecret = $parsed['pass'];
}

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration Keys
    |--------------------------------------------------------------------------
    */

    // ใส่ URL ไว้ด้วย (บางเวอร์ชันใช้ตัวนี้)
    'cloud_url' => $cloudUrl,

    // ✅ จุดสำคัญ: บังคับให้มี Key 'cloud' เสมอ (แม้ค่าจะเป็น null)
    'cloud' => [
        'cloud_name' => $cloudName,
        'api_key'    => $apiKey,
        'api_secret' => $apiSecret,
    ],

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),
    'url' => [
        'secure' => true,
    ],
];
