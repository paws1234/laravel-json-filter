<?php

namespace Pawsmedz\JsonFilter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Pawsmedz\JsonFilter\Builder\Macros\JsonFilterMacro;

class JsonFilterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Backward-safe macro registration
        if (!Builder::hasMacro('jsonFilter')) {
            Builder::macro('jsonFilter', function (string $keyPath, string $operator, $value) {
                return (new JsonFilterMacro)($this, $keyPath, $operator, $value);
            });
        }
    }
}