<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('order_code')
                ->label('Order Code')
                ->searchable()
                ->sortable(),
            TextColumn::make('customerAddress.customer.FName')
                ->label('Customer')
                ->sortable()
                ->searchable(),
            TextColumn::make('total_amount')
                ->label('Total Amount')
                ->money('IDR')
                ->sortable()
                ->searchable(),
            TextColumn::make('payment_name')
                ->label('Payment')
                ->searchable(),
            BadgeColumn::make('payment_status')
                ->label('Payment Status')
                ->sortable()
                ->searchable()
                ->colors([
                    'success' => 'Paid',
                    'warning' => 'Pending',
                    'danger' => 'Failed',
                ]),
            TextColumn::make('order_date')
                ->label('Order Date')
                ->date()
                ->sortable()
                ->searchable(),
            TextColumn::make('payment_date')
                ->label('Payment Date')
                ->date()
                ->sortable(),
            SelectColumn::make('shipment_status')
                ->label('Shipment Status')
                ->options([
                    null => 'New',
                    'processing' => 'Processing',
                    'shipping' => 'Shipping',
                    'arrived' => 'Arrived',
                    'cancelled' => 'Cancelled',
                ])
                ->searchable()
                ->sortable(),
            TextColumn::make('shipment_name')
                ->label('Shipment')
                ->searchable(),
            TextColumn::make('shopping_cost')
                ->label('Shopping Cost')
                ->money('IDR')
                ->sortable(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('payment_status')
                ->label('Payment Status')
                ->options([
                    'Paid' => 'Paid',
                    'Pending' => 'Pending',
                    'Failed' => 'Failed',
                ]),
            Tables\Filters\SelectFilter::make('shipment_status')
                ->label('Shipment Status')
                ->options([
                    null => 'New',
                    'processing' => 'Processing',
                    'shipping' => 'Shipping',
                    'arrived' => 'Arrived',
                    'cancelled' => 'Cancelled',
                ]),
            Tables\Filters\Filter::make('order_date')
                ->form([
                    Forms\Components\DatePicker::make('from')
                        ->label('From'),
                    Forms\Components\DatePicker::make('until')
                        ->label('Until'),
                ])
                ->query(fn (Builder $query, array $data) => $query
                    ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('order_date', '>=', $date))
                    ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('order_date', '<=', $date))),
            Tables\Filters\Filter::make('total_amount')
                ->label('Search by Price')
                ->form([
                    Forms\Components\TextInput::make('min_price')
                        ->label('Min Price')
                        ->numeric()
                        ->placeholder('0')
                        ->prefix('Rp'),
                    Forms\Components\TextInput::make('max_price')
                        ->label('Max Price')
                        ->numeric()
                        ->placeholder('0')
                        ->prefix('Rp'),
                ])
                ->query(fn (Builder $query, array $data) => $query
                    ->when($data['min_price'], fn (Builder $q, $price) => $q->where('total_amount', '>=', $price))
                    ->when($data['max_price'], fn (Builder $q, $price) => $q->where('total_amount', '<=', $price))),
        ])
        ->actions([
            ActionGroup::make([
                ViewAction::make('view_products')
                    ->modalHeading('Daftar Produk')
                    ->modalContent(fn(Order $record) => view('order-products', ['order' => $record]))
                    ->modalWidth('5xl'),
                DeleteAction::make(),
            ]),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
}

    

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'success' : 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
