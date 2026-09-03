<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_responses_include_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_trusted_host_pattern_is_restricted_to_application_domain(): void
    {
        $patterns = (new \App\Http\Middleware\TrustHosts(app()))->hosts();

        $this->assertNotEmpty($patterns);
        $this->assertDoesNotMatchRegularExpression('/'.$patterns[0].'/', 'attacker.example');
    }

    public function test_https_responses_enable_hsts(): void
    {
        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security');
    }

    public function test_rich_text_is_sanitized_before_storage(): void
    {
        $article = new Article;
        $article->content = '<p onclick="steal()">Aman</p><script>alert(1)</script><a href="javascript:alert(1)">tautan</a>';

        $this->assertStringNotContainsString('onclick', $article->content);
        $this->assertStringNotContainsString('<script', $article->content);
        $this->assertStringNotContainsString('javascript:', $article->content);
        $this->assertStringContainsString('<p>Aman</p>', $article->content);
    }

    public function test_only_verified_official_admins_can_access_filament(): void
    {
        $panel = Filament::getPanel('admin');
        $official = User::factory()->make(['email' => 'editor@unpad.ac.id', 'email_verified_at' => now(), 'is_admin' => true]);
        $unverified = User::factory()->make(['email' => 'editor@unpad.ac.id', 'email_verified_at' => null, 'is_admin' => true]);
        $external = User::factory()->make(['email' => 'admin@example.com', 'email_verified_at' => now(), 'is_admin' => true]);

        $this->assertTrue($official->canAccessPanel($panel));
        $this->assertFalse($unverified->canAccessPanel($panel));
        $this->assertFalse($external->canAccessPanel($panel));
    }

    public function test_search_input_has_a_length_limit(): void
    {
        $this->get('/pencarian?q='.str_repeat('a', 101))->assertSessionHasErrors('q');
    }
}
