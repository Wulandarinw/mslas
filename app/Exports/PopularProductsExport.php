<?php

namespace App\Exports;

use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PopularProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return OrderItem::query()
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
            ->get()
            ->map(function ($item) {
                return [
                    'product_name' => $item->variation->product->name,
                    'color' => $item->variation->color,
                    'seri' => $item->variation->seri_code,
                    'total_sold' => $item->total_ordered,
                    'order_count' => $item->order_count,
                    'last_ordered' => $item->last_ordered,
                    'current_stock' => $item->variation->stock,
                    'total_' => $item->total_amount,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Color',
            'Seri',
            'Total Sold',
            'Order Count',
            'Last Ordered',
            'Current Stock',
            'Total Revenue',
        ];
    }
}