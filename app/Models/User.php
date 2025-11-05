<?php
    
    namespace App\Models;
    
    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    
    class User extends Authenticatable implements MustVerifyEmail
    {
        use HasFactory, Notifiable;
    
        /**
         * [ถูกต้อง] กำหนดตารางและ Primary Key
         */
        protected $table = 'user_accounts';
        protected $primaryKey = 'user_id';
    
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
    
        protected $hidden = [
            'password',
            'remember_token',
        ];
    
        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
            ];
        }
        
        /**
         * [แก้ไข] ความสัมพันธ์กับ UserType
         * Foreign Key: 'user_type_id' (ในตาราง user_accounts)
         * Owner Key: 'user_type_id' (ในตาราง user_types)
         */
        public function userType()
        {
            return $this->belongsTo(UserType::class, 'user_type_id', 'user_type_id');
        }
    
        /**
         * [แก้ไข] ความสัมพันธ์กับ MemberAccount (ถ้ามี)
         */
        public function memberAccount()
        {
            return $this->hasOne(MemberAccount::class, 'user_id', 'user_id');
        }
    }