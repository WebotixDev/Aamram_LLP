<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_details extends Model
{
    use HasFactory;

    protected $table = 'product_details';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'id',
        'parentID', // Foreign key to Product
        'user_id',
        'update_id',
        'client_id',
        'product_size',
        'size_id',
        'unit',
        'dist_price',
        'purch_price',
       'discount',
              'status',
         'sizeoff',
         'disable',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'parentID', 'id'); // Define inverse relationship with Product
    }
}
