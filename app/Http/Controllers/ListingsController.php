<?php

namespace App\Http\Controllers;

use App\Listing;
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

        return fractal($listing, new ListingTransformer);
    }
}
