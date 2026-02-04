<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'accessory_id', // เพิ่มตาม Migration ที่เคยทำ
        'rental_id',
        'care_shop_id',
        'type',
        'status',
        'damage_description',
        'shop_cost',    // ค่าซ่อมที่เรียกเก็บลูกค้า (หรือราคาประเมิน)
        'actual_cost',  // ✅ เพิ่ม: ค่าซ่อมจริงที่ร้านจ่ายออกไป
        'sent_at',
        'received_at',
    ];

    protected $casts = [
        'shop_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'accessory_id');
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id'); // PK ของ Rental คือ rental_id
    }

    public function careShop()
    {
        return $this->belongsTo(CareShop::class, 'care_shop_id', 'care_shop_id');
    }
}
