<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // (อันนี้ถูกต้อง)

    protected $fillable = [
        'item_name',
        'description',
        'price',
        'stock',
        'id',
        'id',
        'status',
    ];

    /**
     * [แก้ไข] Foreign Key 'id' เชื่อมกับ 'id' ของตาราง ItemType
     */
    public function type()
    {
        return $this->belongsTo(ItemType::class, 'id', 'id');
    }

    /**
     * [แก้ไข] Foreign Key 'id' เชื่อมกับ 'id' ของตาราง ItemUnit
     */
    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'id', 'id');
    }

    // (อันนี้ถูกต้อง)
    public function images()
    {
        return $this->hasMany(ItemImage::class, 'item_id', 'id');
    }
}