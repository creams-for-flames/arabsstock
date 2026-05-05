<?php

namespace App\Http\Controllers\AdminV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PinterestController extends Controller
{
    public function callback(Request $request)
    {
        return response()->json([
            'data'=> $request->all()
        ]);
    }
}
