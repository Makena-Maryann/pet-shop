<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function parseQuery(Builder $query, Request $request): LengthAwarePaginator
    {
        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;
        $sort = $request->sortBy ?? 'id';
        $desc = $request->desc ?? false;

        $query = $query->orderBy($sort, $desc ? 'desc' : 'asc');

        $query = $query->paginate($limit, ['*'], 'page', $page);

        return $query;
    }
}
