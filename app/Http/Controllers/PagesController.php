<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class PagesController extends Controller
{
    public function root(Request $request)
    {
        return view('pages.root');
    }
}
