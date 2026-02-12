<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use App\Services\GeoLocationService;
use App\Support\UserAgentParser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request)) {
            $this->track($request);
        }

        return $response;
    }

    private function shouldTrack(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        $path = $request->path();

        if ($path === 'up' || str_starts_with($path, 'admin')) {
            return false;
        }

        return true;
    }

    private function track(Request $request): void
    {
        $ua = UserAgentParser::parse($request->userAgent());
        $location = app(GeoLocationService::class)->resolve($request->ip());

        PageVisit::create([
            'url' => $request->fullUrl(),
            'route_name' => $request->route()?->getName(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => $ua['browser'],
            'platform' => $ua['platform'],
            'device_type' => $ua['device_type'],
            'country' => $location['country'] ?? null,
            'state' => $location['state'] ?? null,
            'city' => $location['city'] ?? null,
            'referer' => $request->header('referer'),
            'user_id' => $request->user()?->id,
            'visited_at' => now(),
        ]);
    }
}
