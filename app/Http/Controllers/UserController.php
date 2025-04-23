<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    //
    public function storeAvatar (Request $request) {
        $request->validate([
           'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048' 
        ]);

        $user = auth()->user();

        $fileName = $user->id . "-" . uniqid() . ".jpg";

        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file("avatar"));
        $imageData = $image->cover(120, 120)->toJpeg();

        Storage::disk('public')->put('avatars/' . $fileName, $imageData);

        $oldAvatar = $user->avatar;

        // $request->file('avatar')->store('avatars', 'public');

        $user->avatar = $fileName;
        $user->save();

        if ($oldAvatar != null) {
            Storage::disk('public')->delete(str_replace("/storage/", "", $oldAvatar));
        }

        return back()->with('success', 'Your avatar has been updated');
    }
    public function showAvatarForm (){
        return view('avatar-form');
    }
    public function profile (User $user) {
        $posts = $user->posts()->latest()->get();
        return view('profile-posts', [
            'user' => $user, 'posts' => $posts]);
    }
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
