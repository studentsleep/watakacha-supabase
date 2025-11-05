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
    
    // --- ▼▼▼ [แก้ไข] กลุ่ม Route สำหรับ Manager (ระบุ Key ให้ชัดเจน) ▼▼▼ ---
    Route::middleware(['auth'])->prefix('manager')->name('manager.')->group(function () {
    
        Route::get('/', [ManagerController::class, 'index'])->name('index');
    
        // Items (PK: id) - ไม่ต้องระบุ Laravel หาเจอ
        Route::post('/items', [ManagerController::class, 'storeItem'])->name('items.store');
        Route::patch('/items/{item}', [ManagerController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{item}', [ManagerController::class, 'destroyItem'])->name('items.destroy');
        Route::post('/items/{item}/image', [ManagerController::class, 'uploadItemImage'])->name('items.uploadImage');
        Route::delete('/item-images/{image}', [ManagerController::class, 'destroyItemImage'])->name('items.destroyImage');
        Route::patch('/item-images/{image}/set-main', [ManagerController::class, 'setMainImage'])->name('items.setMainImage');
    
        // Units (PK: item_unit_id)
        Route::post('/units', [ManagerController::class, 'storeUnit'])->name('units.store');
        Route::patch('/units/{unit:item_unit_id}', [ManagerController::class, 'updateUnit'])->name('units.update');
        Route::delete('/units/{unit:item_unit_id}', [ManagerController::class, 'destroyUnit'])->name('units.destroy');
    
        // Types (PK: item_type_id)
        Route::post('/types', [ManagerController::class, 'storeType'])->name('types.store');
        Route::patch('/types/{type:item_type_id}', [ManagerController::class, 'updateType'])->name('types.update');
        Route::delete('/types/{type:item_type_id}', [ManagerController::class, 'destroyType'])->name('types.destroy');
    
        // Users (PK: user_id)
        Route::post('/users', [ManagerController::class, 'storeUser'])->name('users.store');
        Route::patch('/users/{user:user_id}', [ManagerController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user:user_id}', [ManagerController::class, 'destroyUser'])->name('users.destroy');
    
        // User Types (PK: user_type_id)
        Route::post('/user-types', [ManagerController::class, 'storeUserType'])->name('user_types.store');
        Route::patch('/user-types/{user_type:user_type_id}', [ManagerController::class, 'updateUserType'])->name('user_types.update');
        Route::delete('/user-types/{user_type:user_type_id}', [ManagerController::class, 'destroyUserType'])->name('user_types.destroy');
    });
    // --- ▲▲▲ จบกลุ่ม Route สำหรับ Manager ---
    
    require __DIR__ . '/auth.php';