<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use App\Support\CsvContentImporter;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageFaqs extends ManageRecords
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importCsv')->label('Impor CSV')->icon('heroicon-o-arrow-up-tray')->form([
                FileUpload::make('file')->label('File CSV')->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])->storeFiles(false)->required()->helperText('Kolom wajib: question, answer, category. Kolom opsional: question_en, answer_en, external_url, external_label, sort_order, is_featured, is_published.'),
            ])->action(function (array $data): void {
                $count = CsvContentImporter::faqs($data['file']);
                Notification::make()->title("{$count} FAQ berhasil diimpor")->success()->send();
            }),
            Actions\CreateAction::make(),
        ];
    }
}
