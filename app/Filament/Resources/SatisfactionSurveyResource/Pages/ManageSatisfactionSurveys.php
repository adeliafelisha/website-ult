<?php

namespace App\Filament\Resources\SatisfactionSurveyResource\Pages;

use App\Filament\Resources\SatisfactionSurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSatisfactionSurveys extends ManageRecords
{
    protected static string $resource = SatisfactionSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
