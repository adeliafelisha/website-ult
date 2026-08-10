<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuickLinkResource\Pages;
use App\Models\QuickLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuickLinkResource extends Resource
{
    protected static ?string $model = QuickLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Pengaturan Publik';

    protected static ?string $modelLabel = 'Tautan Cepat';

    protected static ?string $pluralModelLabel = 'Tautan Cepat';

    public static function form(Form $f): Form
    {
        return $f->schema([Forms\Components\TextInput::make('name')->label('Nama')->required(), Forms\Components\TextInput::make('url')->label('URL')->url()->required(), Forms\Components\Textarea::make('description')->columnSpanFull(), Forms\Components\TextInput::make('sort_order')->numeric()->default(0), Forms\Components\Toggle::make('is_published')->label('Aktif')->default(true)])->columns(2);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([Tables\Columns\TextColumn::make('name')->searchable(), Tables\Columns\TextColumn::make('url')->limit(40), Tables\Columns\IconColumn::make('is_published')->boolean()])->defaultSort('sort_order')->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageQuickLinks::route('/')];
    }
}
