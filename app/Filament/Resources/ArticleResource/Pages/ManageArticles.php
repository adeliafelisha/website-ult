<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Support\CsvContentImporter;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageArticles extends ManageRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importCsv')->label('Impor CSV')->icon('heroicon-o-arrow-up-tray')->form([
                FileUpload::make('file')->label('File CSV')->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])->storeFiles(false)->required()->helperText('Kolom wajib: title, category, excerpt, content. Kolom opsional mengikuti field artikel, termasuk external_url dan is_published.'),
            ])->action(function (array $data): void {
                $count = CsvContentImporter::articles($data['file']);
                Notification::make()->title("{$count} artikel berhasil diimpor")->success()->send();
            }),
            Actions\CreateAction::make(),
        ];
    }
}
