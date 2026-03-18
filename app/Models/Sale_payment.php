<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale_payment extends Model
{
    use HasFactory;
    protected $table = 'purchase_payments'; // Ensure there's no leading space here

    public $incrementing = false;
    protected $keyType = 'uuid';

        protected $fillable = [
        'id',
        'user_id',
        'updateduser',
        'locationID',
        'customer_name',
        'PurchaseDate',
        'sale_id',
        'amt_pay',
        'mode',
        'cheque_no',
        'narration',
        'pending_amt',
        'cheque_amt',
        'Type',
        'date',
        'totalvalue',
        'ReceiptNo',
        'season',
        'Bill_No',
        'Ptype',
        'debit_amt',
        'bankid',
        'balance',
        'created_at',
        'updated_at',
    ];

    public function details()
    {
        return $this->belongsTo(Sale_paymentDetails::class, 'pid', 'id');
    }
}
