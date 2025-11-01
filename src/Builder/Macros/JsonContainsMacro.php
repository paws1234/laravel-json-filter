<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JsonContainsMacro
{
    use JsonMacroTrait;

    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath, string $search)
    {
        $this->ensureSelectAll($builder);
        
        $driver = $builder->getConnection()->getDriverName();
        $column = $this->extractColumn($keyPath);
        $path   = $this->extractJsonPath($keyPath);

        switch ($driver) {
            case 'mysql':
                $expr = "JSON_UNQUOTE(JSON_EXTRACT($column, '$.$path'))";
                return $builder->whereRaw("$expr LIKE ?", ["%{$search}%"]);

            case 'pgsql':
                $expr = $this->pgJsonExpression($keyPath);
                return $builder->whereRaw("$expr LIKE ?", ["%{$search}%"]);

            default:
                return $builder->where($column, 'LIKE', "%{$search}%");
        }
    }
}
