<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | string | array  $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order_code')
                    ->label('Order Code')
                    ->searchable(),

                TextColumn::make('customerAddress.customer.FName')
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->money('IDR'),

                TextColumn::make('payment_name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payment_status')
                    ->sortable()
                    ->badge()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime()
            ])
            ->actions([
                ViewAction::make('view_products')
                    ->modalHeading('Daftar Produk')
                    ->modalContent(fn(Order $record) => view('order-products', ['order' => $record]))
                    ->modalWidth('5xl'),
            ]);
    }
}
