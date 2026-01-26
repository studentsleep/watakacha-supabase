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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🏠 1. หน้าบ้าน (Public) - คนทั่วไปเข้าหน้านี้
Route::controller(WelcomeController::class)->group(function () {
    Route::get('/', 'index')->name('welcome');           // หน้าแรก (9 ชิ้น)
    Route::get('/catalog', 'catalog')->name('catalog');  // หน้าสินค้าทั้งหมด + ค้นหา
    Route::get('/promotions', 'promotions')->name('promotions'); // หน้าโปรโมชั่น
    Route::get('/contact', 'contact')->name('contact');  // หน้าติดต่อเรา
});

// 👤 โซนสมาชิก (Member Auth)
Route::prefix('member')->name('member.')->group(function () {
    // 1. Login
    Route::get('/login', [MemberAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [MemberAuthController::class, 'login'])->name('login.store'); // รับค่า Login

    // 2. Register
    Route::get('/register', [MemberAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [MemberAuthController::class, 'store'])->name('store'); // ✅ ตัวแก้ Error (รับค่าสมัคร)

    // 3. Logout
    Route::post('/logout', [MemberAuthController::class, 'logout'])->name('logout');
});

// 🔒 2. หน้าแจ้งเตือน (เข้าได้แต่ไม่มีสิทธิ์)
Route::get('/unauthorized', function () {
    return view('errors.unauthorized');
})->middleware(['auth'])->name('unauthorized');

// 🚀 3. ทางลัด: พิมพ์ /admin ให้เด้งไป /admin/login
Route::redirect('/admin', '/admin/login');


// ==================================================================================
// 🛡️ ADMIN ZONE (หลังบ้าน) - URL จะขึ้นต้นด้วย /admin ทั้งหมด
// ==================================================================================
Route::prefix('admin')->group(function () {

    // 🅰️ นำระบบ Authentication (Login/Logout) เข้ามาในกลุ่มนี้
    // ผลลัพธ์: หน้า Login จะเปลี่ยนจาก /login เป็น /admin/login
    require __DIR__ . '/auth.php';

    // 🅱️ Dashboard Router (ตัวแยกทาง)
    // เช็คว่าใครล็อกอิน แล้วพาไปหน้าบ้านของตัวเอง
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

    // 3. Profile Routes
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // --------------------------------------------------------
    // 🛡️ MANAGER Routes (ผู้จัดการ)
    // --------------------------------------------------------
    Route::middleware(['auth', CheckUserRole::class])->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');

        // กลุ่มงานต่างๆ
        Route::get('/users', [ManagerController::class, 'usersIndex'])->name('users.index');
        Route::get('/members', [ManagerController::class, 'membersIndex'])->name('members.index');
        Route::get('/user-types', [ManagerController::class, 'userTypesIndex'])->name('user_types.index');
        Route::get('/items', [ManagerController::class, 'itemsIndex'])->name('items.index');
        Route::get('/item-types', [ManagerController::class, 'itemTypesIndex'])->name('item_types.index');
        Route::get('/units', [ManagerController::class, 'unitsIndex'])->name('units.index');
        Route::get('/accessories', [ManagerController::class, 'accessoriesIndex'])->name('accessories.index');
        Route::get('/care-shops', [ManagerController::class, 'careShopsIndex'])->name('care_shops.index');
        Route::get('/makeup-artists', [ManagerController::class, 'makeupArtistsIndex'])->name('makeup_artists.index');
        Route::get('/photographers', [ManagerController::class, 'photographersIndex'])->name('photographers.index');
        Route::get('/photographer-packages', [ManagerController::class, 'photographerPackagesIndex'])->name('photographer_packages.index');
        Route::get('/promotions', [ManagerController::class, 'promotionsIndex'])->name('promotions.index');

        // CRUD (Create, Update, Delete)
        Route::post('/users', [ManagerController::class, 'storeUser'])->name('users.store');
        Route::match(['put', 'patch'], '/users/{user:user_id}', [ManagerController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user:user_id}', [ManagerController::class, 'destroyUser'])->name('users.destroy');

        Route::post('/user-types', [ManagerController::class, 'storeUserType'])->name('user_types.store');
        Route::match(['put', 'patch'], '/user-types/{user_type:user_type_id}', [ManagerController::class, 'updateUserType'])->name('user_types.update');
        Route::delete('/user-types/{user_type:user_type_id}', [ManagerController::class, 'destroyUserType'])->name('user_types.destroy');

        Route::post('/members', [ManagerController::class, 'storeMember'])->name('members.store');
        Route::match(['put', 'patch'], '/members/{member:member_id}', [ManagerController::class, 'updateMember'])->name('members.update');
        Route::delete('/members/{member:member_id}', [ManagerController::class, 'destroyMember'])->name('members.destroy');

        Route::post('/items', [ManagerController::class, 'storeItem'])->name('items.store');
        Route::match(['put', 'patch'], '/items/{item}', [ManagerController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{item}', [ManagerController::class, 'destroyItem'])->name('items.destroy');
        Route::post('/items/{item}/image', [ManagerController::class, 'uploadItemImage'])->name('items.uploadImage');
        Route::delete('/item-images/{image}', [ManagerController::class, 'destroyItemImage'])->name('items.destroyImage');
        Route::patch('/item-images/{image}/set-main', [ManagerController::class, 'setMainImage'])->name('items.setMainImage');

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
    // 👩‍💼 RECEPTION Routes (พนักงานต้อนรับ)
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
    });

    // --------------------------------------------------------
    // 🔧 MAINTENANCE Routes
    // --------------------------------------------------------
    Route::middleware(['auth'])->group(function () {
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance/{id}/send', [MaintenanceController::class, 'sendToShop'])->name('maintenance.send');
        Route::post('/maintenance/{id}/receive', [MaintenanceController::class, 'receiveFromShop'])->name('maintenance.receive');
    });
}); // 🛑 ปิด Group Admin


// ==================================================================================
// 📱 LIFF & External (ไม่ต้องมี /admin)
// ==================================================================================
Route::prefix('liff')->group(function () {
    Route::get('/login', [LiffController::class, 'index'])->name('liff.login');
    Route::post('/login', [LiffController::class, 'login'])->name('liff.submit');
    Route::post('/check-auto', [LiffController::class, 'checkAutoLogin'])->name('liff.check');
});
