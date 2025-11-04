<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeupArtist extends Model
{
    use HasFactory;

    protected $table = 'makeup_artists';
    protected $primaryKey = 'makeup_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'tel',
        'email',
        'status',
        'price',
        'description',
    ];
}
