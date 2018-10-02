<?php

namespace App\Http\Controllers;

use App\Listing;
use App\ScopedSearch;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class NewListingsController extends Controller
{
    public function index(Request $request)
    {
        $search   = new ScopedSearch($request);
        $days     = $request->days ?? 10;
        $listings = $search->setScope('newListings', [$request->days])->get();

        return $listings;
    }
}
