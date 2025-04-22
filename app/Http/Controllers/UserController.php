<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function logout () {
        auth()->logout();
        return redirect('/')->with('success', 'You have successfully logged out');
    }
    public function showCorrectHomepage () { 
        if (auth()->check()) {
            return view('homepage-feed');
        }else {
            return view('homepage');
        }
    }
    public function login (Request $request) {
        $incomingRequest = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt([
            'username' => $incomingRequest['loginusername'],
            'password' => $incomingRequest['loginpassword']
        ])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You have successfully logged in');
        }else {
            return redirect('/')->with('error', 'Invalid Credentials');
        }
    }
    public function register (Request $request) {
        $request = $request->validate([
            'username' => 'required|min:3|max:10|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed'

        ]);

        $user = User::create($request);
        auth()->login($user);
        return redirect('/')->with('success', 'You have successfully registered');
    }
}
