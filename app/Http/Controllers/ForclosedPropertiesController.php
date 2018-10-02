<?php

namespace App\Http\Controllers;

use App\Listing;
use App\ScopedSearch;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class ForclosedPropertiesController extends Controller
{
    public function index(Request $request)
    {
        $search = new ScopedSearch($request);
        $listings = $search->setScope('forclosures')->get();

        return $listings;
    }
}
