<?php

namespace App\Http\Controllers\Contributor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('contributor.home');
    }
}
