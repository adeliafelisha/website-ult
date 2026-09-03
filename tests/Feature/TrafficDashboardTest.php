<?php

namespace Tests\Feature;

use App\Models\PageView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrafficDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_html_pages_are_tracked_without_raw_personal_data(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; Mobile)')
            ->withHeader('Referer', 'https://google.com/search?q=ult')
            ->get('/profil')
            ->assertOk();

        $view = PageView::sole();

        $this->assertSame('/profil', $view->path);
        $this->assertSame('profile', $view->route_name);
        $this->assertSame('google.com', $view->referrer_host);
        $this->assertSame('mobile', $view->device_type);
        $this->assertSame(64, strlen($view->visitor_hash));
        $this->assertSame(64, strlen($view->ip_hash));
        $this->assertStringNotContainsString('127.0.0.1', $view->ip_hash);
    }

    public function test_bots_and_admin_pages_are_not_counted(): void
    {
        $this->withHeader('User-Agent', 'Googlebot')->get('/')->assertOk();

        $admin = User::factory()->create([
            'email' => 'traffic-admin@unpad.ac.id',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)->get('/admin')->assertOk();

        $this->assertDatabaseCount('page_views', 0);
    }

    public function test_dashboard_renders_with_traffic_data(): void
    {
        PageView::create([
            'path' => '/',
            'route_name' => 'home',
            'locale' => 'id',
            'visitor_hash' => hash('sha256', 'visitor'),
            'ip_hash' => hash('sha256', 'ip'),
            'device_type' => 'desktop',
            'viewed_at' => now(),
        ]);

        $admin = User::factory()->create([
            'email' => 'dashboard-admin@unpad.ac.id',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Ringkasan Trafik')
            ->assertSee('Trafik 12 Bulan Terakhir')
            ->assertSee('Status Konten')
            ->assertSee('Insight 30 Hari Terakhir');
    }
}
