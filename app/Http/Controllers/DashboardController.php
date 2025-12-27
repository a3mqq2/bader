<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function sections()
    {
        return view('sections');
    }
}
