<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Pos extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'POS System';

    protected static ?string $title = '';

    protected string $view = 'filament.pages.pos';

    public function getHeading(): string
    {
        return '';
    }
}
