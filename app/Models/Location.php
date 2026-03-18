<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'location';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        	'id' ,
        	'location',
            'mobile_no',
            'address',
        	'created_at',
        	'updated_at',
            'purchase_manager'


    ];


}
