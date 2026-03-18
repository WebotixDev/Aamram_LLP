<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery_Challan extends Model
{
    use HasFactory;

    protected $table = 'delivery_challan';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        	'id' ,
            'user_id',
            'Invoicenumber',
        	'billdate',
        	'created_at',
        	'updated_at',
        	'transporter'


    ];
    public function details()
    {
        return $this->hasMany(Delivery_ChallanDetails::class, 'pid', 'id');
    }

}
