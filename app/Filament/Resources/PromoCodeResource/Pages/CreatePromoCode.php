<?php

namespace App\Filament\Resources\PromoCodeResource\Pages;

use App\Filament\Resources\PromoCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePromoCode extends CreateRecord
{
    protected static string $resource = PromoCodeResource::class;

    protected function afterCreate(): void
    {
        $ids = $this->form->getState()['products'] ?? [];
        $this->record->products()->sync(is_array($ids) ? $ids : []);
    }
}
