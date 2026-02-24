<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustmentLog extends Model
{
    protected $fillable = [
        'product_id',
        'qty',
        'type',
        'reason',
        'created_by',
    ];
    
}
