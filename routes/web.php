<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\LiffController;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckUserRole;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\ServiceCostController;
use Illuminate\Support\Facades\Http;

Route::get('/check-richmenu', function () {
    $token = env('LINE_CHANNEL_ACCESS_TOKEN');

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->get('https://api.line.me/v2/bot/richmenu/list');

    return $response->json();
});

Route::get('/create-member-menu', function () {
    $token = env('LINE_CHANNEL_ACCESS_TOKEN');

    // 1. สร้างกรอบเมนู 3 ช่อง
    $menuData = [
        "size" => ["width" => 1200, "height" => 405],
        "selected" => true,
        "name" => "Member Menu API",
        "chatBarText" => "เมนูสมาชิก",
        "areas" => [
            [
                "bounds" => ["x" => 0, "y" => 0, "width" => 400, "height" => 405],
                "action" => ["type" => "message", "text" => "เช็คแต้ม"]
            ],
            [
                "bounds" => ["x" => 400, "y" => 0, "width" => 400, "height" => 405],
                "action" => ["type" => "message", "text" => "เช็คสถานะการเช่า"]
            ],
            [
                "bounds" => ["x" => 800, "y" => 0, "width" => 400, "height" => 405],
                "action" => ["type" => "uri", "uri" => "https://watakacha-supabase.onrender.com/liff/logout"]
            ]
        ]
    ];

    // ส่งคำสั่งสร้างเมนูไปที่ LINE
    $createResponse = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->post('https://api.line.me/v2/bot/richmenu', $menuData);

    $richMenuId = $createResponse->json('richMenuId');

    if (!$richMenuId) {
        return "❌ พังที่ขั้นตอนสร้าง: " . $createResponse->body();
    }

    // 2. อัปโหลดรูปภาพใส่เมนู
    $imagePath = public_path('member_menu.jpg');

    if (!file_exists($imagePath)) {
        return "⚠️ สร้างเมนูได้รหัส: {$richMenuId} <br>แต่หารูปไม่เจอ! เอารูปไปใส่ที่ public/member_menu.jpg ก่อนครับ";
    }

    $image = file_get_contents($imagePath);
    $uploadResponse = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'image/jpeg'
    ])->withBody($image, 'image/jpeg')
        ->post("https://api-data.line.me/v2/bot/richmenu/{$richMenuId}/content");

    if ($uploadResponse->successful()) {
        return "<h1>🎉 สำเร็จ! ได้รหัส ID แล้ว:</h1> <br><br> <h2 style='color:green;'>{$richMenuId}</h2> <br><br> เอารหัสสีเขียวนี้ไปใส่ใน LiffController ได้เลยครับ!";
    }

    return "❌ พังที่ขั้นตอนอัปโหลดรูป: " . $uploadResponse->body();
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================================================================================
// 🏠 1. PUBLIC ZONE (หน้าบ้าน - คนทั่วไป)
// ==================================================================================
Route::controller(WelcomeController::class)->group(function () {
    Route::get('/', 'index')->name('welcome');
    Route::get('/catalog', 'catalog')->name('catalog');
    Route::get('/promotions', 'promotions')->name('promotions');
    Route::get('/contact', 'contact')->name('contact');
});

