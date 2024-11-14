<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $primaryKey = 'customer_address_id';

    protected $fillable = [
        'customer_id',
        'address_id',
        'address_detail',
    ];
    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_address_id', 'customer_address_id');
    }
}
