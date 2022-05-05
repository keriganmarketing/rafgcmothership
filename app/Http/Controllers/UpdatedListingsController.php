<?php

namespace App\Http\Controllers;

use App\Listing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class UpdatedListingsController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->toDateString();
        $listings = Listing::whereDate('date_modified', $today)->get();

        return fractal($listings, new ListingTransformer);
    }
}
