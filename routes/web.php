<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); // <-- คุณขอให้ปิด 'verified' ในภายหลัง

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- ▼▼▼ [แก้ไข] กลุ่ม Route สำหรับ Manager (ใช้ PK ที่ถูกต้อง) ▼▼▼ ---
Route::middleware(['auth'])->prefix('manager')->name('manager.')->group(function () {

    Route::get('/', [ManagerController::class, 'index'])->name('index');

    // Items (PK: id) - (อันนี้ไม่ต้องระบุ Laravel หาเจอ)
    Route::post('/items', [ManagerController::class, 'storeItem'])->name('items.store');
    Route::patch('/items/{item}', [ManagerController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{item}', [ManagerController::class, 'destroyItem'])->name('items.destroy');
    Route::post('/items/{item}/image', [ManagerController::class, 'uploadItemImage'])->name('items.uploadImage');
    Route::delete('/item-images/{image}', [ManagerController::class, 'destroyItemImage'])->name('items.destroyImage');
    Route::patch('/item-images/{image}/set-main', [ManagerController::class, 'setMainImage'])->name('items.setMainImage');

    // Units (PK: id)
    Route::post('/units', [ManagerController::class, 'storeUnit'])->name('units.store');
    // ระบุ Key ที่ใช้ค้นหาใน Route:
    Route::patch('/units/{unit:id}', [ManagerController::class, 'updateUnit'])->name('units.update');
    Route::delete('/units/{unit:id}', [ManagerController::class, 'destroyUnit'])->name('units.destroy');

    // Types (PK: id)
    Route::post('/types', [ManagerController::class, 'storeType'])->name('types.store');
    // ระบุ Key ที่ใช้ค้นหาใน Route:
    Route::patch('/types/{type:id}', [ManagerController::class, 'updateType'])->name('types.update');
    Route::delete('/types/{type:id}', [ManagerController::class, 'destroyType'])->name('types.destroy');

    // Users (PK: user_id)
    Route::post('/users', [ManagerController::class, 'storeUser'])->name('users.store');
    // ระบุ Key ที่ใช้ค้นหาใน Route:
    Route::patch('/users/{user:user_id}', [ManagerController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user:user_id}', [ManagerController::class, 'destroyUser'])->name('users.destroy');

    // User Types (PK: user_type_id)
    Route::post('/user-types', [ManagerController::class, 'storeUserType'])->name('user_types.store');
    // ระบุ Key ที่ใช้ค้นหาใน Route:
    Route::patch('/user-types/{user_type:user_type_id}', [ManagerController::class, 'updateUserType'])->name('user_types.update');
    Route::delete('/user-types/{user_type:user_type_id}', [ManagerController::class, 'destroyUserType'])->name('user_types.destroy');
});
// --- ▲▲▲ จบกลุ่ม Route สำหรับ Manager ---

require __DIR__ . '/auth.php';