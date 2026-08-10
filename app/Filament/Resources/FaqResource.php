<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Informasi';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQ';

    public static function form(Form $f): Form
    {
        return $f->schema([Forms\Components\TextInput::make('question')->label('Pertanyaan')->required()->columnSpanFull(), Forms\Components\RichEditor::make('answer')->label('Jawaban')->required()->columnSpanFull(), Forms\Components\TextInput::make('category')->label('Kategori')->required(), Forms\Components\TextInput::make('audience')->label('Sasaran pengguna'), Forms\Components\TextInput::make('sort_order')->numeric()->default(0), Forms\Components\Toggle::make('is_featured')->label('Tampilkan di beranda'), Forms\Components\Toggle::make('is_published')->label('Terbit')->default(true)])->columns(2);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([Tables\Columns\TextColumn::make('question')->label('Pertanyaan')->searchable()->limit(70), Tables\Columns\TextColumn::make('category')->badge(), Tables\Columns\IconColumn::make('is_featured')->boolean(), Tables\Columns\IconColumn::make('is_published')->boolean()])->defaultSort('sort_order')->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageFaqs::route('/')];
    }
}
