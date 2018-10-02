<?php

namespace App\Http\Controllers;

use App\Listing;
use App\ScopedSearch;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class ContingentPropertiesController extends Controller
{
    public function index(Request $request)
    {
        $search = new ScopedSearch($request);
        $listings = $search->setScope('contingentOrPending')->get();

        return $listings;
    }
}
