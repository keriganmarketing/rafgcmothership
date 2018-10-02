<?php

namespace App\Http\Controllers;

use App\ScopedSearch;
use Illuminate\Http\Request;

class OurSoldController extends Controller
{
    public function index(Request $request, $officeCode)
    {
        $search = new ScopedSearch($request);
        $listings = $search->setScope('recentlySoldBy', [$officeCode])->get();

        return $listings;
    }
}
