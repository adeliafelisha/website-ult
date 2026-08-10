<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $navigationGroup = 'Konten Layanan';

    protected static ?string $modelLabel = 'Layanan';

    protected static ?string $pluralModelLabel = 'Layanan';

    public static function form(Form $f): Form
    {
        return $f->schema([Forms\Components\Section::make('Identitas')->schema([Forms\Components\Select::make('service_category_id')->relationship('category', 'name')->required()->searchable()->preload(), Forms\Components\TextInput::make('title')->label('Nama layanan')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))), Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true), Forms\Components\Textarea::make('summary')->label('Ringkasan')->required()->columnSpanFull(), Forms\Components\TextInput::make('audience')->label('Sasaran pengguna'), Forms\Components\Select::make('delivery_type')->label('Jenis layanan')->options(['online' => 'Daring', 'offline' => 'Luring', 'hybrid' => 'Hybrid'])->required()])->columns(2), Forms\Components\Section::make('Detail layanan')->schema([Forms\Components\Textarea::make('requirements')->label('Persyaratan'), Forms\Components\Textarea::make('documents')->label('Dokumen yang diperlukan'), Forms\Components\RichEditor::make('procedure')->label('Prosedur')->columnSpanFull(), Forms\Components\TextInput::make('location')->label('Lokasi'), Forms\Components\TextInput::make('service_hours')->label('Waktu layanan'), Forms\Components\TextInput::make('process_time')->label('Estimasi proses'), Forms\Components\TextInput::make('fee')->label('Biaya'), Forms\Components\TextInput::make('responsible_unit')->label('Unit penanggung jawab'), Forms\Components\TextInput::make('content_owner')->label('Pemilik konten')])->columns(2), Forms\Components\Section::make('Tindakan & publikasi')->schema([Forms\Components\TextInput::make('cta_label')->label('Label tombol')->required(), Forms\Components\TextInput::make('cta_url')->label('URL tujuan')->url(), Forms\Components\FileUpload::make('featured_image')->image()->directory('services'), Forms\Components\TagsInput::make('keywords')->separator(','), Forms\Components\Textarea::make('seo_description'), Forms\Components\Toggle::make('is_featured')->label('Unggulan'), Forms\Components\Toggle::make('is_published')->label('Terbit'), Forms\Components\DateTimePicker::make('published_at')->label('Waktu terbit')])->columns(2)]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([Tables\Columns\TextColumn::make('title')->searchable()->sortable(), Tables\Columns\TextColumn::make('category.name')->label('Kategori'), Tables\Columns\TextColumn::make('delivery_type')->badge(), Tables\Columns\IconColumn::make('is_published')->label('Terbit')->boolean(), Tables\Columns\TextColumn::make('updated_at')->dateTime('d M Y')->sortable()])->filters([Tables\Filters\TernaryFilter::make('is_published')->label('Status terbit')])->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageServices::route('/')];
    }
}
