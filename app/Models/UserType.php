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
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'description'];

    /**
     * [แก้ไข] ความสัมพันธ์กับ Users ให้ตรงกับ 'id'
     */
    public function users()
    {
        return $this->hasMany(User::class, 'user_type_id', 'id');
    }
}