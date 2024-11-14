<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Shop;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'shop_id',
        'category_code',
        'desc',
        'dimension',
        'weight',
        'status',
    ];

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id', 'product_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_code', 'category_code');
    }
}
