<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ripening_Chamber_details extends Model
{
    use HasFactory;

    protected $table = 'ripening_chamber_details';

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
        'chamber_qty',
        'update_id',
    ];

    /**
     * Relationship: Get the parent purchase detail for this product.
     */
    public function purchaseDetail()
    {
        return $this->belongsTo(Ripening_Chamber::class, 'pid', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'services' ,'id');
    }


}
