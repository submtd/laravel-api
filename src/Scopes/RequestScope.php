<?php

namespace Submtd\LaravelApi\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RequestScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $this->parseFilters($builder);
        $this->parseIncludes($builder);
        $this->parseSorts($builder);
    }

    protected function parseFilters($builder)
    {
        if (!$filters = request()->get('filter')) {
            return;
        }
        if (!is_array($filters)) {
            return;
        }
        foreach ($filters as $field => $filter) {
            if (strpos($filter, '|')) {
                $operator = $this->parseOperator(substr($filter, 0, strpos($filter, '|')));
                $builder->where($field, $operator, substr($filter, strpos($filter, '|') + 1));
            } elseif (strpos($filter, ',')) {
                $values = explode(',', $filter);
                $builder->whereIn($field, $values);
            } else {
                $builder->where($field, $filter);
            }
        }
    }

    protected function parseOperator($operator)
    {
        switch ($operator) {
            case 'lt':
                return '<';
                break;
            case 'lte':
                return '<=';
                break;
            case 'gt':
                return '>';
                break;
            case 'gte':
                return '>=';
                break;
            case 'ne':
                return '<>';
                break;
            case 'bt':
                return 'between';
                break;
            case 'like':
                return 'like';
                break;
            default:
                return '=';
                break;
        }
    }

    protected function parseIncludes($builder)
    {
        $includes = request()->get('include');
        foreach (explode(',', $includes) as $include) {
            $builder->with($include);
        }
    }

    protected function parseSorts($builder)
    {
        if (!$sorts = request()->get('sort')) {
            return;
        }
        $sorts = explode(',', $sorts);
        foreach ($sorts as $sort) {
            if ($sort[0] == '-') {
                $builder->orderBy(ltrim($sort, '-'), 'desc');
            } else {
                $builder->orderBy($sort);
            }
        }
    }
}
