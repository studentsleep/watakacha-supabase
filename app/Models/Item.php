<?php

namespace App\Models; // (ใช้ Namespace เดิมของคุณ ถ้ามี)
// หรือ namespace App\Models; (ถ้าคุณใช้แบบนี้)

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// (ถ้า Model เดิมของคุณไม่ได้อยู่ใน App\Models ให้แก้บรรทัดล่างนี้)
use App\Models\ItemType; 
use App\Models\ItemUnit;
use App\Models\ItemImage;


class Item extends Model
{
    use HasFactory;

    // protected $primaryKey = 'item_id'; // <-- ลบ หรือ คอมเมนต์บรรทัดนี้ (เพราะ PK เดิมคือ 'id')
    protected $primaryKey = 'id'; // หรือใช้ 'id' ให้ชัดเจน (เหมือนไฟล์เดิมของคุณ)
    
    protected $fillable = [
        'item_name',
        'description',
        'price',
        'stock',
        'item_unit_id',
        'item_type_id',
        'status',
    ];

    // ความสัมพันธ์กับ ItemType (แก้ไข Foreign Key)
    public function type()
    {
        // (ไฟล์ใหม่คือ 'item_type_id', 'item_type_id')
        return $this->belongsTo(ItemType::class, 'item_type_id', 'id');
    }

    // ความสัมพันธ์กับ ItemUnit (แก้ไข Foreign Key)
    public function unit()
    {
        // (ไฟล์ใหม่คือ 'item_unit_id', 'item_unit_id')
        return $this->belongsTo(ItemUnit::class, 'item_unit_id', 'id');
    }

    // ความสัมพันธ์กับหลายรูป (แก้ไข Foreign Key)
    public function images()
    {
        // (ไฟล์ใหม่คือ 'item_id', 'item_id')
        return $this->hasMany(ItemImage::class, 'item_id', 'id');
    }

    // ความสัมพันธ์กับรูปหลัก (แก้ไข Foreign Key)
    public function mainImage()
    {
        // (ไฟล์ใหม่คือ 'item_id', 'item_id')
        return $this->hasOne(ItemImage::class, 'item_id', 'id')->where('is_main', true);
    }
}