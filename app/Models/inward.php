<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inward extends Model
{
    use HasFactory;

    protected $table = 'purchase_details';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'id',	
        'user_id',
        'updateduser',
        'PurchaseDate',
        'billdate',
        'product',
        'product_size',
        'stock',
        'qty',
        'batch_id',
        'Tquantity',
        'update_id',
        'season',
        'type',
    ];

    /**
     * Relationship: Get all the purchase products for this purchase detail.
     */
    public function details()
    {
        return $this->hasMany(inward_details::class, 'pid', 'id');
    }

    
}
