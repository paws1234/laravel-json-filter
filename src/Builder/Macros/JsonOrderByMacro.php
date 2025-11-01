<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JsonOrderByMacro
{
    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath, string $direction = 'asc')
    {
        $driver = $builder->getConnection()->getDriverName();
        $column = $this->extractColumn($keyPath);
        $path   = $this->extractJsonPath($keyPath);

        switch ($driver) {
            case 'mysql':
                $expr = "JSON_UNQUOTE(JSON_EXTRACT($column, '$.$path'))";
                break;

            case 'pgsql':
                $expr = $this->pgJsonExpression($keyPath);
                break;

            default:
                $expr = $column; // fallback
        }

        return $builder->orderByRaw("$expr " . strtoupper($direction));
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
