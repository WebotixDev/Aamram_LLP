<?php 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class outward_details extends Model
{
    use HasFactory;

    protected $table = 'outward_details';
    public $incrementing = false;
    protected $keyType = 'uuid';


    protected $fillable = [
        'id',
        'user_id',
        'billdate',
        'Invoicenumber',
        'batch_id',
        'customer_name',
        'order_no',
        'services',
        'size',
        'stage',
        'Quantity',
        'qty',
        'rem_qty',
        'dispatch',
        'currdispatch_qty',
        'gst',
        'season',
        'update_id',
        'flag',
    ];

    
}