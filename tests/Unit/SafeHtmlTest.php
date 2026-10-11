<?php

namespace Tests\Unit;

use App\Support\SafeHtml;
use PHPUnit\Framework\TestCase;

class SafeHtmlTest extends TestCase
{
    public function test_nested_dangerous_markup_is_removed(): void
    {
        $clean = SafeHtml::clean('<div><script>alert(1)</script><p onclick="steal()">Aman</p></div>');

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringContainsString('<p>Aman</p>', $clean);
    }

    public function test_only_catalog_images_are_allowed(): void
    {
        $clean = SafeHtml::clean('<img src="/images/service-guides/mahasiswa/pacis-01.jpg" alt="PACIS" onclick="steal()"><img src="https://attacker.example/tracker.png">');

        $this->assertStringContainsString('/images/service-guides/mahasiswa/pacis-01.jpg', $clean);
        $this->assertStringContainsString('loading="lazy"', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('attacker.example', $clean);
    }
}
