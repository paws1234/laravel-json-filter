<?php

namespace Pawsmedz\JsonFilter\Adapters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class AdapterFactory
{
    /**
     * Available database adapters
     */
    protected static array $adapters = [
        MySQLAdapter::class,
        PostgreSQLAdapter::class,
        MongoDBAdapter::class,
        // Add more adapters here
    ];

    /**
     * Get the appropriate adapter for the given builder
     */
    public static function getAdapter(EloquentBuilder|QueryBuilder $builder): DatabaseAdapter
    {
        foreach (static::$adapters as $adapterClass) {
            $adapter = new $adapterClass($builder);
            
            if ($adapter->supports($builder)) {
                return $adapter;
            }
        }

        // Fallback to a generic adapter
        return new GenericAdapter($builder);
    }

    /**
     * Register a new adapter
     */
    public static function registerAdapter(string $adapterClass): void
    {
        if (!in_array($adapterClass, static::$adapters)) {
            array_unshift(static::$adapters, $adapterClass);
        }
    }

    /**
     * Get all registered adapters
     */
    public static function getAdapters(): array
    {
        return static::$adapters;
    }
}