<?php 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class District extends Model{
    use HasFactory;
    protected $table = 'districts';
    protected $fillable = [
        'district_id',
        'country_id',
        'state_id',	
        'district_name',
        'date'
    ];
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}