<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MakeupArtist;
use App\Models\Photographer;
use App\Models\PhotographerPackage;

class Rental extends Model
{
    use HasFactory;

    protected $table = 'rentals';
    protected $primaryKey = 'rental_id';

    // กำหนด Status เป็น Constant เพื่อเรียกใช้สะดวกและลดความผิดพลาด
    const STATUS_PENDING_PAYMENT = 'pending_payment'; // รอชำระเงิน
    const STATUS_AWAITING_PICKUP = 'awaiting_pickup'; // ชำระแล้ว รอรับของ
    const STATUS_RENTED = 'rented';                   // รับของแล้ว/กำลังเช่า
    const STATUS_RETURNED = 'returned';               // คืนแล้ว
    const STATUS_CANCELLED = 'cancelled';             // ยกเลิก

    protected $fillable = [
        'member_id',
        'user_id',
        'promotion_id',
        'makeup_id',
        'photographer_id',
        'package_id',
        'rental_date',
        'return_date',
        'total_amount',
        'fine_amount', // อย่าลืมเพิ่ม field นี้ถ้ายังไม่มี
        'status',
        'description',
        'makeup_cost',
        'photographer_cost',
        'service_cost_status',
    ];

    protected $casts = [
        'rental_date' => 'date',
        'return_date' => 'date',
        'total_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
    ];

    // --- Relationships (คงเดิม) ---

    public function member()
    {
        return $this->belongsTo(MemberAccount::class, 'member_id', 'member_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'promotion_id');
    }

    public function items()
    {
        return $this->hasMany(RentalItem::class, 'rental_id', 'rental_id');
    }

    public function accessories()
    {
        return $this->belongsToMany(Accessory::class, 'rental_accessories', 'rental_id', 'accessory_id')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'rental_id', 'rental_id');
    }

    // --- Helpers ---

    // ยอดจ่ายแล้วทั้งหมด
    public function getTotalPaidAttribute()
    {
        return $this->payments->where('status', 'paid')->sum('amount');
    }

    // เช็คว่าสถานะนี้ ถือว่าเป็นการ "จองของ" หรือไม่ (ใช้คำนวณ Stock Type A)
    public static function isStatusReserved($status)
    {
        return in_array($status, [
            self::STATUS_PENDING_PAYMENT, // จองตั้งแต่ยังไม่จ่าย
            self::STATUS_AWAITING_PICKUP,
            self::STATUS_RENTED
        ]);
    }

    public function makeupArtist()
    {
        // Parameter 2: ชื่อ column ในตาราง rentals (makeup_id)
        // Parameter 3: ชื่อ column primary key ในตาราง makeup_artists (makeup_id)
        return $this->belongsTo(MakeupArtist::class, 'makeup_id', 'makeup_id');
    }

    // เชื่อมกับตาราง photographers (ช่างภาพ)
    public function photographer()
    {
        return $this->belongsTo(Photographer::class, 'photographer_id', 'photographer_id');
    }

    // เชื่อมกับตาราง photographer_packages (แพ็คเกจ)
    public function photographerPackage()
    {
        return $this->belongsTo(PhotographerPackage::class, 'package_id', 'package_id');
    }
}
