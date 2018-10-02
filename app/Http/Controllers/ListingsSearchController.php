<?php

namespace App\Http\Controllers;

use App\Search;
use App\Listing;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class ListingsSearchController extends Controller
{
    public function index(Request $request)
    {
        $search = new Search($request);

        return $search->forListings();
    }
}
