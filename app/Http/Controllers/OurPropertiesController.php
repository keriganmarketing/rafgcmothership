<?php

namespace App\Http\Controllers;

use App\Listing;
use App\ScopedSearch;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class OurPropertiesController extends Controller
{
    public function index(Request $request, $officeCode)
    {
        $search = new ScopedSearch($request);
        $listings = $search->setScope('by', [$officeCode])->get();

        return $listings;
    }
}
