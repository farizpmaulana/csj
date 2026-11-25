<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Concerns\HasOrderActions;
use App\Filament\Resources\Clients\Pages\ManageClients;
use App\Models\Client;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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

class ClientResource extends Resource
{
    use HasOrderActions;

    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Homepage';

    protected static ?string $navigationLabel = 'Client list';

    protected static ?string $modelLabel = 'client';

    protected static ?string $pluralModelLabel = 'clients';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Client')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('alt')
                            ->label('Alt text')
                            ->maxLength(255)
                            ->helperText('Read by screen readers, so describe the logo.'),
                        FileUpload::make('path')
                            ->label('Logo')
                            ->image()
                            ->imagePreviewHeight('200')
                            ->disk('public')
                            ->directory('images/clients')
                            ->visibility('public')
                            ->required()
                            ->helperText('A single file is expected, ideally a transparent PNG.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Responsive variants')
                    ->description('Optional. The browser picks the best file for the screen it is on. The main logo is used when this list is empty.')
                    ->schema([
                        Repeater::make('variants')
                            ->schema([
                                FileUpload::make('path')
                                    ->label('File')
                                    ->image()
                                    ->disk('public')
                                    ->directory('images/clients')
                                    ->visibility('public')
                                    ->required(),
                                TextInput::make('width')
                                    ->label('Width in pixels')
                                    ->numeric()
                                    ->required()
                                    ->default(280),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add variant')
                            ->reorderable(),
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
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('path')
                    ->label('Logo')
                    ->disk('public')
                    ->height(40),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
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
                EditAction::make()->modalWidth(Width::FiveExtraLarge),
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
            'index' => ManageClients::route('/'),
        ];
    }
}
