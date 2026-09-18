<?php

namespace App\Domain\Learning\Content;

final class MachineKey
{
    public static function assert(string $key, string $kind = 'key'): void
    {
        if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $key)) {
            throw new ContentValidationException("Invalid {$kind} [{$key}]. Use lowercase kebab-case.");
        }
    }

    public static function assertVersion(string $version): void
    {
        if (! preg_match('/^v[1-9][0-9]*$/', $version)) {
            throw new ContentValidationException("Invalid dataset version [{$version}]. Use v1, v2, and so on.");
        }
    }
}
