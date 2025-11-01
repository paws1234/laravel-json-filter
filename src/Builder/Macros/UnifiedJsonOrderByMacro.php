<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Pawsmedz\JsonFilter\Adapters\AdapterFactory;

class UnifiedJsonOrderByMacro
{
    /**
     * Apply a unified JSON order by that works across all database types
     */
    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath, string $direction = 'asc')
    {
        $adapter = AdapterFactory::getAdapter($builder);
        
        return $adapter->orderBy($keyPath, $direction);
    }
}