<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Pawsmedz\JsonFilter\Adapters\AdapterFactory;

class UnifiedJsonExistsMacro
{
    /**
     * Apply a unified JSON exists check that works across all database types
     */
    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath)
    {
        $adapter = AdapterFactory::getAdapter($builder);
        
        return $adapter->exists($keyPath);
    }
}