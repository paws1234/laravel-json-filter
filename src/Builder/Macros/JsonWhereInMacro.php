<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JsonWhereInMacro
{
    use JsonMacroTrait;

    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath, array $values)
    {
        $this->ensureSelectAll($builder);
        
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
                $expr = $column;
        }

        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        return $builder->whereRaw("{$expr} IN ({$placeholders})", $values);
    }
}
