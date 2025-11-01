<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait JsonMacroTrait
{
    /**
     * Ensure the builder has columns selected to prevent breaking pluck() and get() operations.
     * If no columns are explicitly selected, select all (*) to maintain default behavior.
     */
    protected function ensureSelectAll(EloquentBuilder|QueryBuilder $builder): void
    {
        $query = $builder instanceof EloquentBuilder ? $builder->getQuery() : $builder;
        
        if (empty($query->columns)) {
            $builder->select('*');
        }
    }

    /**
     * Extract the column name from a JSON key path (e.g., 'meta->status' => 'meta').
     */
    protected function extractColumn(string $keyPath): string
    {
        return explode('->', $keyPath)[0];
    }

    /**
     * Extract the JSON path from a key path (e.g., 'meta->status->active' => 'status.active').
     */
    protected function extractJsonPath(string $keyPath): string
    {
        $parts = explode('->', $keyPath);
        array_shift($parts);
        return implode('.', $parts);
    }

    /**
     * Generate PostgreSQL JSON expression for the given key path.
     */
    protected function pgJsonExpression(string $keyPath): string
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