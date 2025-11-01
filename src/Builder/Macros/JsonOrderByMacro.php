<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JsonOrderByMacro
{
    use JsonMacroTrait;

    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPath, string $direction = 'asc')
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
                $expr = $column; // fallback
        }

        return $builder->orderByRaw("$expr " . strtoupper($direction));
    }
}
