<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;
    protected $table = 'investors_payment'; // Ensure there's no leading space here

    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'id',
        'user_id',
        'updateduser',
        'investor_name',
        'PurchaseDate',
        'amt_pay',
        'mode',
        'cheque_no',
        'narration',
        'pending_amt',
        'type',
        'date',
        'ReceiptNo',
        'balance',
        'created_at',
        'updated_at',
    ];

}
