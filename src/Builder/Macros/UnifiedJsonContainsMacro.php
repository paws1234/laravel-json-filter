<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Pawsmedz\JsonFilter\Adapters\AdapterFactory;

class UnifiedJsonContainsMacro
{
    /**
     * Apply a unified JSON contains/search that works across all database types
     */
    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath, string $search)
    {
        $adapter = AdapterFactory::getAdapter($builder);
        
        return $adapter->contains($keyPath, $search);
    }
}