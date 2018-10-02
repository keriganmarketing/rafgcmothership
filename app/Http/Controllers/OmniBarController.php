<?php

namespace App\Http\Controllers;

use App\OmniTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OmniBarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        return DB::table('omni_terms')->select('id', 'name', 'value')->where('value', 'like', "%{$search}%")->groupBy('value')->get();
    }
}
