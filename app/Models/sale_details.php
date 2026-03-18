<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sale_details extends Model
{
    use HasFactory;

    protected $table = 'sale_orderdetails';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'user_id',
        'updateduser',
        'PurchaseDate',
        'customer_name',
        'order_address',
        'wholesaler',
        'dispatch',
        'gst',
        'billdate',
        'order_date',
        'batch_id',
        'Tamount',
        'Invoicenumber',
        'Bill_No',
        'discount_per',
        'discount_rupee',
        'subtotal',
        'trans_cost',
        'trans_in_per',
        'trans_in_per_rs',
        'other_charges',
        'CGST',
        'SGST',
        'IGST',
        'mode',
        'amt_pay',
        'narration',
        'update_id',
        'totalproamt',
        'season',
        'import_no',
    ];

    /**
     * Define the relationship with the sale_order model.
     */
    public function details()
    {
        return $this->hasMany(sale_order::class, 'pid', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_name', 'id'); // Assuming 'id' is the primary key in the customers table
    }
 
}
