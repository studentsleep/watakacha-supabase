<?php

namespace App\Models;

// [แก้ไข] ลบ 'MustVerifyEmail' ออกจากบรรทัด use
// use Illuminate\Contracts\Auth\MustVerifyEmail; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// [แก้ไข] ลบ 'implements MustVerifyEmail' ออกจากบรรทัดนี้
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * [ถูกต้อง] กำหนดตารางและ Primary Key
     */
    protected $table = 'user_accounts';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'user_type_id',
        'tel',
        'status',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * [ถูกต้อง] ความสัมพันธ์กับ UserType
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id', 'user_type_id');
    }

    /**
     * [ถูกต้อง] ความสัมพันธ์กับ MemberAccount
     */
    public function memberAccount()
    {
        return $this->hasOne(MemberAccount::class, 'user_id', 'user_id');
    }
}