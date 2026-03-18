<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $table = 'season';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        	'id' ,
            'user_id',
            'season',
        	'created_at',
        	'updated_at',


    ];


}
