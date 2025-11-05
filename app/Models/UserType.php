<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    /**
     * [แก้ไข] ใช้ 'id' เป็น Primary Key (หรือลบบรรทัดนี้)
     */
    protected $primaryKey = 'user_type_id';

    protected $fillable = ['name', 'description'];

    /**
     * [แก้ไข] ความสัมพันธ์กับ Users ให้ตรงกับ 'id'
     */
}