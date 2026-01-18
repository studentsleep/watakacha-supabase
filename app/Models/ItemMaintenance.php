<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'rental_id',
        'care_shop_id',
        'type',
        'status',
        'damage_description',
        'shop_cost',
        'sent_at',
        'received_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // ✅ เชื่อมกับ Rental (ระบุ Key: rental_id)
    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    // ✅ เชื่อมกับ CareShop (ระบุ Key: care_shop_id)
    public function careShop()
    {
        return $this->belongsTo(CareShop::class, 'care_shop_id', 'care_shop_id');
    }
}
