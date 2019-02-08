<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;

class AgentListingsController extends Controller
{
    public function index(Request $request, $agent)
    {
        return Listing::forAgent($agent, $request);
    }

    public function sold(Request $request, $agent)
    {
        return Listing::forAgentSold($agent, $request);
    }
}
