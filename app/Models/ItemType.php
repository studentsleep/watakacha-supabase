<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use HasFactory;

    // [แก้ไข] Primary Key ของตารางนี้คือ 'id'
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'description'];

    // (ตั้งค่า timestamps เป็น false ถ้าตารางนี้ไม่มี created_at/updated_at)
    // public $timestamps = false;
}