<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareShop extends Model
{
    use HasFactory;

    protected $table = 'care_shops';
    protected $primaryKey = 'care_shop_id';

    protected $fillable = [
        'care_name',
        'address',
        'tel',
        'email',
        'status',
    ];
}
