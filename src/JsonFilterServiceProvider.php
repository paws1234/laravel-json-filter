<?php

namespace Pawsmedz\JsonFilter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Pawsmedz\JsonFilter\Builder\Macros\{
    JsonFilterMacro,
    JsonOrderByMacro,
    JsonWhereInMacro,
    JsonContainsMacro,
    JsonExistsMacro,
    JsonSelectMacro
};

class JsonFilterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerMacro(EloquentBuilder::class, 'jsonFilter', JsonFilterMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonFilter', JsonFilterMacro::class);

        $this->registerMacro(EloquentBuilder::class, 'jsonOrderBy', JsonOrderByMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonOrderBy', JsonOrderByMacro::class);

        $this->registerMacro(EloquentBuilder::class, 'jsonWhereIn', JsonWhereInMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonWhereIn', JsonWhereInMacro::class);

        $this->registerMacro(EloquentBuilder::class, 'jsonContains', JsonContainsMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonContains', JsonContainsMacro::class);

        $this->registerMacro(EloquentBuilder::class, 'jsonExists', JsonExistsMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonExists', JsonExistsMacro::class);

        $this->registerMacro(EloquentBuilder::class, 'jsonSelect', JsonSelectMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonSelect', JsonSelectMacro::class);
    }

    protected function registerMacro(string $builderClass, string $name, string $macroClass): void
    {
        $instance = app($builderClass);

        $hasMacro = false;
        try {
            if (method_exists($instance, 'hasMacro')) {
                $hasMacro = $instance->hasMacro($name);
            }
        } catch (\Throwable $e) {
            $hasMacro = false;
        }

        if (! $hasMacro) {
            $builderClass::macro($name, function (...$args) use ($macroClass) {
                // The macro itself should be callable, not invoked now
                return (new $macroClass())($this, ...$args);
            });
        }
    }
}
