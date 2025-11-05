<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemUnit extends Model
{
    use HasFactory;

    /**
     * [แก้ไข] ใช้ 'id' เป็น Primary Key (หรือลบบรรทัดนี้)
     */
    protected $primaryKey = 'id';
    
    protected $fillable = ['name', 'description'];
}