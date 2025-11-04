<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    use HasFactory;

    protected $table = 'item_images';
    protected $primaryKey = 'id';

    protected $fillable = [
        'item_id',
        'path',
        'is_main',
    ];

    /**
     * ความสัมพันธ์: 1 รูปภาพ "เป็นของ" สินค้า 1 ชิ้น
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
