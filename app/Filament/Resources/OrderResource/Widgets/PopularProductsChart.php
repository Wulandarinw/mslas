<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PopularProductsChart extends ChartWidget
{
    protected int | string | array $columnSpan = 2;
    protected static ?string $heading = 'Most Popular Products';
    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $popularProducts = OrderItem::select(
            'products.name as product_name',
            DB::raw('COUNT(*) as order_count')
        )
            ->join('product_variations', 'order_items.variation_id', '=', 'product_variations.variation_id')
            ->join('products', 'product_variations.product_id', '=', 'products.product_id')
            ->groupBy('products.product_id', 'products.name')
            ->orderByDesc('order_count')
            ->limit(12)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $popularProducts->pluck('order_count')->toArray(),
                    'fill' => 'start',
                ],
            ],
            'labels' => $popularProducts->pluck('product_name')->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'maxRotation' => 0,
                        'minRotation' => 0,
                    ],
                ],
                'y' => [
                    'ticks' => [
                        'stepSize' => 100,
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
