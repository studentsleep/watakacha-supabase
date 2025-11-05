<?php

namespace App\Models; // [แก้ไข] ใช้ Namespace นี้

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // [แก้ไข] ใช้ 'id'
    
    // [แก้ไข] ใช้ fillable จาก Controller ใหม่
    protected $fillable = [
        'item_name',
        'description',
        'price',
        'stock',
        'item_unit_id',
        'item_type_id',
        'status',
    ];

    // [แก้ไข] ความสัมพันธ์ให้ตรงกับ 'id'
    public function type()
    {
        return $this->belongsTo(ItemType::class, 'item_type_id', 'id');
    }

    // [แก้ไข] ความสัมพันธ์ให้ตรงกับ 'id'
    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'item_unit_id', 'id');
    }

    // [แก้ไข] ความสัมพันธ์ให้ตรงกับ 'id'
    public function images()
    {
        return $this->hasMany(ItemImage::class, 'item_id', 'id');
    }

    // [แก้ไข] ความสัมพันธ์ให้ตรงกับ 'id'
    public function mainImage()
    {
        return $this->hasOne(ItemImage::class, 'item_id', 'id')->where('is_main', true);
    }
}