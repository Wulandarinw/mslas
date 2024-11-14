<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_item_id';

    protected $fillable = [
        'variation_id',
        'order_code',
        'qty',
    ];

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id', 'variation_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_code', 'order_code');
    }
}
