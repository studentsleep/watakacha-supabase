<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotographerPackage extends Model
{
    use HasFactory;

    protected $table = 'photographer_packages';
    protected $primaryKey = 'package_id';

    protected $fillable = [
        'package_name',
        'price',
        'description',
    ];
}
