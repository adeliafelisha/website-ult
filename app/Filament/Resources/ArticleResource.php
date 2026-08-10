<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Informasi';

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Artikel';

    public static function form(Form $f): Form
    {
        return $f->schema([Forms\Components\TextInput::make('title')->label('Judul')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))), Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true), Forms\Components\TextInput::make('category')->label('Kategori')->required(), Forms\Components\Textarea::make('excerpt')->label('Ringkasan')->required()->columnSpanFull(), Forms\Components\RichEditor::make('content')->label('Isi artikel')->required()->columnSpanFull(), Forms\Components\FileUpload::make('featured_image')->label('Gambar utama')->image()->directory('articles'), Forms\Components\TextInput::make('author')->default('ULT Unpad'), Forms\Components\TextInput::make('content_owner')->label('Pemilik konten'), Forms\Components\TagsInput::make('keywords')->separator(','), Forms\Components\Textarea::make('seo_description')->columnSpanFull(), Forms\Components\Toggle::make('is_featured')->label('Unggulan'), Forms\Components\Toggle::make('is_published')->label('Terbit'), Forms\Components\DateTimePicker::make('published_at')->label('Waktu terbit')])->columns(2);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([Tables\Columns\ImageColumn::make('featured_image')->label(''), Tables\Columns\TextColumn::make('title')->searchable()->sortable(), Tables\Columns\TextColumn::make('category')->badge(), Tables\Columns\IconColumn::make('is_published')->boolean(), Tables\Columns\TextColumn::make('published_at')->dateTime('d M Y')])->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageArticles::route('/')];
    }
}
