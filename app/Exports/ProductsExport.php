<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with('variations', 'shop', 'category')->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Shop',
            'Category',
            'Description',
            'Dimension',
            'Weight',
            'Status',
            'Variation - Color',
            'Variation - Material',
            'Variation - Price',
            'Variation - Stock',
        ];
    }

    public function map($product): array
    {
        $mapped = [];
        foreach ($product->variations as $variation) {
            $mapped[] = [
                $product->name,
                optional($product->shop)->name,
                optional($product->category)->name,
                $product->desc,
                $product->dimension,
                $product->weight,
                $product->status,
                $variation->color,
                $variation->material,
                $variation->price,
                $variation->stock,
            ];
        }
        return $mapped;
    }
}