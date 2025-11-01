<?php

namespace Pawsmedz\JsonFilter\Adapters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class MongoDBAdapter extends DatabaseAdapter
{
    public function getDatabaseType(): string
    {
        return 'mongodb';
    }

    public function supports(EloquentBuilder|QueryBuilder $builder): bool
    {
        try {
            // Check for MongoDB connection
            $connectionClass = get_class($builder->getConnection());
            return str_contains($connectionClass, 'Mongo') || 
                   str_contains($connectionClass, 'MongoDB');
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filter(string $keyPath, string $operator, $value): EloquentBuilder|QueryBuilder
    {
        $path = $this->convertPath($keyPath);
        
        // MongoDB uses different operator syntax
        switch ($operator) {
            case '=':
            case '==':
                return $this->builder->where($path, $value);
            case '!=':
            case '<>':
                return $this->builder->where($path, '!=', $value);
            case '>':
                return $this->builder->where($path, '>', $value);
            case '>=':
                return $this->builder->where($path, '>=', $value);
            case '<':
                return $this->builder->where($path, '<', $value);
            case '<=':
                return $this->builder->where($path, '<=', $value);
            default:
                return $this->builder->where($path, $operator, $value);
        }
    }

    public function whereIn(string $keyPath, array $values): EloquentBuilder|QueryBuilder
    {
        $path = $this->convertPath($keyPath);
        return $this->builder->whereIn($path, $values);
    }

    public function contains(string $keyPath, string $search): EloquentBuilder|QueryBuilder
    {
        $path = $this->convertPath($keyPath);
        
        // MongoDB regex search
        return $this->builder->where($path, 'regexp', "/{$search}/i");
    }

    public function exists(string $keyPath): EloquentBuilder|QueryBuilder
    {
        $path = $this->convertPath($keyPath);
        return $this->builder->whereNotNull($path);
    }

    public function orderBy(string $keyPath, string $direction = 'asc'): EloquentBuilder|QueryBuilder
    {
        $path = $this->convertPath($keyPath);
        return $this->builder->orderBy($path, $direction);
    }

    public function select(string $keyPath, ?string $alias = null): EloquentBuilder|QueryBuilder
    {
        $path = $this->convertPath($keyPath);
        
        // MongoDB projections work differently
        if ($alias) {
            // For MongoDB, we might need to use aggregation pipeline
            return $this->builder->project([$alias => "\$$path"]);
        } else {
            return $this->builder->project([$path => 1]);
        }
    }

    public function ensureSelectAll(): void
    {
        // MongoDB doesn't need this - documents are returned as-is by default
    }

    protected function convertPath(string $keyPath): string
    {
        // Convert 'meta->profile->name' to 'meta.profile.name'
        return str_replace('->', '.', $keyPath);
    }
}