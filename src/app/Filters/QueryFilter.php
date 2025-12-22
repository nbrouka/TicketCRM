<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QueryFilter
{
    protected $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request ?: request();
    }

    public function apply($query)
    {
        $filters = $this->getFilters();

        foreach ($filters as $filter => $value) {
            if (method_exists($this, $this->filterToMethod($filter)) && ! empty($value)) {
                $method = $this->filterToMethod($filter);
                $this->$method($query, $value);
            }
        }

        return $query;
    }

    protected function filterToMethod($filter)
    {
        return Str::camel($filter);
    }

    protected function getFilters()
    {
        return $this->request->all();
    }
}
