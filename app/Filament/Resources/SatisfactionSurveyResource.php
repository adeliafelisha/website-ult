<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SatisfactionSurveyResource\Pages;
use App\Models\SatisfactionSurvey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class SatisfactionSurveyResource extends Resource
{
    protected static ?string $model = SatisfactionSurvey::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = 'Informasi';

    protected static ?string $modelLabel = 'Survei Kepuasan';

    protected static ?string $pluralModelLabel = 'Survei Kepuasan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Periode dan skor')->description('Isi maksimal empat skor untuk setiap tahun. Skor yang belum tersedia boleh dikosongkan.')->schema([
                Forms\Components\TextInput::make('year')->label('Tahun')->numeric()->minValue(2015)->maxValue(2100)->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('quarter_1_score')->label('Triwulan 1')->numeric()->minValue(0)->maxValue(100),
                Forms\Components\TextInput::make('quarter_2_score')->label('Triwulan 2')->numeric()->minValue(0)->maxValue(100),
                Forms\Components\TextInput::make('quarter_3_score')->label('Triwulan 3')->numeric()->minValue(0)->maxValue(100),
                Forms\Components\TextInput::make('quarter_4_score')->label('Triwulan 4')->numeric()->minValue(0)->maxValue(100),
            ])->columns(5),
            Forms\Components\Section::make('Tautan')->schema([
                Forms\Components\TextInput::make('source_url')->label('URL data asli')->url()->helperText('Menjadi tombol “Lihat data asli” di halaman Profil.'),
                Forms\Components\TextInput::make('questionnaire_url')->label('URL kuesioner')->url()->helperText('Menjadi tombol kuesioner survei kepuasan masyarakat.'),
                Forms\Components\Toggle::make('is_published')->label('Terbitkan di website')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('year')->label('Tahun')->sortable(),
            Tables\Columns\TextColumn::make('quarter_1_score')->label('TW 1')->placeholder('—'),
            Tables\Columns\TextColumn::make('quarter_2_score')->label('TW 2')->placeholder('—'),
            Tables\Columns\TextColumn::make('quarter_3_score')->label('TW 3')->placeholder('—'),
            Tables\Columns\TextColumn::make('quarter_4_score')->label('TW 4')->placeholder('—'),
            Tables\Columns\IconColumn::make('source_url')->label('Data')->boolean(),
            Tables\Columns\IconColumn::make('questionnaire_url')->label('Kuesioner')->boolean(),
            Tables\Columns\IconColumn::make('is_published')->label('Terbit')->boolean(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])->bulkActions([
            Tables\Actions\BulkAction::make('publish')->label('Terbitkan')->icon('heroicon-o-eye')->action(fn (Collection $records) => $records->each->update(['is_published' => true]))->deselectRecordsAfterCompletion(),
            Tables\Actions\BulkAction::make('unpublish')->label('Batalkan terbit')->icon('heroicon-o-eye-slash')->color('warning')->action(fn (Collection $records) => $records->each->update(['is_published' => false]))->deselectRecordsAfterCompletion(),
            Tables\Actions\DeleteBulkAction::make(),
        ])->defaultSort('year', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageSatisfactionSurveys::route('/')];
    }
}
