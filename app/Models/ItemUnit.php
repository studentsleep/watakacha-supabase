<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemUnit extends Model
{
    use HasFactory;

    protected $table = 'item_units';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * ความสัมพันธ์: 1 หน่วย "มี" สินค้าได้หลายชิ้น
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'item_unit_id', 'id');
    }
}
