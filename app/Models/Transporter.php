<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    use HasFactory;

    protected $table = 'transporter';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        	'id' ,
            'user_id',
        	'transporter',
            'mobile_no',
            'address',
        	'created_at',
        	'updated_at',
            'gstin',


    ];


}
