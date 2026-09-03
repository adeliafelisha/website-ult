<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            PageView::create([
                'path' => '/'.ltrim($request->path(), '/'),
                'route_name' => $request->route()?->getName(),
                'locale' => app()->getLocale(),
                'referrer_host' => $this->referrerHost($request),
                'visitor_hash' => hash_hmac('sha256', $request->session()->getId(), (string) config('app.key')),
                'ip_hash' => hash_hmac('sha256', (string) $request->ip(), (string) config('app.key')),
                'device_type' => $this->deviceType((string) $request->userAgent()),
                'viewed_at' => now(),
            ]);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $request->is('admin', 'admin/*')) {
            return false;
        }

        if ($response->getStatusCode() >= 300 || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }

        return ! preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp/i', (string) $request->userAgent());
    }

    private function referrerHost(Request $request): ?string
    {
        $host = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);

        if (! is_string($host) || $host === '' || $host === $request->getHost()) {
            return null;
        }

        return mb_substr(strtolower($host), 0, 255);
    }

    private function deviceType(string $userAgent): string
    {
        if (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'tablet';
        }

        return preg_match('/mobile|android|iphone/i', $userAgent) ? 'mobile' : 'desktop';
    }
}
