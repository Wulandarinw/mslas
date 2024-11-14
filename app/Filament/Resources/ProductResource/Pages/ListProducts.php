<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Excel as EksporTo;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('unduhPdf')
                ->label('Export')
                ->action('exportXLSX')
                ->color('success'),
        ];
    }

    public function exportXLSX()
    {
        return Excel::download(new ProductsExport, 'Product.xlsx', EksporTo::XLSX);
    }
}
