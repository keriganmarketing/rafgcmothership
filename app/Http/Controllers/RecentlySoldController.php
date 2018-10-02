<?php

namespace App\Http\Controllers;

use App\Listing;
use Carbon\Carbon;
use App\ScopedSearch;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class RecentlySoldController extends Controller
{
    public function index(Request $request)
    {
        $search = new ScopedSearch($request);
        $listings = $search->setScope('recentlySold', [$request->days])->get();

        return $listings;
    }
}
