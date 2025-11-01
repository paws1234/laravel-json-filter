<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder;

class JsonFilterMacro
{
    /**
     * Apply a JSON filter to an Eloquent query.
     *
     * @param  Builder  $builder
     * @param  string   $keyPath
     * @param  string   $operator
     * @param  mixed    $value
     * @return Builder
     */
    public function __invoke(Builder $builder, string $keyPath, string $operator, $value): Builder
    {
        $driver = $builder->getConnection()->getDriverName();
        $column = $this->extractColumn($keyPath);
        $path   = $this->extractJsonPath($keyPath);

        switch ($driver) {
            case 'mysql':
                $jsonExpr = "JSON_UNQUOTE(JSON_EXTRACT($column, '$.$path'))";
                break;

            case 'pgsql':
                $jsonExpr = $this->pgJsonExpression($keyPath);
                break;

            default:
                throw new \RuntimeException("Unsupported driver: {$driver}");
        }

        return $builder->whereRaw("$jsonExpr {$operator} ?", [$value]);
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
