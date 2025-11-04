<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotions';
    protected $primaryKey = 'promotion_id';

    protected $fillable = [
        'promotion_name',
        'discount_type',
        'discount_value',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * แปลง date/time ให้อัตโนมัติ
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
