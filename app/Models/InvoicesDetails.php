<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicesDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_number',
        'invoices_id',
        'product',
        'Section',
        'Status',
        'Value_status',
        'note',
        'Payment_Date',
        'user',
    ];
}
