<?php

namespace Pawsmedz\JsonFilter\Builder\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JsonSelectMacro
{
    use JsonMacroTrait;

    public function __invoke(EloquentBuilder|QueryBuilder $builder, string $keyPathWithAlias)
    {
        $this->ensureSelectAll($builder);
        
        [$keyPath, $alias] = array_pad(explode(' as ', $keyPathWithAlias), 2, null);
        $alias = $alias ?? str_replace(['->', '.'], '_', $keyPath);

        $driver = $builder->getConnection()->getDriverName();
        $column = $this->extractColumn($keyPath);
        $path   = $this->extractJsonPath($keyPath);

        switch ($driver) {
            case 'mysql':
                $expr = "JSON_UNQUOTE(JSON_EXTRACT($column, '$.$path')) as $alias";
                break;
            case 'pgsql':
                $expr = "{$this->pgJsonExpression($keyPath)} as $alias";
                break;
            default:
                $expr = "$column as $alias";
        }

        return $builder->selectRaw($expr);
    }
}
