<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class PagesController extends Controller
{

    public function root(Request $request)
    {
        $user = new User();
        return view('pages.root');
    }
}
