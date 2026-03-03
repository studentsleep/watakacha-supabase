<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
protected $table = 'services';
protected $primaryKey = 'service_id';

protected $fillable = [
'rental_id', 'makeup_id', 'photographer_id', 'package_id',
'service_price', 'service_cost', 'status', 'description'
];

// เชื่อมกลับไปหา Rental
public function rental() {
return $this->belongsTo(Rental::class, 'rental_id');
}
// เชื่อมไปหาช่าง
public function makeupArtist() {
return $this->belongsTo(MakeupArtist::class, 'makeup_id');
}
public function photographer() {
return $this->belongsTo(Photographer::class, 'photographer_id');
}
}