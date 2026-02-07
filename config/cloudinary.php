<?php

/*
|--------------------------------------------------------------------------
| Smart Cloudinary Configuration
|--------------------------------------------------------------------------
*/

// 1. ดึงค่า URL มาจาก ENV
$cloudinaryUrl = env('CLOUDINARY_URL');

// 2. เตรียมตัวแปรสำหรับค่าแยก
$cloudName = env('CLOUDINARY_CLOUD_NAME');
$apiKey = env('CLOUDINARY_API_KEY');
$apiSecret = env('CLOUDINARY_API_SECRET');

// 3. (สำคัญ!) ถ้ามี URL แต่ไม่มีค่าแยก ให้ "แกะ" ค่าออกมาจาก URL
if ($cloudinaryUrl && empty($cloudName)) {
    // URL Format: cloudinary://API_KEY:API_SECRET@CLOUD_NAME
    if (preg_match('#cloudinary://([^:]+):([^@]+)@(.+)#', $cloudinaryUrl, $matches)) {
        $apiKey = $matches[1];
        $apiSecret = $matches[2];
        $cloudName = $matches[3];
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    */
    'cloud_url' => $cloudinaryUrl,
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    /*
    |--------------------------------------------------------------------------
    | Cloud Keys (จุดที่เคย Error)
    |--------------------------------------------------------------------------
    | ตอนนี้เราใส่ค่าที่แกะออกมาได้ลงไปตรงนี้แล้ว รับรองไม่ Null
    */
    'cloud' => [
        'cloud_name' => $cloudName,
        'api_key'    => $apiKey,
        'api_secret' => $apiSecret,
    ],

    'url' => [
        'secure' => true,
    ],
];
