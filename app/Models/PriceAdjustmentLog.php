<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceAdjustmentLog extends Model
{
    protected $fillable = [
        'product_id',
        'old_price',
        'new_price',
        'reason',
        'created_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}