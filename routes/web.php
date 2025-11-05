<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerController; // [แก้ไข] ตรวจสอบว่าเรียกใช้

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

// --- ▼▼▼ กลุ่ม Route สำหรับ Manager (แบบใหม่) ▼▼▼ ---
Route::middleware(['auth'])->prefix('manager')->name('manager.')->group(function () {

    // 1. หน้า Index หลัก (ยังคงใช้ table query)
    Route::get('/', [ManagerController::class, 'index'])->name('index');

    // 2. Routes สำหรับ Items (CRUD ใหม่)
    Route::post('/items', [ManagerController::class, 'storeItem'])->name('items.store');
    Route::patch('/items/{item}', [ManagerController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{item}', [ManagerController::class, 'destroyItem'])->name('items.destroy');
    
    // (Routes สำหรับ Item Images)
    Route::post('/items/{item}/image', [ManagerController::class, 'uploadItemImage'])->name('items.uploadImage');
    Route::delete('/item-images/{image}', [ManagerController::class, 'destroyItemImage'])->name('items.destroyImage');
    Route::patch('/item-images/{image}/set-main', [ManagerController::class, 'setMainImage'])->name('items.setMainImage');

    // 3. Routes สำหรับ Item Units (CRUD ใหม่)
    Route::post('/units', [ManagerController::class, 'storeUnit'])->name('units.store');
    Route::patch('/units/{unit}', [ManagerController::class, 'updateUnit'])->name('units.update');
    Route::delete('/units/{unit}', [ManagerController::class, 'destroyUnit'])->name('units.destroy');

    // 4. Routes สำหรับ Item Types (CRUD ใหม่)
    Route::post('/types', [ManagerController::class, 'storeType'])->name('types.store');
    Route::patch('/types/{type}', [ManagerController::class, 'updateType'])->name('types.update');
    Route::delete('/types/{type}', [ManagerController::class, 'destroyType'])->name('types.destroy');

    // 5. Routes สำหรับ Users (CRUD ใหม่)
    Route::post('/users', [ManagerController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}', [ManagerController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [ManagerController::class, 'destroyUser'])->name('users.destroy');

    // 6. Routes สำหรับ User Types (CRUD ใหม่)
    Route::post('/user-types', [ManagerController::class, 'storeUserType'])->name('user_types.store');
    Route::patch('/user-types/{user_type}', [ManagerController::class, 'updateUserType'])->name('user_types.update');
    Route::delete('/user-types/{user_type}', [ManagerController::class, 'destroyUserType'])->name('user_types.destroy');
});
// --- ▲▲▲ จบกลุ่ม Route สำหรับ Manager ---

require __DIR__ . '/auth.php';