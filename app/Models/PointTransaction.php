<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    protected $table = 'point_transactions';
    protected $primaryKey = 'transaction_id'; // ตรวจสอบว่าใน DB ชื่อนี้จริงไหม หรือเป็น id

    protected $fillable = [
        'member_id',
        'points',
        'transaction_type', // 'earn' (ได้), 'redeem' (ใช้)
        'description',
        'created_at'
    ];

    public function member()
    {
        return $this->belongsTo(MemberAccount::class, 'member_id', 'member_id');
    }
}