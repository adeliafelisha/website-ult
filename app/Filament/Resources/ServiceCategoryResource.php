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

    public static function form(Form $form): Form
    {
        return $form->schema([Forms\Components\TextInput::make('name')->label('Nama')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))), Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true), Forms\Components\Textarea::make('description')->columnSpanFull(), Forms\Components\TextInput::make('icon')->default('sparkles'), Forms\Components\TextInput::make('sort_order')->numeric()->default(0), Forms\Components\Toggle::make('is_featured')->label('Tampilkan di beranda')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('name')->searchable()->sortable(), Tables\Columns\TextColumn::make('services_count')->counts('services')->label('Layanan'), Tables\Columns\IconColumn::make('is_featured')->boolean()])->defaultSort('sort_order')->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageServiceCategories::route('/')];
    }
}
