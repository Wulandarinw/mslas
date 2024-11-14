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
        'total_amount',
    ];

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id', 'customer_address_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_code', 'order_code');
    }

    public function scopeFilter($query, array $filters): Builder
    {
        $query->when(isset($filters['order_code']), function ($query, $value) {
            $query->where('order_code', $value);
        });

        $query->when(isset($filters['customer_address_id']), function ($query, $value) {
            $query->where('customer_address_id', $value);
        });

        $query->when(isset($filters['payment_name']), function ($query, $value) {
            $query->where('payment_name', 'like', "%$value%");
        });

        $query->when(isset($filters['shipment_name']), function ($query, $value) {
            $query->where('shipment_name', 'like', "%$value%");
        });

        $query->when(isset($filters['order_date']), function ($query, $value) {
            // Assuming order_date is a date column
            $query->whereDate('order_date', $value);
        });

        $query->when(isset($filters['shopping_cost']), function ($query, $value) {
            $query->where('shopping_cost', $value);
        });

        $query->when(isset($filters['payment_status']), function ($query, $value) {
            $query->where('payment_status', $value);
        });

        $query->when(isset($filters['payment_date']), function ($query, $value) {
            // Assuming payment_date is a date column
            $query->whereDate('payment_date', $value);
        });

        $query->when(isset($filters['shipment_status']), function ($query, $value) {
            $query->where('shipment_status', $value);
        });

        $query->when(isset($filters['total_amount']), function ($query, $value) {
            $query->where('total_amount', $value);
        });

        return $query;
    }
}