<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Http\Request;

class MapSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = new Search($request);

        return $search->noPaginate();
    }
}
