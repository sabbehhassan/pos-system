<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',   
        'invoice_no',
        'user_id',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'total',
        'payment_method',
        'paid_amount',
        'change_amount',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
