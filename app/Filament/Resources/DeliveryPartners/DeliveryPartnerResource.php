<?php

namespace App\Filament\Resources\DeliveryPartners;

use App\Filament\Resources\DeliveryPartners\Pages\CreateDeliveryPartner;
use App\Filament\Resources\DeliveryPartners\Pages\EditDeliveryPartner;
use App\Filament\Resources\DeliveryPartners\Pages\ListDeliveryPartners;
use App\Filament\Resources\DeliveryPartners\Schemas\DeliveryPartnerForm;
use App\Filament\Resources\DeliveryPartners\Tables\DeliveryPartnersTable;
use App\Models\DeliveryPartner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeliveryPartnerResource extends Resource
{
    protected static ?string $model = DeliveryPartner::class;

    protected static ?string $label = 'Delivery Partner';
    protected static ?string $pluralLabel = 'Delivery Partners';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    public static function form(Schema $schema): Schema
    {
        return DeliveryPartnerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryPartnersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveryPartners::route('/'),
            'create' => CreateDeliveryPartner::route('/create'),
            'edit' => EditDeliveryPartner::route('/{record}/edit'),
        ];
    }
}
