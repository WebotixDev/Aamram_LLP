<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expense';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        	'id' ,
            'user_id',
            'expense_name',
        	'exp_no',
        	'billdate',
        	'exp_type',
        	'inc_by',
            'mode',
        	'date',
        	'season',
        	'cheque_no',
        	'cheque_amt',
        	'amt_pay'	,
        	'narration'	,
        	'created_at',
        	'season',
        	'updated_at',
        	'update_id',
           'expense_receipt',



    ];


}
