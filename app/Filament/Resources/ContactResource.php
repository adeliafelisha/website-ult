<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationGroup = 'Pengaturan Publik';

    protected static ?string $modelLabel = 'Kontak';

    protected static ?string $pluralModelLabel = 'Kontak';

    public static function form(Form $f): Form
    {
        return $f->schema([Forms\Components\TextInput::make('label')->label('Label')->required(), Forms\Components\Select::make('type')->label('Jenis')->options(['email' => 'Email', 'phone' => 'Telepon', 'whatsapp' => 'WhatsApp', 'helpdesk' => 'Helpdesk', 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'address' => 'Alamat', 'other' => 'Lainnya'])->required(), Forms\Components\TextInput::make('value')->label('Nilai')->required(), Forms\Components\TextInput::make('url')->label('URL/tautan')->helperText('Gunakan URL lengkap, termasuk https://'), Forms\Components\Textarea::make('description')->columnSpanFull(), Forms\Components\TextInput::make('sort_order')->numeric()->default(0), Forms\Components\Toggle::make('is_published')->label('Aktif')->default(true)])->columns(2);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([Tables\Columns\TextColumn::make('label')->searchable(), Tables\Columns\TextColumn::make('type')->badge(), Tables\Columns\TextColumn::make('value'), Tables\Columns\IconColumn::make('is_published')->boolean()])->defaultSort('sort_order')->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageContacts::route('/')];
    }
}
