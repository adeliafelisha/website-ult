<?php

namespace App\Models\Concerns;

trait HasTranslations
{
    public function getAttribute($key)
    {
        if (is_string($key) && app()->getLocale() === 'en' && in_array($key, $this->translatable ?? [], true)) {
            $translated = parent::getAttribute($key.'_en');
            if (filled($translated)) {
                return $translated;
            }
        }

        return parent::getAttribute($key);
    }
}
