<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // 1. ระบุชื่อตารางให้ชัดเจน (กันพลาด)
    protected $table = 'payments';

    // 2. PK ของคุณ
    protected $primaryKey = 'payment_id';

    // 3. อนุญาตให้กรอกข้อมูล (Mass Assignment)
    protected $fillable = [
        'rental_id',
        'payment_method', // transfer, cash, credit_card
        'amount',
        'type',           // deposit, remaining, fine
        'status',         // paid, pending
        'payment_date',
    ];

    // 4. แปลงข้อมูลอัตโนมัติ
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // 5. ความสัมพันธ์
    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }
}