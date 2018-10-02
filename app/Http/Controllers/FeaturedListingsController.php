<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Transformers\ListingTransformer;

class FeaturedListingsController extends Controller
{
    public function index(Request $request)
    {
        return Listing::featuredList(explode('|', $request->mlsNumbers));
    }
}
