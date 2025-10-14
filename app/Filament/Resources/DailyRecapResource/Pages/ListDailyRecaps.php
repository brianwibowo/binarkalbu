<?php

namespace App\Filament\Resources\DailyRecapResource\Pages;

use App\Filament\Resources\DailyRecapResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyRecaps extends ListRecords
{
    protected static string $resource = DailyRecapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
