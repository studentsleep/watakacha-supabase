<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $primaryKey = 'id'; // (อ้างอิงจาก item.png)

    protected $fillable = [
        'item_name',
        'stock',
        'price',
        'status',
        'description',
        'item_type_id',
        'item_unit_id',
    ];

    /**
     * ความสัมพันธ์: Item 1 ชิ้น "เป็นของ" ItemType 1 ประเภท
     */
    public function type()
    {
        return $this->belongsTo(ItemType::class, 'item_type_id', 'id');
    }

    /**
     * ความสัมพันธ์: Item 1 ชิ้น "เป็นของ" ItemUnit 1 หน่วย
     */
    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'item_unit_id', 'id');
    }

    /**
     * ความสัมพันธ์: Item 1 ชิ้น "มี" รูปภาพได้หลายรูป
     */
    public function images()
    {
        return $this->hasMany(ItemImage::class, 'item_id', 'id');
    }
}