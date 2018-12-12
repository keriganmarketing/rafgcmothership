<?php

namespace App\Http\Controllers;

use App\Listing;
use App\Jobs\LogListingClick;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class ListingsController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $listing = Listing::where('mls_acct', $id)->first();
        LogListingClick::dispatch($listing)->onQueue('stats');
        return fractal($listing, new ListingTransformer);
    }
}
