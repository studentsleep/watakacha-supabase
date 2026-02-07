<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalAccessory extends Model
{
    use HasFactory;

    protected $table = 'rental_accessories'; // ชี้ไปที่ตาราง Pivot เดิม

    protected $fillable = [
        'rental_id',
        'accessory_id',
        'quantity',
        'price',
    ];

    // เชื่อมกับตาราง Accessory เพื่อดึงชื่ออุปกรณ์
    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'accessory_id');
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id');
    }
}
