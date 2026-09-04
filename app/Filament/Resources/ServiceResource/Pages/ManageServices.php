<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Support\CsvContentImporter;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageServices extends ManageRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importCsv')->label('Impor CSV')->icon('heroicon-o-arrow-up-tray')->form([
                FileUpload::make('file')->label('File CSV')->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])->storeFiles(false)->required()->helperText('Kolom wajib: category_slug, title, summary. Tombol kontak memakai contact_1_label, contact_1_channel, dan contact_1_url; tersedia sampai tombol ke-3.'),
            ])->action(function (array $data): void {
                $count = CsvContentImporter::services($data['file']);
                Notification::make()->title("{$count} layanan berhasil diimpor")->success()->send();
            }),
            Actions\CreateAction::make(),
        ];
    }
}
