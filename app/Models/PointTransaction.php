<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    /**
     * ระบุตารางและ Primary Key
     */
    protected $table = 'point_transactions';
    protected $primaryKey = 'transaction_id';

    /**
     * ระบุว่าไม่มีคอลัมน์ created_at และ updated_at (หากไม่มีในตาราง)
     * (จากรูปของคุณ มี created_at แต่ไม่มี updated_at)
     */
    const UPDATED_AT = null; 

    /**
     * The attributes that are mass assignable.
     * (ข้อมูลที่อนุญาตให้กรอกได้ แม้ว่าเราจะไม่ได้สร้างฟอร์มก็ตาม)
     */
    protected $fillable = [
        'member_id',
        'rental_id',
        'change_type',
        'point_change',
        'description',
        'transaction_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'point_change' => 'integer',
        ];
    }

    /**
     * ความสัมพันธ์: ธุรกรรมนี้เป็นของ Member คนไหน
     */
    public function member()
    {
        return $this->belongsTo(MemberAccount::class, 'member_id', 'member_id');
    }
    
    /**
     * (Optional) ความสัมพันธ์: ธุรกรรมนี้เกี่ยวข้องกับการเช่าครั้งไหน
     * (สมมติว่าคุณมี Model 'Rental' ที่เชื่อมกับตาราง 'rentals')
     */
    // public function rental()
    // {
    //     return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    // }
}