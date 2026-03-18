<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sale_order extends Model
{
    use HasFactory;

    protected $table = 'sale_order';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'id',
        'user_id',
        'pid',
        'services',
        'size',
        'stage',
        'transper',
        'rate',
        'Quantity',
        'qty',
        'gstper',
        'amount',
        'update_id',
        'created_at',
        'updated_at',
    ];


    /**
     * Define the inverse relationship with sale_details.
     */
    public function SaleOrderDetails_()
    {
        return $this->belongsTo(sale_details::class, 'pid', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'services' ,'id'); 
    }

    public function productDetail()
    {
        return $this->belongsTo(Product_details::class, 'size','id'); 
    }
}
