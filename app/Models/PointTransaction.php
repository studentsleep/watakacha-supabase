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
        'rental_id',
        'change_type',  // <--- ใน Controller คุณใช้ transaction_type (ผิด)
        'point_change', // <--- ใน Controller คุณใช้ points (ผิด)
        'description',
        'transaction_date',
        'created_at',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'point_change' => 'integer',
        'created_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(MemberAccount::class, 'member_id', 'member_id');
    }
}
