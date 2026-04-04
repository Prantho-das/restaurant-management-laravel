<?php

namespace App\Filament\Resources\FoodPreparations\Pages;

use App\Filament\Resources\FoodPreparations\FoodPreparationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFoodPreparations extends ListRecords
{
    protected static string $resource = FoodPreparationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
