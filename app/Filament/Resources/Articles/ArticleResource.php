<?php

namespace App\Filament\Resources\Articles;

use App\Enums\ArticleCategory;
use App\Filament\Resources\Articles\Pages\ManageArticles;
use App\Models\Article;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $navigationLabel = 'Articles';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Article')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in the address, for example /articles/panduan-k3.'),
                        Select::make('category')
                            ->options(collect(ArticleCategory::cases())->mapWithKeys(
                                fn (ArticleCategory $category) => [$category->value => $category->label()]
                            )->all())
                            ->required()
                            ->native(false),
                        DateTimePicker::make('published_at')
                            ->label('Published at')
                            ->seconds(false),
                        TextInput::make('reading_time')
                            ->placeholder('5 min read')
                            ->maxLength(255),
                        Textarea::make('lead')
                            ->rows(2)
                            ->label('Standfirst')
                            ->columnSpanFull(),
                        Textarea::make('excerpt')
                            ->rows(2)
                            ->helperText('Shown on the article cards.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Body')
                    ->schema([
                        RichEditor::make('body')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('cta_text')
                            ->label('Closing call to action text')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                Section::make('Images')
                    ->schema([
                        FileUpload::make('card_image')
                            ->label('Card image')
                            ->image()
                            ->disk('public')
                            ->directory('images/articles')
                            ->visibility('public'),
                        TextInput::make('card_alt')
                            ->label('Card alt text')
                            ->maxLength(255),
                        FileUpload::make('hero_image')
                            ->label('Hero image')
                            ->image()
                            ->disk('public')
                            ->directory('images/articles')
                            ->visibility('public'),
                        TextInput::make('hero_alt')
                            ->label('Hero alt text')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('SEO and publishing')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('Meta title')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Toggle::make('featured')
                            ->label('Featured'),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make('card_image')
                    ->label('Image')
                    ->disk('public')
                    ->height(40),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (ArticleCategory $state): string => $state->label()),
                TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
                IconColumn::make('featured')
                    ->label('Featured')
                    ->boolean(),
                IconColumn::make('is_published')
                    ->label('Live')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(collect(ArticleCategory::cases())->mapWithKeys(
                        fn (ArticleCategory $category) => [$category->value => $category->label()]
                    )->all()),
                TernaryFilter::make('is_published')->label('Live'),
            ])
            ->defaultSort('published_at', 'desc')
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
            'index' => ManageArticles::route('/'),
        ];
    }
}
