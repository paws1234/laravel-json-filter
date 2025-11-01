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
    JsonSelectMacro,
    UnifiedJsonFilterMacro,
    UnifiedJsonWhereInMacro,
    UnifiedJsonContainsMacro,
    UnifiedJsonExistsMacro,
    UnifiedJsonOrderByMacro,
    UnifiedJsonSelectMacro
};

class UnifiedJsonFilterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register unified macros (these work across ALL database types)
        $this->registerUnifiedMacros();
        
        // Keep backward compatibility with original macros
        $this->registerLegacyMacros();
    }

    /**
     * Register the new unified macros that work across all database types
     */
    protected function registerUnifiedMacros(): void
    {
        // These macros automatically detect the database type and use the appropriate adapter
        $this->registerMacro(EloquentBuilder::class, 'jsonFilter', UnifiedJsonFilterMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonFilter', UnifiedJsonFilterMacro::class);

        $this->registerMacro(EloquentBuilder::class, 'jsonOrderBy', UnifiedJsonOrderByMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonOrderBy', UnifiedJsonOrderByMacro::class);

        $this->registerMacro(EloquentBuilder::class, 'jsonWhereIn', UnifiedJsonWhereInMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonWhereIn', UnifiedJsonWhereInMacro::class);

        $this->registerMacro(EloquentBuilder::class, 'jsonContains', UnifiedJsonContainsMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonContains', UnifiedJsonContainsMacro::class);

        $this->registerMacro(EloquentBuilder::class, 'jsonExists', UnifiedJsonExistsMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonExists', UnifiedJsonExistsMacro::class);

        $this->registerMacro(EloquentBuilder::class, 'jsonSelect', UnifiedJsonSelectMacro::class);
        $this->registerMacro(QueryBuilder::class, 'jsonSelect', UnifiedJsonSelectMacro::class);
    }

    /**
     * Register legacy macros for backward compatibility
     * These will be deprecated in future versions
     */
    protected function registerLegacyMacros(): void
    {
        // Only register legacy macros if unified ones aren't already registered
        $this->registerMacro(EloquentBuilder::class, 'jsonFilterLegacy', JsonFilterMacro::class, false);
        $this->registerMacro(QueryBuilder::class, 'jsonFilterLegacy', JsonFilterMacro::class, false);

        $this->registerMacro(EloquentBuilder::class, 'jsonOrderByLegacy', JsonOrderByMacro::class, false);
        $this->registerMacro(QueryBuilder::class, 'jsonOrderByLegacy', JsonOrderByMacro::class, false);

        $this->registerMacro(EloquentBuilder::class, 'jsonWhereInLegacy', JsonWhereInMacro::class, false);
        $this->registerMacro(QueryBuilder::class, 'jsonWhereInLegacy', JsonWhereInMacro::class, false);

        $this->registerMacro(EloquentBuilder::class, 'jsonContainsLegacy', JsonContainsMacro::class, false);
        $this->registerMacro(QueryBuilder::class, 'jsonContainsLegacy', JsonContainsMacro::class, false);

        $this->registerMacro(EloquentBuilder::class, 'jsonExistsLegacy', JsonExistsMacro::class, false);
        $this->registerMacro(QueryBuilder::class, 'jsonExistsLegacy', JsonExistsMacro::class, false);

        $this->registerMacro(EloquentBuilder::class, 'jsonSelectLegacy', JsonSelectMacro::class, false);
        $this->registerMacro(QueryBuilder::class, 'jsonSelectLegacy', JsonSelectMacro::class, false);
    }

    protected function registerMacro(string $builderClass, string $name, string $macroClass, bool $force = true): void
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

        if (!$hasMacro || $force) {
            $builderClass::macro($name, function (...$args) use ($macroClass) {
                return (new $macroClass())($this, ...$args);
            });
        }
    }
}