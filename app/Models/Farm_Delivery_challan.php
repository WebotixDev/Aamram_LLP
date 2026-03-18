<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm_Delivery_challan extends Model
{
    use HasFactory;

    protected $table = 'farm_delivery_challan';


    protected $fillable = [
        'id',
        'user_id',
        'updateduser',
        'challan_date',
        'from_location_id',
        'from_location_name',
        'to_location_id',
        'to_location_name',
        'driver_name',
        'driver_mobile_no',
        'transporter_id',
        'transporter_name',
        'update_id',
        'invoice_no ',
        'Invoicenumber',
        'season',
        'totalamt',
    ];


    public function details()
    {
        return $this->hasMany(Farm_Delivery_challan_details::class, 'pid', 'id');
    }


}
