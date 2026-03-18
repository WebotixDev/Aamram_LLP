<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'id',
        'user_id',
        'update_id',
        'product_name',
        'product_code',
        'hsn_no',
        'status',
        'description',
        'video_url',
        'moq',
        'img',
         'type',
    ];

    /**
     * Get the details for the product.
     */
    public function details()
    {
        return $this->hasMany(Product_details::class, 'parentID', 'id'); // Define relationship with Product_details
    }
}
