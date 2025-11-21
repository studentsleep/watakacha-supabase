<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    /**
     * ระบุชื่อตารางและ Primary Key
     */
    protected $table = 'rentals';
    protected $primaryKey = 'rental_id';

    /**
     * ข้อมูลที่อนุญาตให้เพิ่ม/แก้ไขได้ผ่าน Mass Assignment
     */
    protected $fillable = [
        'member_id',
        'user_id',
        'promotion_id',
        'rental_date',
        'return_date',
        'total_amount',
        'status', // rented, returned, overdue
        'description',
    ];

    /**
     * แปลงประเภทข้อมูลอัตโนมัติ
     */
    protected $casts = [
        'rental_date' => 'date',
        'return_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /* --- ความสัมพันธ์ (Relationships) --- */

    /**
     * การเช่านี้เป็นของสมาชิกคนไหน
     */
    public function member()
    {
        return $this->belongsTo(MemberAccount::class, 'member_id', 'member_id');
    }

    /**
     * การเช่านี้ทำรายการโดยพนักงานคนไหน
     */
    public function user()
    {
        // ตรวจสอบว่า Model User ของคุณชื่อ 'User' และ PK คือ 'user_id' หรือ 'id'
        // ถ้า PK ของ User คือ 'user_id' ให้ใช้:
        return $this->belongsTo(User::class, 'user_id', 'user_id');
        
        // ถ้า PK ของ User คือ 'id' (default Laravel) ให้ใช้:
        // return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * การเช่านี้มีโปรโมชั่นอะไร (ถ้ามี)
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'promotion_id');
    }

    /**
     * การเช่านี้มีรายการสินค้าอะไรบ้าง (One-to-Many)
     */
    public function items()
    {
        return $this->hasMany(RentalItem::class, 'rental_id', 'rental_id');
    }
}