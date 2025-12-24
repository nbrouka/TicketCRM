<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QueryFilter
{
    /** @var Request|array<string, mixed>|null */
    protected Request|array|null $request = null;

    /**
     * @param  Request|array<string, mixed>|object|null  $request
     */
    public function __construct(mixed $request = null)
    {
        if ($request === null) {
            $this->request = request();

            return;
        }

        if ($request instanceof Request) {
            $this->request = $request;

            return;
        }

        if (is_array($request)) {
            $this->request = new Request($request);

            return;
        }

        $this->request = new Request((array) $request);
    }

    public function apply(mixed $query): mixed
    {
        $filters = $this->getFilters();

        foreach ($filters as $filter => $value) {
            if (method_exists($this, $this->filterToMethod($filter)) && ! empty($value)) {
                $method = $this->filterToMethod($filter);
                $query = $this->$method($query, $value);
            }
        }

        return $query;
    }

    protected function filterToMethod(string $filter): string
    {
        return Str::camel($filter);
    }

    /** @return array<string, mixed> */
    protected function getFilters(): array
    {
        if ($this->request === null) {
            return [];
        }

        if ($this->request instanceof Request) {
            return $this->request->all();
        }

        return (array) $this->request;
    }
}
