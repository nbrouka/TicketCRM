<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QueryFilter
{
    protected $request;

    public function __construct($request = null)
    {
        if ($request instanceof Request) {
            $this->request = $request;
        } elseif (is_array($request) || is_object($request)) {
            $this->request = new Request((array) $request);
        } else {
            $this->request = request();
        }
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
        if ($this->request instanceof Request) {
            return $this->request->all();
        }

        return (array) $this->request;
    }
}
