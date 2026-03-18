<?php 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale_paymentDetails extends Model
{
    use HasFactory;

    protected $table = 'purchase_payment_info';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
            'id',
             'pid',
             'Invoicenumber',
        	 'paymentid',
        	 'purchaseid',
             'amount',
             'payamt',
        	'created_at',
            'updated_at',
       
    ];
}	





