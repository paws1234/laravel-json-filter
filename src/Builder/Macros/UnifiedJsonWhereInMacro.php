<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Pawsmedz\JsonFilter\Adapters\AdapterFactory;

class UnifiedJsonWhereInMacro
{
    /**
     * Apply a unified JSON whereIn that works across all database types
     */
    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath, array $values)
    {
        $adapter = AdapterFactory::getAdapter($builder);
        
        return $adapter->whereIn($keyPath, $values);
    }
}