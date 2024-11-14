<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New orders', Order::query()->where('shipment_status', null)->count()),
            Stat::make('Order Processing', Order::query()->where('shipment_status', 'processing')->count()),
            Stat::make('Average Price', 'Rp ' . number_format(Order::query()->avg('total_amount'), 0, ',', '.'))
        ];
    }
}
