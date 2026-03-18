<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';
    public $incrementing = false;
    // protected $keyType = 'uuid';
	protected $primaryKey = 'id';

    protected $fillable = [
        	'id',
        	'userID',	
        	'bank_name',	
        	'ACNo',	
        	'Branch',	
        	'IFSC',	
        	'account_name',	
        	'accounttype',	
        	'default_bank',	
        	'created_at',	
        	'updated_at',	
        	'update_id',
    
    ];

}