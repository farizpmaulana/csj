<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\ManageProducts;
use App\Models\Product;
use App\Models\ProductCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        Select::make('brand_id')
                            ->label('Partner')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('product_category_id', null)),
                        Select::make('product_category_id')
                            ->label('Category')
                            ->options(fn (Get $get): array => ProductCategory::query()
                                ->where('brand_id', $get('brand_id'))
                                ->orderBy('position')
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn (ProductCategory $category) => [$category->id => $category->name ?: 'Uncategorised'])
                                ->all())
                            ->searchable()
                            ->native(false)
                            ->helperText('Products are grouped by category on the partner page. Add categories under Partners first.'),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in the address, for example /products/bosch/impact-wrench.'),
                        TextInput::make('subtitle')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Content')
                    ->schema([
                        Textarea::make('excerpt')
                            ->rows(3)
                            ->label('Card summary')
                            ->helperText('Shown on the partner list page.'),
                        RichEditor::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Card image')
                    ->schema([
                        FileUpload::make('card_image')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('images/products')
                            ->visibility('public'),
                        TextInput::make('card_alt')
                            ->label('Alt text')
                            ->maxLength(255),
                        TextInput::make('card_label')
                            ->label('Card label')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Gallery')
                    ->schema([
                        Repeater::make('images')
                            ->label('Slider images')
                            ->relationship('images')
                            ->orderColumn('position')
                            ->schema([
                                FileUpload::make('path')
                                    ->label('Image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('images/products')
                                    ->visibility('public')
                                    ->required(),
                                TextInput::make('alt')
                                    ->label('Alt text')
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add image')
                            ->reorderable()
                            ->itemLabel(fn (array $state): ?string => basename((string) ($state['path'] ?? '')) ?: null)
                            ->columnSpanFull()
                            ->helperText('These become the slides of the product image slider, in this order.'),
                    ]),
                Section::make('Specifications')
                    ->schema([
                        Repeater::make('sections')
                            ->label('Specification sections')
                            ->relationship('sections')
                            ->orderColumn('position')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('type')
                                    ->options([
                                        'list' => 'Bulleted list',
                                        'ordered' => 'Numbered list',
                                        'table' => 'Table',
                                    ])
                                    ->default('list')
                                    ->required(),
                                Repeater::make('rows')
                                    ->label('Rows')
                                    ->relationship('rows')
                                    ->orderColumn('position')
                                    ->schema([
                                        TextInput::make('label')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('value')
                                            ->maxLength(255),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add row')
                                    ->reorderable()
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                    ->collapsed(),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->defaultItems(0)
                            ->addActionLabel('Add section')
                            ->reorderable()
                            ->collapsed()
                            ->columnSpanFull(),
                    ]),
                Section::make('Enquiry and SEO')
                    ->schema([
                        TextInput::make('whatsapp_message')
                            ->label('WhatsApp message')
                            ->maxLength(255)
                            ->helperText('Leave empty to use the generic message.')
                            ->columnSpanFull(),
                        TextInput::make('seo_title')
                            ->label('Meta title')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Visibility')
                    ->schema([
                        TextInput::make('position')
                            ->label('Order')
                            ->numeric()
                            ->required()
                            ->default(0),
                        Toggle::make('is_visible')
                            ->label('Visible on the website')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('card_image')
                    ->label('Image')
                    ->disk('public')
                    ->height(40),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->label('Partner')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->placeholder('Uncategorised'),
                TextColumn::make('images_count')
                    ->label('Images')
                    ->counts('images'),
                TextColumn::make('whatsapp_link')
                    ->label('Enquiry link')
                    ->placeholder('No WhatsApp number set')
                    ->limit(48)
                    ->tooltip('Click to copy'),
                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('brand')->relationship('brand', 'name'),
                TernaryFilter::make('is_visible')->label('Visible'),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProducts::route('/'),
        ];
    }
}
