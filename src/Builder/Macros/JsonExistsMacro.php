<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JsonExistsMacro
{
    use JsonMacroTrait;

    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath)
    {
        $this->ensureSelectAll($builder);
        
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
}
