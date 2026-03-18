<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_size extends Model
{
    use HasFactory;

    protected $table = 'product_size';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        	'id' ,
            'product_size',
        	'type',
        	'status',
    ];


}
