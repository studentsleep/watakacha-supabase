<?php



use App\Http\Controllers\ProfileController;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ManagerController;



Route::get('/', function () {

    return view('welcome');

});



Route::get('/dashboard', function () {

    return view('dashboard');

})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});



// --- ▼▼▼ กลุ่ม Route สำหรับ Manager Controller ▼▼▼ ---

// เราจะใช้ middleware 'auth' (และ 'admin' ถ้าคุณมี)

Route::middleware(['auth'])->group(function () {



    // 1. หน้า Index หลัก (ใช้ 'manager.' เป็น prefix ของ name)

    Route::get('/manager', [ManagerController::class, 'index'])->name('manager.index');



    // 2. Routes สำหรับ Items (ตัวอย่างจากที่คุณให้มา)

    Route::post('/manager/items', [ManagerController::class, 'storeItem'])->name('manager.storeItem');

    Route::patch('/manager/items/{item}', [ManagerController::class, 'updateItem'])->name('manager.updateItem');

    Route::delete('/manager/items/{item}', [ManagerController::class, 'destroyItem'])->name('manager.destroyItem');

    // (Routes สำหรับ Item Images)

    Route::post('/manager/items/{item}/image', [ManagerController::class, 'uploadItemImage'])->name('manager.uploadItemImage');

    Route::delete('/manager/item-images/{image}', [ManagerController::class, 'destroyItemImage'])->name('manager.destroyItemImage');

    Route::patch('/manager/item-images/{image}/set-main', [ManagerController::class, 'setMainImage'])->name('manager.setMainImage');



    // 3. Routes สำหรับ Item Units

    Route::post('/manager/units', [ManagerController::class, 'storeUnit'])->name('manager.storeUnit');

    Route::patch('/manager/units/{unit}', [ManagerController::class, 'updateUnit'])->name('manager.updateUnit');

    Route::delete('/manager/units/{unit}', [ManagerController::class, 'destroyUnit'])->name('manager.destroyUnit');



    // 4. Routes สำหรับ Item Types

    Route::post('/manager/types', [ManagerController::class, 'storeType'])->name('manager.storeType');

    Route::patch('/manager/types/{type}', [ManagerController::class, 'updateType'])->name('manager.updateType');

    Route::delete('/manager/types/{type}', [ManagerController::class, 'destroyType'])->name('manager.destroyType');



    // 5. Routes สำหรับ Users (บัญชีผู้ใช้)

    Route::post('/manager/users', [ManagerController::class, 'storeUser'])->name('manager.storeUser');

    Route::patch('/manager/users/{user}', [ManagerController::class, 'updateUser'])->name('manager.updateUser');

    Route::delete('/manager/users/{user}', [ManagerController::class, 'destroyUser'])->name('manager.destroyUser');



    // 6. Routes สำหรับ User Types

    Route::post('/manager/user-types', [ManagerController::class, 'storeUserType'])->name('manager.storeUserType');

    Route::patch('/manager/user-types/{user_type}', [ManagerController::class, 'updateUserType'])->name('manager.updateUserType');

    Route::delete('/manager/user-types/{user_type}', [ManagerController::class, 'destroyUserType'])->name('manager.destroyUserType');

});

// --- ▲▲▲ จบกลุ่ม Route สำหรับ Manager ---



require __DIR__ . '/auth.php';