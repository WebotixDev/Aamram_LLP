<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm_Delivery_challan_details extends Model
{
    use HasFactory;

    protected $table = 'farm_delivery_challan_details';

    protected $fillable = [
        'id',
        'pid',
        'user_id',
        'services',
        'size_name',
        'size',
        'stage',
        'batch_number',
        'Quantity',
        'transcost',
        'update_id',
    ];

    /**
     * Relationship: Get the parent purchase detail for this product.
     */
    public function purchaseDetail()
    {
        return $this->belongsTo(Farm_Delivery_challan::class, 'pid', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'services' ,'id');
    }


}
