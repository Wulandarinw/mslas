<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        // Add your form fields here
        return $form
            ->schema([
                // Your form schema components here
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()->where('shop_id', Auth::user()->customers->sellers->shops->shop_id)
            )
            ->columns([
                ImageColumn::make('variations.images')
                    ->label('Image')
                    ->circular()
                    ->getStateUsing(function ($record) {
                        return $record->variations->first()?->images[0] ?? null;
                    }),

                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('variations.price')
                    ->money('IDR')
                    ->label('Price')
                    ->getStateUsing(function ($record) {
                        return $record->variations->first()?->price;
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'success' => 'Publish',
                        'danger' => 'Draft',
                    ]),

                TextColumn::make('variations_count')
                    ->label('Jumlah Variasi')
                    ->counts('variations')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
            ])
            ->actions([
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->iconButton()
                    ->action(function (Product $record) {
                        $record->status = 'Publish';
                        $record->save();
                        Notification::make()
                            ->title('Product Published')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->hidden(fn(Product $record) => $record->status === 'Publish'),
                Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->iconButton()
                    ->action(function (Product $record) {
                        $record->status = 'Draft';
                        $record->save();
                        Notification::make()
                            ->title('Product Unpublished')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->hidden(fn(Product $record) => $record->status !== 'Publish'),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}