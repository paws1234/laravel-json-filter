<?php

namespace Pawsmedz\JsonFilter\Adapters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class GenericAdapter extends DatabaseAdapter
{
    public function getDatabaseType(): string
    {
        return 'generic';
    }

    public function supports(EloquentBuilder|QueryBuilder $builder): bool
    {
        // This is the fallback adapter - it always "supports" any builder
        return true;
    }

    public function filter(string $keyPath, string $operator, $value): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $column = $this->extractColumn($keyPath);
        
        // Fallback to basic column operations
        return $this->builder->where($column, 'LIKE', "%{$value}%");
    }

    public function whereIn(string $keyPath, array $values): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $column = $this->extractColumn($keyPath);
        
        // Basic whereIn on the column
        return $this->builder->whereIn($column, $values);
    }

    public function contains(string $keyPath, string $search): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $column = $this->extractColumn($keyPath);
        
        return $this->builder->where($column, 'LIKE', "%{$search}%");
    }

    public function exists(string $keyPath): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $column = $this->extractColumn($keyPath);
        
        return $this->builder->whereNotNull($column);
    }

    public function orderBy(string $keyPath, string $direction = 'asc'): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $column = $this->extractColumn($keyPath);
        
        return $this->builder->orderBy($column, $direction);
    }

    public function select(string $keyPath, ?string $alias = null): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $column = $this->extractColumn($keyPath);
        $alias = $alias ?? $column;
        
        return $this->builder->addSelect($this->builder->getConnection()->raw("$column as $alias"));
    }

    public function ensureSelectAll(): void
    {
        $query = $this->builder instanceof EloquentBuilder ? $this->builder->getQuery() : $this->builder;
        
        if (empty($query->columns)) {
            $this->builder->select('*');
        }
    }

    protected function convertPath(string $keyPath): string
    {
        // For generic adapter, just return the column name
        return $this->extractColumn($keyPath);
    }
}