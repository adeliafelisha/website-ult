<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class CsvContentImporter
{
    public static function articles(UploadedFile $file): int
    {
        return self::process($file, function (array $row, int $line): void {
            self::require($row, ['title', 'category', 'excerpt', 'content'], $line);
            $slug = $row['slug'] ?: Str::slug($row['title']);
            Article::updateOrCreate(['slug' => $slug], self::only($row, [
                'title', 'title_en', 'category', 'category_en', 'excerpt', 'excerpt_en', 'content', 'content_en',
                'author', 'author_en', 'content_owner', 'content_owner_en', 'external_url', 'external_label',
                'external_label_en', 'seo_description', 'seo_description_en',
            ]) + ['slug' => $slug, 'is_featured' => self::bool($row['is_featured'] ?? false), 'is_published' => self::bool($row['is_published'] ?? false), 'published_at' => self::bool($row['is_published'] ?? false) ? now() : null]);
        });
    }

    public static function faqs(UploadedFile $file): int
    {
        return self::process($file, function (array $row, int $line): void {
            self::require($row, ['question', 'answer', 'category'], $line);
            Faq::updateOrCreate(['question' => $row['question']], self::only($row, [
                'question', 'question_en', 'answer', 'answer_en', 'category', 'category_en', 'audience',
                'audience_en', 'external_url', 'external_label', 'external_label_en',
            ]) + ['sort_order' => (int) ($row['sort_order'] ?? 0), 'is_featured' => self::bool($row['is_featured'] ?? false), 'is_published' => self::bool($row['is_published'] ?? true)]);
        });
    }

    public static function services(UploadedFile $file): int
    {
        return self::process($file, function (array $row, int $line): void {
            self::require($row, ['category_slug', 'title', 'summary'], $line);
            $category = ServiceCategory::where('slug', $row['category_slug'])->first();
            if (! $category) {
                throw new RuntimeException("Baris {$line}: category_slug tidak ditemukan.");
            }
            $slug = $row['slug'] ?: Str::slug($row['title']);
            $delivery = in_array($row['delivery_type'] ?? 'online', ['online', 'offline', 'hybrid'], true) ? $row['delivery_type'] : 'online';
            $contactButtons = self::contactButtons($row);
            Service::updateOrCreate(['slug' => $slug], self::only($row, [
                'title', 'title_en', 'summary', 'summary_en', 'audience', 'audience_en', 'requirements',
                'requirements_en', 'documents', 'documents_en', 'procedure', 'procedure_en', 'location', 'location_en', 'service_hours', 'service_hours_en',
                'process_time', 'process_time_en', 'fee', 'fee_en', 'responsible_unit', 'responsible_unit_en',
                'content_owner', 'content_owner_en', 'seo_description', 'seo_description_en',
            ]) + ['slug' => $slug, 'service_category_id' => $category->id, 'delivery_type' => $delivery, 'contact_buttons' => $contactButtons, 'is_featured' => self::bool($row['is_featured'] ?? false), 'is_published' => self::bool($row['is_published'] ?? false), 'published_at' => self::bool($row['is_published'] ?? false) ? now() : null]);
        });
    }

    private static function contactButtons(array $row): array
    {
        $buttons = [];
        foreach (range(1, 3) as $number) {
            $url = $row["contact_{$number}_url"] ?? null;
            if (blank($url)) {
                continue;
            }
            $buttons[] = [
                'label' => ($row["contact_{$number}_label"] ?? null) ?: 'Hubungi Admin ULT',
                'label_en' => ($row["contact_{$number}_label_en"] ?? null) ?: null,
                'channel' => ($row["contact_{$number}_channel"] ?? null) ?: 'other',
                'url' => $url,
            ];
        }

        if ($buttons) {
            return $buttons;
        }

        return [[
            'label' => 'Hubungi Admin ULT',
            'label_en' => 'Contact ULT Admin',
            'channel' => 'whatsapp',
            'url' => Contact::where('type', 'whatsapp')->where('is_published', true)->orderBy('sort_order')->value('url') ?: 'https://wa.me/6280000000000',
        ]];
    }

    private static function process(UploadedFile $file, callable $callback): int
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (! $handle) {
            throw new RuntimeException('File CSV tidak dapat dibaca.');
        }
        $headers = fgetcsv($handle);
        if (! $headers) {
            fclose($handle);
            throw new RuntimeException('Header CSV tidak ditemukan.');
        }
        $headers = array_map(fn ($header) => Str::snake(trim(str_replace("\xEF\xBB\xBF", '', (string) $header))), $headers);
        $count = 0;
        $line = 1;
        while (($values = fgetcsv($handle)) !== false) {
            $line++;
            if (count(array_filter($values, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }
            $values = array_pad($values, count($headers), null);
            $row = array_map(fn ($value) => is_string($value) ? trim($value) : $value, array_combine($headers, array_slice($values, 0, count($headers))));
            $callback($row, $line);
            $count++;
        }
        fclose($handle);

        return $count;
    }

    private static function require(array $row, array $fields, int $line): void
    {
        foreach ($fields as $field) {
            if (blank($row[$field] ?? null)) {
                throw new RuntimeException("Baris {$line}: kolom {$field} wajib diisi.");
            }
        }
    }

    private static function only(array $row, array $fields): array
    {
        return array_filter(array_intersect_key($row, array_flip($fields)), fn ($value) => $value !== null && $value !== '');
    }

    private static function bool(mixed $value): bool
    {
        return in_array(strtolower((string) $value), ['1', 'true', 'ya', 'yes', 'publish', 'published'], true);
    }
}
