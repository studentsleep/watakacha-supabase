<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MemberAccount extends Authenticatable
{
    use HasFactory;

    protected $table = 'member_accounts';
    protected $primaryKey = 'member_id';

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'tel',
        'status',
        'points',
        'password',
        'line_user_id',
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

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'password' => 'hashed',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(PointTransaction::class, 'member_id', 'member_id');
    }
}
