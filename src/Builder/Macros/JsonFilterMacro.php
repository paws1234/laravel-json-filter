<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JsonFilterMacro
{
    use JsonMacroTrait;

    /**
     * Apply a JSON filter to an Eloquent or Query Builder.
     */
    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath, string $operator, $value)
    {
        $this->ensureSelectAll($builder);
        
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
                // Fallback for SQLite or unsupported drivers
                $jsonExpr = "$column LIKE ?";
                $value = "%{$value}%";
        }

        return $builder->whereRaw("$jsonExpr {$operator} ?", [$value]);
    }
}
