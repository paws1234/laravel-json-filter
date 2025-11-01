<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Pawsmedz\JsonFilter\Adapters\AdapterFactory;

class UnifiedJsonFilterMacro
{
    /**
     * Apply a unified JSON filter that works across all database types
     */
    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath, string $operator, $value)
    {
        $adapter = AdapterFactory::getAdapter($builder);
        
        return $adapter->filter($keyPath, $operator, $value);
    }
}