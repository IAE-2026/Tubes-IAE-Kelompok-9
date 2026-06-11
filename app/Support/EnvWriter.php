<?php

namespace App\Support;

class EnvWriter
{
    public static function set(string $key, ?string $value): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            throw new \RuntimeException('.env file not found.');
        }

        $content = file_get_contents($envPath);
        $escapedValue = self::escapeValue($value);
        $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $key.'='.$escapedValue, $content);
        } else {
            $content = rtrim($content).PHP_EOL.$key.'='.$escapedValue.PHP_EOL;
        }

        file_put_contents($envPath, $content);
    }

    private static function escapeValue(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (preg_match('/[\s#"]/', $value)) {
            return '"'.str_replace('"', '\\"', $value).'"';
        }

        return $value;
    }
}
