<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function register (Request $request) {
        $request = $request->validate([
            'username' => 'required',
            'email' => 'required',
            'password' => 'required'

        ]);

        User::create($request);
        return "From Register Function";
    }
}
