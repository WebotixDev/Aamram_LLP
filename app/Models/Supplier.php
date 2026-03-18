<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        	'id' ,
            'user_id',
        	'supplier_name',
            'mobile_no',
            'address',
        	'created_at',
        	'updated_at',
        	'update_id',
            'gstin',
            'products',


    ];


}
