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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas')->schema([Forms\Components\Select::make('service_category_id')->relationship('category', 'name')->required()->searchable()->preload(), Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true), Forms\Components\Select::make('delivery_type')->label('Jenis layanan')->options(['online' => 'Daring', 'offline' => 'Luring', 'hybrid' => 'Hybrid'])->required()])->columns(3),
            Forms\Components\Tabs::make('Konten')->tabs([
                Forms\Components\Tabs\Tab::make('Indonesia')->schema([Forms\Components\TextInput::make('title')->label('Nama layanan')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))), Forms\Components\Textarea::make('summary')->label('Ringkasan')->required(), Forms\Components\TextInput::make('audience')->label('Sasaran'), Forms\Components\Textarea::make('requirements')->label('Persyaratan'), Forms\Components\Textarea::make('documents')->label('Dokumen'), Forms\Components\RichEditor::make('procedure')->label('Prosedur')->columnSpanFull(), Forms\Components\TextInput::make('cta_label')->label('Label tombol')->required(), Forms\Components\TextInput::make('location')->label('Lokasi'), Forms\Components\TextInput::make('service_hours')->label('Waktu layanan'), Forms\Components\TextInput::make('process_time')->label('Estimasi proses'), Forms\Components\TextInput::make('fee')->label('Biaya'), Forms\Components\TextInput::make('responsible_unit')->label('Unit penanggung jawab'), Forms\Components\TextInput::make('content_owner')->label('Pemilik konten'), Forms\Components\Textarea::make('seo_description')->label('Deskripsi SEO')->columnSpanFull()])->columns(2),
                Forms\Components\Tabs\Tab::make('English')->schema([Forms\Components\TextInput::make('title_en')->label('Service name'), Forms\Components\Textarea::make('summary_en')->label('Summary'), Forms\Components\TextInput::make('audience_en')->label('Audience'), Forms\Components\Textarea::make('requirements_en')->label('Requirements'), Forms\Components\Textarea::make('documents_en')->label('Documents'), Forms\Components\RichEditor::make('procedure_en')->label('Procedure')->columnSpanFull(), Forms\Components\TextInput::make('cta_label_en')->label('Button label'), Forms\Components\TextInput::make('location_en')->label('Location'), Forms\Components\TextInput::make('service_hours_en')->label('Service hours'), Forms\Components\TextInput::make('process_time_en')->label('Processing time'), Forms\Components\TextInput::make('fee_en')->label('Fee'), Forms\Components\TextInput::make('responsible_unit_en')->label('Responsible unit'), Forms\Components\TextInput::make('content_owner_en')->label('Content owner'), Forms\Components\Textarea::make('seo_description_en')->label('SEO description')->columnSpanFull()])->columns(2),
            ])->columnSpanFull(),
            Forms\Components\Section::make('Tindakan & publikasi')->schema([Forms\Components\TextInput::make('cta_url')->label('URL tujuan')->url(), Forms\Components\FileUpload::make('featured_image')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(5120)->directory('services'), Forms\Components\TagsInput::make('keywords')->separator(','), Forms\Components\Toggle::make('is_featured')->label('Unggulan'), Forms\Components\Toggle::make('is_published')->label('Terbit'), Forms\Components\DateTimePicker::make('published_at')->label('Waktu terbit')])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('title')->searchable()->sortable(), Tables\Columns\TextColumn::make('category.name')->label('Kategori'), Tables\Columns\TextColumn::make('delivery_type')->badge(), Tables\Columns\IconColumn::make('is_published')->label('Terbit')->boolean(), Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->since()])->filters([Tables\Filters\TernaryFilter::make('is_published')->label('Status terbit')])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])->bulkActions([Tables\Actions\DeleteBulkAction::make()])->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageServices::route('/')];
    }
}
