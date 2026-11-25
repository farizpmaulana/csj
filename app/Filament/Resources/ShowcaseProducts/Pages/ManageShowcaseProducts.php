<?php

namespace App\Filament\Resources\ShowcaseProducts\Pages;

use App\Filament\Resources\ShowcaseProducts\ShowcaseProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageShowcaseProducts extends ManageRecords
{
    protected static string $resource = ShowcaseProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add product to showcase')
                ->modalHeading('Add product to showcase')
                ->slideOver()
                ->modalWidth(Width::TwoExtraLarge)
                ->createAnother(false),
        ];
    }
}
