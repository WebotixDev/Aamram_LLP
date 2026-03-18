<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery_ChallanDetails extends Model
{
    use HasFactory;

    protected $table = 'delivery_challan_details';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        	'otid',
            'pid',
        	'user_id',
        	'billdate',
        	'customer_name',
        	'order_no',
            'pid'	,
            'services'	,
            'size'	,
            'stage'	,
            'qty',
            'Quantity',
            'rem_qty',
           'currdispatch_qty',
           'created_at',
        	'updated_at',


    ];
    public function challan()
    {
        return $this->belongsTo(Delivery_Challan::class, 'pid', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'services' ,'id');
    }
    public function productDetail()
    {
        return $this->belongsTo(Product_details::class, 'size','id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_name' ,'id');
    }
}
