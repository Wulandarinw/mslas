<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueChart extends ChartWidget
{
    protected int | string | array $columnSpan = 2;
    protected static ?string $heading = 'Monthly Revenue Overview';
    protected static ?int $sort = 4;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $monthlyRevenue = Order::select(
            DB::raw('MONTH(order_date) as month'),
            DB::raw('SUM(total_amount) as total_revenue')
        )
            ->whereYear('order_date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total_revenue', 'month')
            ->toArray();

        $data = array_fill(1, 12, 0); // Initialize all months with zero

        foreach ($monthlyRevenue as $month => $revenue) {
            $data[$month] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => array_values($data),
                    'fill' => 'start',
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.2)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) {
                        return "Rp " + new Intl.NumberFormat().format(value);
                    }',
                        'font' => [
                            'size' => 12,
                            'family' => 'Roboto, sans-serif',
                        ],
                        'color' => '#374151',
                    ],
                    'grid' => [
                        'color' => 'rgba(209, 213, 219, 0.5)',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Revenue (Rp)',
                        'color' => '#374151',
                        'font' => [
                            'size' => 14,
                            'family' => 'Roboto, sans-serif',
                            'weight' => 'bold',
                        ],
                    ],
                ],
                'x' => [
                    'grid' => [
                        'color' => 'rgba(209, 213, 219, 0.5)',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Month',
                        'color' => '#374151',
                        'font' => [
                            'size' => 14,
                            'family' => 'Roboto, sans-serif',
                            'weight' => 'bold',
                        ],
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'color' => '#374151',
                        'font' => [
                            'size' => 14,
                        ],
                    ],
                ],
                'tooltip' => [
                    'titleFont' => [
                        'size' => 16,
                        'weight' => 'bold',
                    ],
                    'bodyFont' => [
                        'size' => 14,
                    ],
                    'callbacks' => [
                        'label' => 'function(context) {return "Revenue: Rp " + new Intl.NumberFormat().format(context.parsed.y);}',
                    ],
                ],
            ],
            'animation' => [
                'duration' => 1000,
                'easing' => 'easeOutQuart',
            ],
        ];
    }
}
