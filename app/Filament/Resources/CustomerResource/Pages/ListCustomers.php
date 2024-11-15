<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Exports\CustomerExport;
use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as EksporTo;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportExcel')
                ->label('Export to Excel')
                ->action('exportToExcel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }

    public function exportToExcel()
    {
        $filename = 'customers_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new CustomerExport, $filename, EksporTo::XLSX);
    }
}
