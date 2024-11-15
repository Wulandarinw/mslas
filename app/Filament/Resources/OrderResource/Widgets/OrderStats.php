<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected int | string | array $columnSpan = 4;

    protected function getStats(): array
    {
        $currentMonth = now()->startOfMonth();
        $nextMonth = now()->startOfMonth()->addMonth();
        
        // Array of Indonesian month names
        $indonesianMonths = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        
        $monthName = $indonesianMonths[now()->month];

        return [
            Stat::make("New Customers {$monthName}",
                Customer::query()
                    ->whereBetween('created_at', [$currentMonth, $nextMonth])
                    ->count()
            )
                ->description('Customers registered this month')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make("New Orders {$monthName}", 
                Order::query()
                    ->where('shipment_status', null)
                    ->whereBetween('created_at', [$currentMonth, $nextMonth])
                    ->count()
            )
                ->description('Waiting to be processed')
                ->descriptionIcon('heroicon-m-calendar'),

            Stat::make("Order Processing {$monthName}", 
                Order::query()
                    ->where('shipment_status', 'processing')
                    ->whereBetween('created_at', [$currentMonth, $nextMonth])
                    ->count()
            )
                ->description('Orders being processed')
                ->descriptionIcon('heroicon-m-arrow-path'),

            Stat::make("Total Omset {$monthName}", 
                'Rp ' . number_format(
                    Order::query()
                        ->whereBetween('created_at', [$currentMonth, $nextMonth])
                        ->sum('total_amount'), 
                    0, 
                    ',', 
                    '.'
                )
            )
                ->description($monthName . ' ' . now()->year)
                ->descriptionIcon('heroicon-m-banknotes')
        ];
    }
}