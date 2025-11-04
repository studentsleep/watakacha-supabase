<?php

// [แก้ไข] เปลี่ยน Namespace ให้ตรงกับที่ Controller เรียกใช้
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// (ดึง Model อื่นๆ ที่เกี่ยวข้องมา)
use App\Models\ItemType;
use App\Models\ItemUnit;
use App\Models\ItemImage;

class Item extends Model
{
    use HasFactory;

    // [แก้ไข] ใช้ Primary Key 'id' (ตาม Database เดิม)
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'item_name',
        'description',
        'price',
        'stock',
        'item_unit_id',
        'item_type_id',
        'status',
    ];

    // [แก้ไข] ความสัมพันธ์กับ ItemType (ใช้ Foreign Key 'id')
    public function type()
    {
        return $this->belongsTo(ItemType::class, 'item_type_id', 'id');
    }

    // [แก้ไข] ความสัมพันธ์กับ ItemUnit (ใช้ Foreign Key 'id')
    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'item_unit_id', 'id');
    }

    // [แก้ไข] ความสัมพันธ์กับหลายรูป (ใช้ Foreign Key 'id')
    public function images()
    {
        return $this->hasMany(ItemImage::class, 'item_id', 'id');
    }

    // [แก้ไข] ความสัมพันธ์กับรูปหลัก (ใช้ Foreign Key 'id')
    public function mainImage()
    {
        return $this->hasOne(ItemImage::class, 'item_id', 'id')->where('is_main', true);
    }
}