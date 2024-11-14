<?php

namespace App\Filament\Pages;

use App\Exports\PopularProductsExport;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Filament\Resources\OrderResource\Widgets\PopularProductsChart;
use App\Filament\Resources\OrderResource\Widgets\PopularProductsTable;
use App\Filament\Resources\OrderResource\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Pages\Page;
use Filament\Support\Facades\FilamentAsset;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as EksporTo;
use Filament\Actions;

class Report extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.reports';

    public function getTitle(): string
    {
        return 'Reports Dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class,
            RevenueChart::class,
            PopularProductsChart::class,
            PopularProductsTable::class,
            // Widgets\SalesChart::class,
            
        ];
    }

    public function getHeaderWidgetsColumns(): array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'm' => 3,
            'xl' => 4,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('unduhPdf')
                ->label('Export')
                ->action('exportXLSX')
                ->color('success'),
        ];
    }

    public function exportXLSX()
    {
        return Excel::download(new PopularProductsExport, 'Report-Revenue-Product.xlsx', EksporTo::XLSX);
    }

    
}