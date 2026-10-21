<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Site settings';

    protected static ?string $modelLabel = 'site settings';

    protected static ?string $pluralModelLabel = 'site settings';

    protected static ?string $recordTitleAttribute = 'company_name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company')
                    ->schema([
                        TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('contact_email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('contact_phone')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp number')
                            ->helperText('Country code and number only, for example 6281140000223.')
                            ->maxLength(32),
                    ])
                    ->columns(2),
                Section::make('Addresses')
                    ->schema([
                        TextInput::make('address_primary')
                            ->label('Primary address')
                            ->maxLength(255),
                        TextInput::make('address_secondary')
                            ->label('Secondary address')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Social profiles')
                    ->schema([
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('linkedin_url')
                            ->label('LinkedIn')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Branding')
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('images/branding')
                            ->visibility('public'),
                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('images/branding')
                            ->visibility('public'),
                        FileUpload::make('default_social_image')
                            ->label('Default social share image')
                            ->image()
                            ->disk('public')
                            ->directory('images/branding')
                            ->visibility('public')
                            ->helperText('Shown when a page has no image of its own. 1200 by 630 works well.'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('company_name')
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->height(40),
                TextColumn::make('company_name')
                    ->searchable(),
                TextColumn::make('contact_email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('contact_phone')
                    ->label('Phone'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => EditSiteSetting::route('/'),
        ];
    }
}
