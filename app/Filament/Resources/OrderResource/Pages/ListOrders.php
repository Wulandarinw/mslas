<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Exports\OrdersExport;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as EksporTo;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class,
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
        return Excel::download(new OrdersExport, 'Order.xlsx', EksporTo::XLSX);
    }

    public function getTabs(): array{
        return [
         null => Tab::make('All'),
         'new' => Tab::make()->query(fn ($query) => $query->where('shipment_status', null)),
         'processing' => Tab::make()->query(fn ($query) => $query->where('shipment_status', 'processing')),
         'shipping' => Tab::make()->query(fn ($query) => $query->where('shipment_status', 'shipping')),
         'arrived' => Tab::make()->query(fn ($query) => $query->where('shipment_status', 'arrived')),
         'cancelled' => Tab::make()->query(fn ($query) => $query->where('shipment_status', 'cancelled')),
        ];
      }
}
