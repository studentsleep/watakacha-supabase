<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// --- Dashboard ---
// ใช้ Controller เพื่อคำนวณกราฟและตัวเลข
Route::get('/dashboard', [ManagerController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- Auth & Profile ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ====================================================
// 🛡️ Manager Routes (ผู้จัดการ) - แยกตามกลุ่มงาน
// ====================================================
Route::middleware(['auth'])->prefix('manager')->name('manager.')->group(function () {

    // 🟢 กลุ่ม 1: ระบบสมาชิก & พนักงาน (Users & Members)
    Route::get('/users', [ManagerController::class, 'usersIndex'])->name('users.index');
    Route::get('/members', [ManagerController::class, 'membersIndex'])->name('members.index');
    Route::get('/user-types', [ManagerController::class, 'userTypesIndex'])->name('user_types.index');

    // 🟣 กลุ่ม 2: คลังสินค้า (Stock)
    Route::get('/items', [ManagerController::class, 'itemsIndex'])->name('items.index');
    Route::get('/item-types', [ManagerController::class, 'itemTypesIndex'])->name('item_types.index');
    Route::get('/units', [ManagerController::class, 'unitsIndex'])->name('units.index');
    Route::get('/accessories', [ManagerController::class, 'accessoriesIndex'])->name('accessories.index');

    // 🩷 กลุ่ม 3: พาร์ทเนอร์ & บริการ (Services)
    Route::get('/care-shops', [ManagerController::class, 'careShopsIndex'])->name('care_shops.index');
    Route::get('/makeup-artists', [ManagerController::class, 'makeupArtistsIndex'])->name('makeup_artists.index');
    Route::get('/photographers', [ManagerController::class, 'photographersIndex'])->name('photographers.index');
    Route::get('/photographer-packages', [ManagerController::class, 'photographerPackagesIndex'])->name('photographer_packages.index');

    // 🟡 กลุ่ม 4: การตลาด (Marketing)
    Route::get('/promotions', [ManagerController::class, 'promotionsIndex'])->name('promotions.index');


    // --- CRUD Operations (Store / Update / Destroy) ---
    // หมายเหตุ: ใช้ match(['put', 'patch']) เพื่อรองรับ Form ทั้ง 2 แบบ กัน Error 405

    // 1. Users
    Route::post('/users', [ManagerController::class, 'storeUser'])->name('users.store');
    Route::match(['put', 'patch'], '/users/{user:user_id}', [ManagerController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user:user_id}', [ManagerController::class, 'destroyUser'])->name('users.destroy');

    // 2. User Types
    Route::post('/user-types', [ManagerController::class, 'storeUserType'])->name('user_types.store');
    Route::match(['put', 'patch'], '/user-types/{user_type:user_type_id}', [ManagerController::class, 'updateUserType'])->name('user_types.update');
    Route::delete('/user-types/{user_type:user_type_id}', [ManagerController::class, 'destroyUserType'])->name('user_types.destroy');

    // 3. Members
    Route::post('/members', [ManagerController::class, 'storeMember'])->name('members.store');
    Route::match(['put', 'patch'], '/members/{member:member_id}', [ManagerController::class, 'updateMember'])->name('members.update');
    Route::delete('/members/{member:member_id}', [ManagerController::class, 'destroyMember'])->name('members.destroy');

    // 4. Items
    Route::post('/items', [ManagerController::class, 'storeItem'])->name('items.store');
    Route::match(['put', 'patch'], '/items/{item}', [ManagerController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{item}', [ManagerController::class, 'destroyItem'])->name('items.destroy');
    // Item Images
    Route::post('/items/{item}/image', [ManagerController::class, 'uploadItemImage'])->name('items.uploadImage');
    Route::delete('/item-images/{image}', [ManagerController::class, 'destroyItemImage'])->name('items.destroyImage');
    Route::patch('/item-images/{image}/set-main', [ManagerController::class, 'setMainImage'])->name('items.setMainImage');

    // 5. Item Types
    Route::post('/types', [ManagerController::class, 'storeType'])->name('types.store');
    Route::match(['put', 'patch'], '/types/{type:id}', [ManagerController::class, 'updateType'])->name('types.update');
    Route::delete('/types/{type:id}', [ManagerController::class, 'destroyType'])->name('types.destroy');

    // 6. Units
    Route::post('/units', [ManagerController::class, 'storeUnit'])->name('units.store');
    Route::match(['put', 'patch'], '/units/{unit:id}', [ManagerController::class, 'updateUnit'])->name('units.update');
    Route::delete('/units/{unit:id}', [ManagerController::class, 'destroyUnit'])->name('units.destroy');

    // 7. Accessories
    Route::post('/accessories', [ManagerController::class, 'storeAccessory'])->name('accessories.store');
    Route::match(['put', 'patch'], '/accessories/{accessory}', [ManagerController::class, 'updateAccessory'])->name('accessories.update');
    Route::delete('/accessories/{accessory}', [ManagerController::class, 'destroyAccessory'])->name('accessories.destroy');

    // 8. Care Shops
    Route::post('/care-shops', [ManagerController::class, 'storeCareShop'])->name('care_shops.store');
    Route::match(['put', 'patch'], '/care-shops/{care_shop:care_shop_id}', [ManagerController::class, 'updateCareShop'])->name('care_shops.update');
    Route::delete('/care-shops/{care_shop:care_shop_id}', [ManagerController::class, 'destroyCareShop'])->name('care_shops.destroy');

    // 9. Makeup Artists
    Route::post('/makeup-artists', [ManagerController::class, 'storeMakeupArtist'])->name('makeup_artists.store');
    Route::match(['put', 'patch'], '/makeup-artists/{makeup_artist:makeup_id}', [ManagerController::class, 'updateMakeupArtist'])->name('makeup_artists.update');
    Route::delete('/makeup-artists/{makeup_artist:makeup_id}', [ManagerController::class, 'destroyMakeupArtist'])->name('makeup_artists.destroy');

    // 10. Photographers
    Route::post('/photographers', [ManagerController::class, 'storePhotographer'])->name('photographers.store');
    Route::match(['put', 'patch'], '/photographers/{photographer:photographer_id}', [ManagerController::class, 'updatePhotographer'])->name('photographers.update');
    Route::delete('/photographers/{photographer:photographer_id}', [ManagerController::class, 'destroyPhotographer'])->name('photographers.destroy');

    // 11. Photographer Packages
    Route::post('/photographer-packages', [ManagerController::class, 'storePhotographerPackage'])->name('photographer_packages.store');
    Route::match(['put', 'patch'], '/photographer-packages/{photographer_package:package_id}', [ManagerController::class, 'updatePhotographerPackage'])->name('photographer_packages.update');
    Route::delete('/photographer-packages/{photographer_package:package_id}', [ManagerController::class, 'destroyPhotographerPackage'])->name('photographer_packages.destroy');

    // 12. Promotions
    Route::post('/promotions', [ManagerController::class, 'storePromotion'])->name('promotions.store');
    Route::match(['put', 'patch'], '/promotions/{promotion:promotion_id}', [ManagerController::class, 'updatePromotion'])->name('promotions.update');
    Route::delete('/promotions/{promotion:promotion_id}', [ManagerController::class, 'destroyPromotion'])->name('promotions.destroy');

    // Payments (สำหรับ Manager ถ้าต้องการ)
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::match(['put', 'patch'], '/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
});


// ====================================================
// 👩‍💼 Reception Routes (พนักงานต้อนรับ)
// ====================================================
Route::middleware(['auth'])->prefix('reception')->name('reception.')->group(function () {
    // หน้าหลัก
    Route::get('/rental', [ReceptionController::class, 'index'])->name('rental');
    Route::post('/rental', [ReceptionController::class, 'storeRental'])->name('storeRental');

    // APIs
    Route::get('/api/check-member', [ReceptionController::class, 'checkMember'])->name('checkMember');
    Route::get('/api/search-items', [ReceptionController::class, 'searchItems'])->name('searchItems');

    // Return System (คืนชุด)
    Route::get('/return', [ReceptionController::class, 'returnIndex'])->name('return');
    Route::post('/return/{rental}', [ReceptionController::class, 'processReturn'])->name('processReturn');

    // History & Calendar
    Route::get('/history', [ReceptionController::class, 'history'])->name('history');
    Route::get('/services-history', [ReceptionController::class, 'serviceHistory'])->name('serviceHistory');
    Route::get('/payment-history', [ReceptionController::class, 'paymentHistory'])->name('paymentHistory');
    Route::get('/point-history', [ReceptionController::class, 'pointHistory'])->name('pointHistory');

    Route::get('/calendar', [ReceptionController::class, 'calendar'])->name('calendar');
    Route::get('/calendar/events', [ReceptionController::class, 'getCalendarEvents'])->name('calendar.events');

    // Payments (สำหรับ Reception)
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::match(['put', 'patch'], '/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
});


// ====================================================
// 🔧 Maintenance Routes (ซัก-ซ่อม) - ใช้ร่วมกันได้
// ====================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance/{id}/send', [MaintenanceController::class, 'sendToShop'])->name('maintenance.send');
    Route::post('/maintenance/{id}/receive', [MaintenanceController::class, 'receiveFromShop'])->name('maintenance.receive');
});

// Authentication Routes (Breeze)
require __DIR__ . '/auth.php';
