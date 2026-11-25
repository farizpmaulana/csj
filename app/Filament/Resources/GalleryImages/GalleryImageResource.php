<?php

namespace App\Filament\Resources\GalleryImages;

use App\Filament\Concerns\HasOrderActions;
use App\Filament\Resources\GalleryImages\Pages\ManageGalleryImages;
use App\Models\GalleryImage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class GalleryImageResource extends Resource
{
    use HasOrderActions;

    protected static ?string $model = GalleryImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Homepage';

    protected static ?string $navigationLabel = 'Gallery images';

    protected static ?string $recordTitleAttribute = 'caption';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Image')
                    ->schema([
                        FileUpload::make('path')
                            ->label('Card image')
                            ->image()
                            ->imagePreviewHeight('200')
                            ->disk('public')
                            ->directory('images/gallery')
                            ->visibility('public')
                            ->required()
                            ->helperText('Shown in the grid. Landscape images look best.'),
                        FileUpload::make('full_path')
                            ->label('Full size image')
                            ->image()
                            ->imagePreviewHeight('200')
                            ->disk('public')
                            ->directory('images/gallery')
                            ->visibility('public')
                            ->helperText('Opened by the zoom button. Leave empty to zoom the card image.'),
                        TextInput::make('alt')
                            ->label('Alt text')
                            ->maxLength(255),
                        Textarea::make('caption')
                            ->rows(2)
                            ->maxLength(255),
                    ]),
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->columns([
                ImageColumn::make('path')
                    ->label('Image')
                    ->disk('public')
                    ->height(50),
                TextColumn::make('caption')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('—'),
                TextColumn::make('alt')
                    ->label('Alt text')
                    ->limit(40)
                    ->placeholder('—'),
                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),
                ...static::getOrderColumns(),
            ])
            ->filters([
                TernaryFilter::make('is_visible')->label('Visible'),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->recordActions([
                ...static::getOrderActions(),
                EditAction::make()->slideOver()->modalWidth(Width::TwoExtraLarge),
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
            'index' => ManageGalleryImages::route('/'),
        ];
    }
}
