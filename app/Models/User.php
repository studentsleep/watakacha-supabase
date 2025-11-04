<?php

namespace App\Models;

// ... (use ... อื่นๆ)
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- ต้องใช้ Authenticatable
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * 1. บอก Laravel ว่าตารางชื่อ user_accounts (ไม่ใช่ users)
     */
    protected $table = 'user_accounts';

    /**
     * 2. บอก Laravel ว่า Primary Key (PK) คือ user_id (ไม่ใช่ id)
     */
    protected $primaryKey = 'user_id';

    /**
     * 3. (สำคัญมาก) "อนุญาต" ให้ Controller บันทึก Field เหล่านี้
     * (อ้างอิงจากตาราง user_accounts)
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'user_type_id',
        'status',
        'tel',
        // 'auth_user_id', (ถ้าคุณจะใช้)
    ];

    /**
     * 4. (สำคัญที่สุดสำหรับหน้านี้) 
     * สร้าง "ความสัมพันธ์" (Relationship)
     * เพื่อให้ Controller (ที่ใช้ with('userType')) ดึงชื่อประเภทผู้ใช้ได้
     */
    public function userType()
    {
        // (FK 'user_type_id' ในตารางนี้, PK 'user_type_id' ในตาราง UserType)
        return $this->belongsTo(UserType::class, 'user_type_id', 'user_type_id');
    }

    // --- (ส่วนที่เหลือของ Model) ---
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
