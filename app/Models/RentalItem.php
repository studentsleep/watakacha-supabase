<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalItem extends Model
{
    use HasFactory;

    protected $table = 'rental_items';
    // ถ้าใน Migration ใช้ $table->id(); ก็ไม่ต้องระบุ primaryKey (ใช้ default 'id')
    // แต่ถ้ากำหนดชื่ออื่นต้องระบุที่นี่

    protected $fillable = [
        'rental_id',
        'item_id',
        'accessory_id',
        'quantity',
        'price',
        'fine_amount',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'fine_amount' => 'decimal:2',
    ];

    /* --- ความสัมพันธ์ (Relationships) --- */

    /**
     * รายการนี้อยู่ในการเช่าครั้งไหน
     */
    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    /**
     * รายการนี้คือสินค้าอะไร
     */
    public function item()
    {
        // เชื่อมกับ Model Item โดยใช้ 'id' ในตารางนี้ เชื่อมกับ 'id' ในตาราง items
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function accessory()
    {
        // เชื่อมกับ Model Accessory (ต้องแน่ใจว่ามี Model นี้แล้ว)
        return $this->belongsTo(Accessory::class, 'accessory_id', 'id');
    }

    public function getNameAttribute()
    {
        if ($this->item_id) {
            return $this->item->item_name ?? 'ไม่มีไอเทม';
        } elseif ($this->accessory_id) {
            return $this->accessory->name ?? 'ไม่มีอุปกรณ์เสริม'; // สมมติใน Accessory ใช้ name
        }
        return '-';
    }
}
