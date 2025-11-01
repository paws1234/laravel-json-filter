<?php

namespace Pawsmedz\JsonFilter\Adapters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class PostgreSQLAdapter extends DatabaseAdapter
{
    public function getDatabaseType(): string
    {
        return 'pgsql';
    }

    public function supports(EloquentBuilder|QueryBuilder $builder): bool
    {
        try {
            $connection = $builder->getConnection();
            
            // Check connection class name for PostgreSQL indicators
            $connectionClass = get_class($connection);
            return str_contains($connectionClass, 'PostgreS') || 
                   str_contains($connectionClass, 'pgsql') ||
                   str_contains($connectionClass, 'Postgres');
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filter(string $keyPath, string $operator, $value): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $expr = $this->convertPath($keyPath);
        return $this->builder->whereRaw("$expr {$operator} ?", [$value]);
    }

    public function whereIn(string $keyPath, array $values): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $expr = $this->convertPath($keyPath);
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        return $this->builder->whereRaw("{$expr} IN ({$placeholders})", $values);
    }

    public function contains(string $keyPath, string $search): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $expr = $this->convertPath($keyPath);
        return $this->builder->whereRaw("$expr LIKE ?", ["%{$search}%"]);
    }

    public function exists(string $keyPath): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $expr = $this->convertPath($keyPath);
        return $this->builder->whereRaw("$expr IS NOT NULL");
    }

    public function orderBy(string $keyPath, string $direction = 'asc'): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $expr = $this->convertPath($keyPath);
        return $this->builder->orderByRaw("$expr " . strtoupper($direction));
    }

    public function select(string $keyPath, ?string $alias = null): EloquentBuilder|QueryBuilder
    {
        $this->ensureSelectAll();
        $expr = $this->convertPath($keyPath);
        $alias = $alias ?? str_replace(['->', '.'], '_', $keyPath);
        return $this->builder->addSelectRaw("$expr as $alias");
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
        $segments = explode('->', $keyPath);
        $column = array_shift($segments);
        $expr = $column;

        foreach ($segments as $i => $seg) {
            $arrow = $i === count($segments) - 1 ? '->>' : '->';
            $expr .= "{$arrow}'{$seg}'";
        }

        return $expr;
    }
}