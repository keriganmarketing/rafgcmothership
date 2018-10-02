<?php

namespace App\Http\Controllers;

use App\Listing;
use App\ScopedSearch;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class WaterfrontPropertiesController extends Controller
{
    public function index(Request $request)
    {
        $search = new ScopedSearch($request);
        $listings = $search->setScope('waterfront')->get();

        return $listings;
    }
}
