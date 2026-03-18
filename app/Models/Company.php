<?php 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'company';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
            	'id' ,
                'user_id',
              	'name'	,
            	'code'	,
            	'website',
            	'address',	
            	'phone'	,
            	'mobile',	
            	'email'	,
            	'logo'	,
            	'CIN'	,
            	'option'	,
            	'password'	,
            	'gst_no'	,
            	'SMTP_HOST'	,
            	'port',	
            	'user',
            	'pass',
            	'IMAP_HOST'	,
            	'IMAP_PORT',
            	'trans_cost',
            	'update_id'	,
            	'adhar'	,
            	'sign',
            	'days',
                'created_at',
                'updated_at',
        	
    ];

    
}