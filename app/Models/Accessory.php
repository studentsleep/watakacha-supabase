<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'item_type_id',
        'item_unit_id',
    ];

    public function type()
    {
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'item_unit_id');
    }
}