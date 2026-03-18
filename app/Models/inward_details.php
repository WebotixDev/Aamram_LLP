<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inward_details extends Model
{
    use HasFactory;

    protected $table = 'purchase_product';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'id',
        'pid',
        'user_id',
        'services',
        'size',
        'stage',
        'rate',
        'productsizes',
        'batch_id',
        'Quantity',
        'update_id',
    ];

    /**
     * Relationship: Get the parent purchase detail for this product.
     */
    public function purchaseDetail()
    {
        return $this->belongsTo(inward::class, 'pid', 'id');
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
