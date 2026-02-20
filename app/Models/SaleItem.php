<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'price',
        'qty',
        'total',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
