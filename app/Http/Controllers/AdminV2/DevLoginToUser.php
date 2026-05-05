<?php

namespace App\Http\Controllers\AdminV2;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevLoginToUser extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function loginDev(Request $request, $id)
    {
        if (is_numeric($id))
            $user = User::findOrFail($id);
        else
            $user = User::where('email', $id)->firstOrFail();
        Auth::login($user);
        return \redirect('/ ');
    }


    public function getImagesDpi(Request $request)
    {
        $data = Image::with('stock')->whereHas('stock', function ($q) {
            $q->where('type', "large")
                ->where('dpi', 72);
        })->paginate(10);
        return view('index.devtest', compact('data'));
    }

}
