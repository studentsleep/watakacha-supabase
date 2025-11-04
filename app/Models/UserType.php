<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class UserType extends Model

{

    use HasFactory;



    /**

     * 1. บอก Laravel ว่าตารางชื่อ user_types

     */

    protected $table = 'user_types';



    /**

     * 2. (สำคัญที่สุด) บอก Laravel ว่า Primary Key คือ user_type_id

     */

    protected $primaryKey = 'user_type_id';



    /**

     * 3. อนุญาตให้กรอกข้อมูล 2 field นี้

     */

    protected $fillable = ['name', 'description'];



    /**

     * 4. สร้างความสัมพันธ์ (เผื่อใช้)

     * (ตารางของคุณมี timestamps เราจึง "ไม่" ใส่ public $timestamps = false;)

     */

    public function users()

    {

        return $this->hasMany(User::class, 'user_type_id', 'user_type_id');

    }

}