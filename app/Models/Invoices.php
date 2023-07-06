<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Invoices extends Model
{
    use HasFactory;
    use  SoftDeletes;
    // protected $guarded = [];
    Protected $fillable = [
        'invoice_number',
        'invoice_Date',
        'Due_date',
        'section_id',
        'product',
        'Amount_collection',
        'Amount_commission',
        'Discount',
        'Rate_vat',
        'Value_vat',
        'Total',
        'Status',
        'value_status',
        'note',
        'Payment_Date'
    ];
    protected $dates = ['deleted_at'];
    public function sections(): BelongsTo
{
    return $this->belongsTo(Sections::class,'section_id');
}
}
