<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $fillable = [
        'id',
        'country_id',
        'state_id',
        'district_id',	
        'customer_name',
        'company_name',
        'mobile_no',
        'vendor',
        'email_id',
        'address',
         'address1',
        'address2',
        'city_name',
        'pin_code',
         'wp_number',


    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function cities()
    {
        return $this->belongsTo(City::class, 'city_name', 'id');
    }
}