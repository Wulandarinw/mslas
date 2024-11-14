<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'user_id',
        'FName',
        'LName',
        'gender',
        'date_of_birth',
        'email',
        'phone',
        'profile_icon'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sellers()
    {
        return $this->hasOne(Seller::class, 'customer_id', 'customer_id');
    }

    public function customerAddress()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id', 'customer_id');
    }

    public function carts()
    {
        return $this->hasOne(Cart::class, 'customer_id', 'customer_id');
    } 
}
