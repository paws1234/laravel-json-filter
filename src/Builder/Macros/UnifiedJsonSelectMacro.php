<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Pawsmedz\JsonFilter\Adapters\AdapterFactory;

class UnifiedJsonSelectMacro
{
    /**
     * Apply a unified JSON select that works across all database types
     */
    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPathWithAlias)
    {
        $adapter = AdapterFactory::getAdapter($builder);
        
        [$keyPath, $alias] = array_pad(explode(' as ', $keyPathWithAlias), 2, null);
        
        return $adapter->select($keyPath, $alias);
    }
}