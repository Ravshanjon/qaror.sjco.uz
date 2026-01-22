<?php

namespace App\Filament\Admin\Resources\QarorlarResource\Pages;

use App\Filament\Admin\Resources\QarorlarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQarorlar extends ListRecords
{
    protected static string $resource = QarorlarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
