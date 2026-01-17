<?php

namespace App\Filament\Admin\Resources\QarorlarResource\Pages;

use App\Filament\Admin\Resources\QarorlarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQarorlar extends EditRecord
{
    protected static string $resource = QarorlarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
