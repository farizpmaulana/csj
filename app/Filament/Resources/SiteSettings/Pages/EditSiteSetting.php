<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\EditRecord;

class EditSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    protected static ?string $title = 'Site settings';

    /**
     * The settings are a single row, so the page mounts it directly and ignores
     * any identifier from the address.
     */
    public function mount(int|string|null $record = null): void
    {
        $this->record = SiteSetting::current();

        $this->authorizeAccess();

        $this->fillForm();
    }

    /**
     * There is only ever one settings row, so it can neither be created nor
     * deleted from here.
     *
     * @return array<mixed>
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }
}
