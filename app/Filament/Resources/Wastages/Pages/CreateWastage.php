<?php

namespace App\Filament\Resources\Wastages\Pages;

use App\Filament\Resources\Wastages\WastageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWastage extends CreateRecord
{
    protected static string $resource = WastageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
