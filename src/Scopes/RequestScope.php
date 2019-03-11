<?php

namespace Submtd\LaravelApi\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
            $this->parseFilter($builder, $field, $filter);
        }
    }

    protected function parseFilter($builder, $field, $filter)
    {
        $filters = explode(',', $filter);
        $builder->where(function ($query) use ($field, $filters) {
            foreach ($filters as $filter) {
                $parsed = $this->parseOperator($filter);
                $query->orWhere($field, $parsed['operator'], $parsed['value']);
            }
        });
    }

    protected function parseOperator($filter)
    {
        if (!Str::contains($filter, '|')) {
            return [
                'operator' => '=',
                'value' => $filter,
            ];
        }
        $operator = Str::before($filter, '|');
        $value = Str::after($filter, '|');
        switch ($operator) {
            case 'lt':
                return [
                    'operator' => '<',
                    'value' => $value,
                ];
            case 'lte':
                return [
                    'operator' => '<=',
                    'value' => $value,
                ];
            case 'gt':
                return [
                    'operator' => '>',
                    'value' => $value,
                ];
            case 'gte':
                return [
                    'operator' => '>=',
                    'value' => $value,
                ];
            case 'ne':
                return [
                    'operator' => '<>',
                    'value' => $value,
                ];
            case 'bt':
                return [
                    'operator' => 'between',
                    'value' => [
                        Str::before($value, ';'),
                        Str::after($value, ';'),
                    ],
                ];
            case 'like':
                return [
                    'operator' => 'like',
                    'value' => '%' . $value . '%',
                ];
            default:
                return [
                    'operator' => '=',
                    'value' => $filter,
                ];
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