// ==================================================================================
// 👤 MEMBER ZONE (โซนลูกค้า - เข้าสู่ระบบและดูข้อมูลส่วนตัว)
// ==================================================================================
Route::prefix('member')->name('member.')->group(function () {
    // 1. Authentication
    Route::get('/login', [MemberAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [MemberAuthController::class, 'login'])->name('login.store');
    Route::get('/register', [MemberAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [MemberAuthController::class, 'store'])->name('store');
    Route::post('/logout', [MemberAuthController::class, 'logout'])->name('logout');

    // 2. Member Portal (ต้อง Login ผ่าน Guard member)
    Route::middleware(['auth:member'])->group(function () {
        // ข้อมูลส่วนตัว
        Route::get('/profile', [\App\Http\Controllers\MemberProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\MemberProfileController::class, 'update'])->name('profile.update');

        // ประวัติการเช่าชุด
        Route::get('/history', [\App\Http\Controllers\MemberProfileController::class, 'history'])->name('history');
    });
});

// ==================================================================================
// 📱 LIFF ZONE (เชื่อมต่อ LINE)
// ==================================================================================
Route::prefix('liff')->name('liff.')->group(function () {
    Route::get('/login', [LiffController::class, 'index'])->name('login');
    Route::post('/login', [LiffController::class, 'login'])->name('submit');
    Route::post('/check-auto', [LiffController::class, 'checkAutoLogin'])->name('check');
    Route::get('/logout', [LiffController::class, 'logout'])->name('logout');
    Route::post('/register', [LiffController::class, 'register'])->name('register.submit');
});

// ==================================================================================
// 🛡️ ADMIN ZONE (หลังบ้าน - ผู้จัดการและพนักงาน)
// ==================================================================================
Route::redirect('/admin', '/admin/login');

Route::prefix('admin')->group(function () {

    // Authentication Routes
    require __DIR__ . '/auth.php';

    // Dashboard Dispatcher
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $role = strtolower($user->userType->name ?? '');

        if ($role === 'manager') {
            return redirect()->route('manager.dashboard');
        } elseif ($role === 'reception') {
            return redirect()->route('reception.rental');
        }
        return redirect()->route('unauthorized');
    })->middleware(['auth', 'verified'])->name('dashboard');

    // Profile Management
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // --------------------------------------------------------
    // 👔 MANAGER Routes
    // --------------------------------------------------------
    Route::middleware(['auth', CheckUserRole::class])->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');

        // General Resources
        Route::get('/users', [ManagerController::class, 'usersIndex'])->name('users.index');
        Route::get('/members', [ManagerController::class, 'membersIndex'])->name('members.index');
        Route::get('/user-types', [ManagerController::class, 'userTypesIndex'])->name('user_types.index');
        Route::get('/item-types', [ManagerController::class, 'itemTypesIndex'])->name('item_types.index');
        Route::get('/units', [ManagerController::class, 'unitsIndex'])->name('units.index');
        Route::get('/accessories', [ManagerController::class, 'accessoriesIndex'])->name('accessories.index');
        Route::get('/care-shops', [ManagerController::class, 'careShopsIndex'])->name('care_shops.index');
        Route::get('/makeup-artists', [ManagerController::class, 'makeupArtistsIndex'])->name('makeup_artists.index');
        Route::get('/photographers', [ManagerController::class, 'photographersIndex'])->name('photographers.index');
        Route::get('/photographer-packages', [ManagerController::class, 'photographerPackagesIndex'])->name('photographer_packages.index');
        Route::get('/promotions', [ManagerController::class, 'promotionsIndex'])->name('promotions.index');

        // Items Management
        Route::get('/items', [ManagerController::class, 'itemsIndex'])->name('items.index');
        Route::post('/items', [ManagerController::class, 'storeItem'])->name('items.store');
        Route::match(['put', 'patch'], '/items/{item}', [ManagerController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{item}', [ManagerController::class, 'destroyItem'])->name('items.destroy');
        Route::post('/items/{item}/image', [ManagerController::class, 'uploadItemImage'])->name('items.uploadImage');

        // Item Images (Bulk delete must be before specific ID)
        Route::delete('/item-images/bulk', [ManagerController::class, 'bulkDestroyImages'])->name('items.bulkDestroyImages');
        Route::delete('/item-images/{image}', [ManagerController::class, 'destroyItemImage'])->name('items.destroyImage');
        Route::patch('/item-images/{image}/set-main', [ManagerController::class, 'setMainImage'])->name('items.setMainImage');

        // CRUD Operations
        Route::post('/users', [ManagerController::class, 'storeUser'])->name('users.store');
        Route::match(['put', 'patch'], '/users/{user:user_id}', [ManagerController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user:user_id}', [ManagerController::class, 'destroyUser'])->name('users.destroy');

        Route::post('/user-types', [ManagerController::class, 'storeUserType'])->name('user_types.store');
        Route::match(['put', 'patch'], '/user-types/{user_type:user_type_id}', [ManagerController::class, 'updateUserType'])->name('user_types.update');
        Route::delete('/user-types/{user_type:user_type_id}', [ManagerController::class, 'destroyUserType'])->name('user_types.destroy');

        Route::post('/members', [ManagerController::class, 'storeMember'])->name('members.store');
        Route::match(['put', 'patch'], '/members/{member:member_id}', [ManagerController::class, 'updateMember'])->name('members.update');
        Route::delete('/members/{member:member_id}', [ManagerController::class, 'destroyMember'])->name('members.destroy');

        Route::post('/types', [ManagerController::class, 'storeType'])->name('types.store');
        Route::match(['put', 'patch'], '/types/{type:id}', [ManagerController::class, 'updateType'])->name('types.update');
        Route::delete('/types/{type:id}', [ManagerController::class, 'destroyType'])->name('types.destroy');

        Route::post('/units', [ManagerController::class, 'storeUnit'])->name('units.store');
        Route::match(['put', 'patch'], '/units/{unit:id}', [ManagerController::class, 'updateUnit'])->name('units.update');
        Route::delete('/units/{unit:id}', [ManagerController::class, 'destroyUnit'])->name('units.destroy');

        Route::post('/accessories', [ManagerController::class, 'storeAccessory'])->name('accessories.store');
        Route::match(['put', 'patch'], '/accessories/{accessory}', [ManagerController::class, 'updateAccessory'])->name('accessories.update');
        Route::delete('/accessories/{accessory}', [ManagerController::class, 'destroyAccessory'])->name('accessories.destroy');

        Route::post('/care-shops', [ManagerController::class, 'storeCareShop'])->name('care_shops.store');
        Route::match(['put', 'patch'], '/care-shops/{care_shop:care_shop_id}', [ManagerController::class, 'updateCareShop'])->name('care_shops.update');
        Route::delete('/care-shops/{care_shop:care_shop_id}', [ManagerController::class, 'destroyCareShop'])->name('care_shops.destroy');

        Route::post('/makeup-artists', [ManagerController::class, 'storeMakeupArtist'])->name('makeup_artists.store');
        Route::match(['put', 'patch'], '/makeup-artists/{makeup_artist:makeup_id}', [ManagerController::class, 'updateMakeupArtist'])->name('makeup_artists.update');
        Route::delete('/makeup-artists/{makeup_artist:makeup_id}', [ManagerController::class, 'destroyMakeupArtist'])->name('makeup_artists.destroy');

        Route::post('/photographers', [ManagerController::class, 'storePhotographer'])->name('photographers.store');
        Route::match(['put', 'patch'], '/photographers/{photographer:photographer_id}', [ManagerController::class, 'updatePhotographer'])->name('photographers.update');
        Route::delete('/photographers/{photographer:photographer_id}', [ManagerController::class, 'destroyPhotographer'])->name('photographers.destroy');

        Route::post('/photographer-packages', [ManagerController::class, 'storePhotographerPackage'])->name('photographer_packages.store');
        Route::match(['put', 'patch'], '/photographer-packages/{photographer_package:package_id}', [ManagerController::class, 'updatePhotographerPackage'])->name('photographer_packages.update');
        Route::delete('/photographer-packages/{photographer_package:package_id}', [ManagerController::class, 'destroyPhotographerPackage'])->name('photographer_packages.destroy');

        Route::post('/promotions', [ManagerController::class, 'storePromotion'])->name('promotions.store');
        Route::match(['put', 'patch'], '/promotions/{promotion:promotion_id}', [ManagerController::class, 'updatePromotion'])->name('promotions.update');
        Route::delete('/promotions/{promotion:promotion_id}', [ManagerController::class, 'destroyPromotion'])->name('promotions.destroy');

        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::match(['put', 'patch'], '/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    // --------------------------------------------------------
    // 👩‍💼 RECEPTION Routes
    // --------------------------------------------------------
    Route::middleware(['auth', CheckUserRole::class])->prefix('reception')->name('reception.')->group(function () {
        Route::get('/rental', [ReceptionController::class, 'index'])->name('rental');
        Route::post('/rental', [ReceptionController::class, 'storeRental'])->name('storeRental');

        Route::get('/api/check-member', [ReceptionController::class, 'checkMember'])->name('checkMember');
        Route::get('/api/search-items', [ReceptionController::class, 'searchItems'])->name('searchItems');

        Route::get('/return', [ReceptionController::class, 'returnIndex'])->name('return');
        Route::post('/return/{rental}', [ReceptionController::class, 'processReturn'])->name('processReturn');

        Route::get('/history', [ReceptionController::class, 'history'])->name('history');
        Route::get('/services-history', [ReceptionController::class, 'serviceHistory'])->name('serviceHistory');
        Route::get('/payment-history', [ReceptionController::class, 'paymentHistory'])->name('paymentHistory');
        Route::get('/point-history', [ReceptionController::class, 'pointHistory'])->name('pointHistory');

        Route::get('/calendar', [ReceptionController::class, 'calendar'])->name('calendar');
        Route::get('/calendar/events', [ReceptionController::class, 'getCalendarEvents'])->name('calendar.events');

        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::match(['put', 'patch'], '/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        Route::get('/member/create', [ReceptionController::class, 'createMember'])->name('member.create');
        Route::post('/member/store', [ReceptionController::class, 'storeMember'])->name('member.store');

        Route::post('/rental/{rentalId}/update', [ReceptionController::class, 'updateRental'])->name('rental.update');

        Route::post('/rental/{rentalId}/confirm-payment', [ReceptionController::class, 'confirmPayment'])->name('rental.confirmPayment');
        Route::post('/rental/{rentalId}/confirm-pickup', [ReceptionController::class, 'confirmPickup'])->name('rental.confirmPickup');
        Route::post('/rental/{rentalId}/cancel', [ReceptionController::class, 'cancelRental'])->name('rental.cancel');
    });

    // --------------------------------------------------------
    // 🔧 MAINTENANCE & SERVICE COST Routes
    // --------------------------------------------------------
    Route::middleware(['auth'])->group(function () {
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance/{id}/send', [MaintenanceController::class, 'sendToShop'])->name('maintenance.send');
        Route::post('/maintenance/{id}/receive', [MaintenanceController::class, 'receiveFromShop'])->name('maintenance.receive');

        Route::get('/service-costs', [ServiceCostController::class, 'index'])->name('service_costs.index');
        Route::post('/service-costs/{id}/update', [ServiceCostController::class, 'updateCost'])->name('service_costs.update');
    });
}); // End Admin Group

// ==================================================================================
// 🚫 ERROR & DEBUG
// ==================================================================================
Route::get('/unauthorized', function () {
    return view('errors.unauthorized');
})->name('unauthorized');

Route::get('/debug-cloudinary', function () {
    return [
        'cloudinary_url_from_env' => env('CLOUDINARY_URL'),
        'config_check' => config('cloudinary'),
    ];
});
