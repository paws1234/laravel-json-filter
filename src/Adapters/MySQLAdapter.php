<?php

namespace Pawsmedz\JsonFilter\Adapters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class MySQLAdapter extends DatabaseAdapter
{
    public function getDatabaseType(): string
    {
        return 'mysql';
    }

    public function supports(EloquentBuilder|QueryBuilder $builder): bool
    {
        try {
            $connection = $builder->getConnection();
            
            // Check connection class name for MySQL indicators
            $connectionClass = get_class($connection);
            return str_contains($connectionClass, 'MySQL') || 
                   str_contains($connectionClass, 'mysql') ||
                   str_contains($connectionClass, 'MySql');
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filter(string $keyPath, string $operator, $value): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $path = $this->convertPath($keyPath);
        $column = $this->extractColumn($keyPath);
        
        $expr = "JSON_UNQUOTE(JSON_EXTRACT($column, '$.$path'))";
        return $this->builder->whereRaw("$expr {$operator} ?", [$value]);
    }

    public function whereIn(string $keyPath, array $values): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $path = $this->convertPath($keyPath);
        $column = $this->extractColumn($keyPath);
        
        $expr = "JSON_UNQUOTE(JSON_EXTRACT($column, '$.$path'))";
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        return $this->builder->whereRaw("{$expr} IN ({$placeholders})", $values);
    }

    public function contains(string $keyPath, string $search): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $path = $this->convertPath($keyPath);
        $column = $this->extractColumn($keyPath);
        
        $expr = "JSON_UNQUOTE(JSON_EXTRACT($column, '$.$path'))";
        return $this->builder->whereRaw("$expr LIKE ?", ["%{$search}%"]);
    }

    public function exists(string $keyPath): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $path = $this->convertPath($keyPath);
        $column = $this->extractColumn($keyPath);
        
        return $this->builder->whereRaw("JSON_CONTAINS_PATH($column, 'one', '$.$path')");
    }

    public function orderBy(string $keyPath, string $direction = 'asc'): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $path = $this->convertPath($keyPath);
        $column = $this->extractColumn($keyPath);
        
        $expr = "JSON_UNQUOTE(JSON_EXTRACT($column, '$.$path'))";
        return $this->builder->orderByRaw("$expr " . strtoupper($direction));
    }

    public function select(string $keyPath, ?string $alias = null): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $path = $this->convertPath($keyPath);
        $column = $this->extractColumn($keyPath);
        $alias = $alias ?? str_replace(['->', '.'], '_', $keyPath);
        
        $expr = "JSON_UNQUOTE(JSON_EXTRACT($column, '$.$path')) as $alias";
        return $this->builder->addSelect($this->builder->getConnection()->raw($expr));
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
        $parts = explode('->', $keyPath);
        array_shift($parts); // Remove column name
        return implode('.', $parts);
    }
}