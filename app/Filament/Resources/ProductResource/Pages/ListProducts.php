<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Добавить товар'),
        ];
    }

    public function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();
        if ($query && \Illuminate\Support\Facades\Schema::hasTable('product_types')) {
            $query->with('typeRelation');
        }
        return $query;
    }
}
