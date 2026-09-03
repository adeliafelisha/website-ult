<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceCategoryResource\Pages;
use App\Models\ServiceCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceCategoryResource extends Resource
{
    protected static ?string $model = ServiceCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Konten Layanan';

    protected static ?string $modelLabel = 'Kategori Layanan';

    protected static ?string $pluralModelLabel = 'Kategori Layanan';

    public static function form(Form $f): Form
    {
        return $f->schema([Forms\Components\TextInput::make('name')->label('Nama Indonesia')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))), Forms\Components\TextInput::make('name_en')->label('Name (English)'), Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true), Forms\Components\Textarea::make('description')->label('Deskripsi Indonesia'), Forms\Components\Textarea::make('description_en')->label('Description (English)'), Forms\Components\TextInput::make('sort_order')->numeric()->default(0), Forms\Components\Toggle::make('is_featured')->label('Tampilkan di beranda')])->columns(2);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([Tables\Columns\TextColumn::make('name')->searchable(), Tables\Columns\TextColumn::make('services_count')->counts('services')->label('Layanan'), Tables\Columns\IconColumn::make('is_featured')->boolean()])->defaultSort('sort_order')->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()->disabled(fn ($record) => $record->services()->exists())])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageServiceCategories::route('/')];
    }
}
