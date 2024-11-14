<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\OrderItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PopularProductsTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getTableRecordKey(Model $record): string
    {
        return $record->variation_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->select([
                        'order_items.variation_id',
                        DB::raw('SUM(order_items.qty) as total_ordered'),
                        DB::raw('COUNT(DISTINCT orders.order_code) as order_count'),
                        DB::raw('MAX(orders.order_date) as last_ordered'),
                        DB::raw('SUM(orders.total_amount) as total_amount')
                    ])
                    ->join('orders', 'order_items.order_code', '=', 'orders.order_code')
                    ->join('product_variations', 'order_items.variation_id', '=', 'product_variations.variation_id')
                    ->join('products', 'product_variations.product_id', '=', 'products.product_id')
                    ->where('products.shop_id', function ($query) {
                        $query->select('shops.shop_id')
                            ->from('shops')
                            ->join('sellers', 'shops.seller_ktp_nik', '=', 'sellers.ktp_nik')
                            ->join('customers', 'sellers.customer_id', '=', 'customers.customer_id')
                            ->join('users', 'customers.user_id', '=', 'users.id')
                            ->where('users.id', auth()->id())
                            ->limit(1);
                    })
                    ->with([
                        'variation.product',
                    ])
                    ->groupBy([
                        'order_items.variation_id',
                        'product_variations.variation_id',
                        'product_variations.product_id',
                        'products.product_id',
                        'products.name'
                    ])
                    ->orderByDesc('total_ordered')
            )
            ->columns([
                ImageColumn::make('variation.images')
                    ->label('Product Image')
                    ->circular(),

                TextColumn::make('variation.product.name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('variation.color')
                    ->label('Color')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('variation.seri_code')
                    ->label('Seri')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_ordered')
                    ->label('Total Sold')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('order_count')
                    ->label('Order Count')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('last_ordered')
                    ->label('Last Order')
                    ->date()
                    ->sortable(),

                TextColumn::make('variation.stock')
                    ->label('Current Stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),
            ])
            ->defaultSort('total_ordered', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->heading('Popular Products');
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        // Cek apakah user adalah seller (userType = 'S') dan memiliki toko
        if (!$user || $user->userType !== 'S') {
            return false;
        }

        // Cek apakah user memiliki data customer
        $customer = $user->customers;
        if (!$customer) {
            return false;
        }

        // Cek apakah customer memiliki data seller
        $seller = $customer->sellers;
        if (!$seller) {
            return false;
        }

        // Cek apakah seller memiliki toko
        return $seller->shops()->exists();
    }
}
