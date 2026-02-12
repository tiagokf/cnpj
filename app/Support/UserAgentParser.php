<?php

namespace App\Support;

class UserAgentParser
{
    public static function parse(?string $userAgent): array
    {
        if (! $userAgent) {
            return [
                'browser' => 'Desconhecido',
                'platform' => 'Desconhecido',
                'device_type' => 'desktop',
            ];
        }

        return [
            'browser' => static::detectBrowser($userAgent),
            'platform' => static::detectPlatform($userAgent),
            'device_type' => static::detectDeviceType($userAgent),
        ];
    }

    private static function detectBrowser(string $ua): string
    {
        $browsers = [
            'Edge' => '/Edg[e\/]/',
            'Opera' => '/OPR\/|Opera/',
            'Samsung Internet' => '/SamsungBrowser/',
            'Chrome' => '/Chrome\//',
            'Firefox' => '/Firefox\//',
            'Safari' => '/Safari\//',
            'IE' => '/MSIE|Trident/',
        ];

        foreach ($browsers as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                return $name;
            }
        }

        return 'Outro';
    }

    private static function detectPlatform(string $ua): string
    {
        $platforms = [
            'Windows' => '/Windows/',
            'macOS' => '/Macintosh/',
            'Linux' => '/Linux(?!.*Android)/',
            'Android' => '/Android/',
            'iOS' => '/iPhone|iPad|iPod/',
            'Chrome OS' => '/CrOS/',
        ];

        foreach ($platforms as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                return $name;
            }
        }

        return 'Outro';
    }

    private static function detectDeviceType(string $ua): string
    {
        if (preg_match('/Mobile|Android.*Mobile|iPhone|iPod/', $ua)) {
            return 'mobile';
        }

        if (preg_match('/iPad|Android(?!.*Mobile)|Tablet/', $ua)) {
            return 'tablet';
        }

        return 'desktop';
    }
}
