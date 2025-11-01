<?php

namespace Pawsmedz\JsonFilter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Pawsmedz\JsonFilter\Builder\Macros\JsonFilterMacro;

class JsonFilterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Handle Laravel versions where hasMacro() is non-static (Laravel 9/10)
        $hasMacro = false;

        try {
            $builderInstance = app(Builder::class);
            if (method_exists($builderInstance, 'hasMacro')) {
                $hasMacro = $builderInstance->hasMacro('jsonFilter');
            }
        } catch (\Throwable $e) {
            // If app(Builder::class) cannot resolve (during boot), fallback
            $hasMacro = false;
        }

        if (!$hasMacro) {
            // Safe macro registration
            Builder::macro('jsonFilter', function (string $keyPath, string $operator, $value) {
                return (new JsonFilterMacro)($this, $keyPath, $operator, $value);
            });
        }
    }
}
