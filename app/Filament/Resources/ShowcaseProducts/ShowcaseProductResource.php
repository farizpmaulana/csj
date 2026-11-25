<?php

namespace App\Filament\Resources\ShowcaseProducts;

use App\Filament\Concerns\HasOrderActions;
use App\Filament\Resources\ShowcaseProducts\Pages\ManageShowcaseProducts;
use App\Models\Product;
use App\Models\ShowcaseProduct;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ShowcaseProductResource extends Resource
{
    use HasOrderActions;

    protected static ?string $model = ShowcaseProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Homepage';

    protected static ?string $navigationLabel = 'Product showcase';

    protected static ?string $modelLabel = 'showcase product';

    protected static ?string $pluralModelLabel = 'showcase products';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Showcase product')
                    ->description('Choose an existing product to feature on the homepage showcase.')
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->options(function (?ShowcaseProduct $record): array {
                                $existingIds = ShowcaseProduct::query()
                                    ->when($record?->id, fn ($q, $id) => $q->where('id', '!=', $id))
                                    ->pluck('product_id')
                                    ->all();

                                return Product::query()
                                    ->visible()
                                    ->whereNotIn('id', $existingIds)
                                    ->with('brand')
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn (Product $p): array => [
                                        $p->id => "{$p->name} ({$p->brand?->name})",
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select a product to display on the homepage showcase.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('Feature products in the homepage showcase. Use the up and down arrows or drag rows to change the display order.')
            ->columns([
                ImageColumn::make('product.card_image')
                    ->label('Image')
                    ->disk('public')
                    ->height(40),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.brand.name')
                    ->label('Partner')
                    ->sortable(),
                IconColumn::make('product.is_visible')
                    ->label('Product visible')
                    ->boolean(),
                ...static::getOrderColumns(),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->recordActions([
                ...static::getOrderActions(),
                DeleteAction::make()
                    ->label('Remove')
                    ->modalHeading('Remove product from showcase')
                    ->modalDescription('Are you sure you want to remove this product from the homepage showcase? The product itself will not be deleted.')
                    ->modalSubmitActionLabel('Remove'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageShowcaseProducts::route('/'),
        ];
    }
}
