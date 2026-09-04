<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Informasi';

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Artikel';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Bahasa')->tabs([
                Forms\Components\Tabs\Tab::make('Indonesia')->schema([Forms\Components\TextInput::make('title')->label('Judul')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))), Forms\Components\TextInput::make('category')->label('Kategori')->required(), Forms\Components\Textarea::make('excerpt')->label('Ringkasan')->required()->columnSpanFull(), Forms\Components\RichEditor::make('content')->label('Isi artikel')->required()->columnSpanFull(), Forms\Components\TextInput::make('author')->label('Penulis')->default('ULT Unpad'), Forms\Components\TextInput::make('content_owner')->label('Pemilik konten'), Forms\Components\TextInput::make('external_label')->label('Label tombol link')->placeholder('Baca sumber lengkap'), Forms\Components\Textarea::make('seo_description')->label('Deskripsi SEO')->columnSpanFull()])->columns(2),
                Forms\Components\Tabs\Tab::make('English')->schema([Forms\Components\TextInput::make('title_en')->label('Title'), Forms\Components\TextInput::make('category_en')->label('Category'), Forms\Components\Textarea::make('excerpt_en')->label('Summary')->columnSpanFull(), Forms\Components\RichEditor::make('content_en')->label('Article content')->columnSpanFull(), Forms\Components\TextInput::make('author_en')->label('Author'), Forms\Components\TextInput::make('content_owner_en')->label('Content owner'), Forms\Components\TextInput::make('external_label_en')->label('Link button label'), Forms\Components\Textarea::make('seo_description_en')->label('SEO description')->columnSpanFull()])->columns(2),
            ])->columnSpanFull(),
            Forms\Components\Section::make('Publikasi')->schema([Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true), Forms\Components\TextInput::make('external_url')->label('URL tombol opsional')->url()->helperText('Jika diisi, tombol akan muncul di akhir artikel.'), Forms\Components\FileUpload::make('featured_image')->label('Gambar utama')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(5120)->directory('articles'), Forms\Components\TagsInput::make('keywords')->separator(','), Forms\Components\Toggle::make('is_featured')->label('Unggulan'), Forms\Components\Toggle::make('is_published')->label('Terbit'), Forms\Components\DateTimePicker::make('published_at')->label('Waktu terbit')])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\ImageColumn::make('featured_image')->label(''), Tables\Columns\TextColumn::make('title')->searchable()->sortable(), Tables\Columns\TextColumn::make('category')->badge(), Tables\Columns\IconColumn::make('external_url')->label('Link')->boolean(), Tables\Columns\IconColumn::make('is_published')->label('Terbit')->boolean(), Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->since()])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])->bulkActions([Tables\Actions\BulkAction::make('publish')->label('Terbitkan')->icon('heroicon-o-eye')->action(fn (Collection $records) => $records->each->update(['is_published' => true, 'published_at' => now()]))->deselectRecordsAfterCompletion(), Tables\Actions\BulkAction::make('unpublish')->label('Batalkan terbit')->icon('heroicon-o-eye-slash')->color('warning')->action(fn (Collection $records) => $records->each->update(['is_published' => false]))->deselectRecordsAfterCompletion(), Tables\Actions\DeleteBulkAction::make()])->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageArticles::route('/')];
    }
}
