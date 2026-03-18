<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class farm_inward_details extends Model
{
    use HasFactory;

    protected $table = 'farm_inward_details';


    protected $fillable = [
        'id',
        'pid',
        'user_id',
        'services',
        'size_name',
        'size',
        'stage',
        'rate',
        'batch_no',
        'batch_number',
        'Quantity',
        'update_id',
    ];

    /**
     * Relationship: Get the parent purchase detail for this product.
     */
    public function purchaseDetail()
    {
        return $this->belongsTo(farm_inward::class, 'pid', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'services' ,'id');
    }


}
