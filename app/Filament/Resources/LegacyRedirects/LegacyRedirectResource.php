<?php

namespace App\Filament\Resources\LegacyRedirects;

use App\Filament\Resources\LegacyRedirects\Pages\ManageLegacyRedirects;
use App\Models\LegacyRedirect;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;
use UnitEnum;

class LegacyRedirectResource extends Resource
{
    protected static ?string $model = LegacyRedirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Legacy redirects';

    protected static ?string $modelLabel = 'legacy redirect';

    protected static ?string $recordTitleAttribute = 'from_path';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Redirect')
                    ->schema([
                        TextInput::make('from_path')
                            ->label('Old address')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Relative to the site root, for example products/bosch.html or index.html.'),
                        Select::make('route_name')
                            ->label('Redirects to page')
                            ->options(fn () => collect(Route::getRoutes())
                                ->filter(fn ($route) => $route->getName() !== null && $route->getName() !== 'legacy')
                                ->mapWithKeys(fn ($route) => [$route->getName() => $route->getName()])
                                ->sort()
                                ->all())
                            ->required()
                            ->searchable()
                            ->native(false),
                        KeyValue::make('parameters')
                            ->label('Route parameters')
                            ->keyLabel('Parameter')
                            ->valueLabel('Value')
                            ->helperText('For example brand = bosch, or article = panduan-k3.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('from_path')
            ->columns([
                TextColumn::make('from_path')
                    ->label('Old address')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('route_name')
                    ->label('Redirects to')
                    ->badge(),
                TextColumn::make('destination')
                    ->label('Destination')
                    ->state(fn (LegacyRedirect $record): string => $record->destinationUrl())
                    ->limit(50)
                    ->tooltip(fn (LegacyRedirect $record): string => $record->destinationUrl()),
            ])
            ->defaultSort('from_path')
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
            'index' => ManageLegacyRedirects::route('/'),
        ];
    }
}
