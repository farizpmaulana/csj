<?php

namespace App\Filament\Resources\LegacyRedirects\Pages;

use App\Filament\Resources\LegacyRedirects\LegacyRedirectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLegacyRedirects extends ManageRecords
{
    protected static string $resource = LegacyRedirectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
