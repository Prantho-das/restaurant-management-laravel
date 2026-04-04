<?php

namespace App\Filament\Resources\FoodPreparations\Pages;

use App\Filament\Resources\FoodPreparations\FoodPreparationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFoodPreparation extends EditRecord
{
    protected static string $resource = FoodPreparationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
