<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    // อนุญาตให้แก้ไขคอลัมน์ปลายทาง
    protected $fillable = [
        'item_name',
        'description',
        'price',
        'stock',
        'item_type_id', // เพิ่มบรรทัดนี้
        'item_unit_id', // เพิ่มบรรทัดนี้
        'status',
    ];

    // บอก Laravel ว่าคอลัมน์ item_type_id ของเรา เชื่อมกับ id ของอีกตารางนะ
    public function type()
    {
        // belongsTo(Model, 'คอลัมน์ในตารางนี้ (FK)', 'คอลัมน์ในตารางนู้น (PK)')
        return $this->belongsTo(ItemType::class, 'item_type_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'item_unit_id', 'id');
    }

    // (อันนี้ถูกต้อง)
    public function images()
    {
        // แก้เป็น 'item_id' (ชื่อคอลัมน์ในตาราง item_images ที่เก็บรหัสสินค้า)
        return $this->hasMany(ItemImage::class, 'item_id', 'id');
    }
}
