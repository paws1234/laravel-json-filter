<?php

namespace Pawsmedz\JsonFilter\Adapters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

abstract class DatabaseAdapter
{
    protected EloquentBuilder|QueryBuilder $builder;

    public function __construct(EloquentBuilder|QueryBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get the database type identifier
     */
    abstract public function getDatabaseType(): string;

    /**
     * Check if this adapter supports the given builder
     */
    abstract public function supports(EloquentBuilder|QueryBuilder $builder): bool;

    /**
     * Apply a filter operation
     */
    abstract public function filter(string $keyPath, string $operator, $value): EloquentBuilder|QueryBuilder;

    /**
     * Apply a whereIn operation
     */
    abstract public function whereIn(string $keyPath, array $values): EloquentBuilder|QueryBuilder;

    /**
     * Apply a contains/search operation
     */
    abstract public function contains(string $keyPath, string $search): EloquentBuilder|QueryBuilder;

    /**
     * Check if a path exists
     */
    abstract public function exists(string $keyPath): EloquentBuilder|QueryBuilder;

    /**
     * Order by a JSON path
     */
    abstract public function orderBy(string $keyPath, string $direction = 'asc'): EloquentBuilder|QueryBuilder;

    /**
     * Select a JSON path with alias
     */
    abstract public function select(string $keyPath, ?string $alias = null): EloquentBuilder|QueryBuilder;

    /**
     * Ensure columns are selected (for SQL databases)
     */
    public function ensureSelectAll(): void
    {
        // Default implementation - can be overridden
    }

    /**
     * Extract column name from key path
     */
    protected function extractColumn(string $keyPath): string
    {
        return explode('->', $keyPath)[0];
    }

    /**
     * Convert Laravel arrow notation to database-specific path
     */
    abstract protected function convertPath(string $keyPath): string;
}