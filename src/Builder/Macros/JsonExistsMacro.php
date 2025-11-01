<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JsonExistsMacro
{
    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath)
    {
        $driver = $builder->getConnection()->getDriverName();
        $column = $this->extractColumn($keyPath);
        $path   = $this->extractJsonPath($keyPath);

        switch ($driver) {
            case 'mysql':
                return $builder->whereRaw("JSON_CONTAINS_PATH($column, 'one', '$.$path')");
            case 'pgsql':
                $expr = $this->pgJsonExpression($keyPath);
                return $builder->whereRaw("$expr IS NOT NULL");
            default:
                return $builder->whereNotNull($column);
        }
    }

    protected function extractColumn(string $keyPath): string
    {
        return explode('->', $keyPath)[0];
    }

    protected function extractJsonPath(string $keyPath): string
    {
        $parts = explode('->', $keyPath);
        array_shift($parts);
        return implode('.', $parts);
    }

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
