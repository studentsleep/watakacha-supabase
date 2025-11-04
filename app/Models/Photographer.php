<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photographer extends Model
{
    use HasFactory;

    protected $table = 'photographers';
    protected $primaryKey = 'photographer_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'tel',
        'email',
        'status',
    ];
}
