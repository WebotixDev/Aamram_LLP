<?php 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_bulk extends Model
{
    use HasFactory;

    protected $table = 'product_bulk';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        	'id' ,
            'user_id',
        	'size',
	    	'rate',	
		    'services',	
        	'created_at',	
        	'updated_at',	
        	'update_id'	
    ];
 
}