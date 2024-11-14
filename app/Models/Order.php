<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_code';
    public $incrementing = false;
    protected $keyType = 'string';  

    protected $fillable = [
        'order_code',
        'customer_address_id',
        'payment_name',
        'shipment_name',
        'order_date',
        'shopping_cost',
        'payment_status',
        'payment_date',
        'shipment_status',
        'total_amount'
    ];

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id', 'customer_address_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_code', 'order_code');
    }
}
