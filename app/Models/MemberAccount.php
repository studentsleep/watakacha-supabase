<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// (ถ้า Member ต้อง Login ด้วย ให้ใช้ Authenticatable เหมือน User.php)
// use Illuminate\Foundation\Auth\User as Authenticatable;

class MemberAccount extends Model // หรือ Authenticatable
{
    use HasFactory;

    protected $table = 'member_accounts';
    protected $primaryKey = 'member_id';

    protected $fillable = [
        'auth_user_id',
        'username',
        'first_name',
        'last_name',
        'points',
        'status',
        'tel',
        'email',
        'transaction_id', // (FK สุดท้าย)
        // (ระวัง: ถ้า Member Login ได้ ต้องมี 'password' ด้วย)
    ];

    // (ถ้ามี password)
    // protected $hidden = [ 'password', 'remember_token', ];
    // protected $casts = [ 'password' => 'hashed', ];
}
