<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Events\OurExampleEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
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

    private function getShareddata ($user) {
        $currentFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();

        View::share('sharedData', [
            'username' => $user->username, 'avatar' => $user->avatar, 'currentFollowing' => $currentFollowing, 'postCount' => $user->posts()->count(), 'followerCount' => $user->followers()->count(), 'followingCount' => $user->following()->count()]);
    }
    public function profile (User $user) {
        $this->getShareddata($user);
        $posts = $user->posts()->latest()->get();
        return view('profile-posts', [
            'user' => $user, 'posts' => $posts]);
    }

    public function profileFollowers (User $user) {
        $this->getShareddata($user);
        $followers = $user->followers()->get();
        return view('profile-followers', [
            'followers' => $followers]);
    }

    public function profileFollowing (User $user) {
        $this->getShareddata($user);
        $following = $user->following()->get();
        return view('profile-following', ['following' => $following]);
    }
    public function logout () {
        event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'logout']));
        auth()->logout();
        return redirect('/')->with('success', 'You have successfully logged out');
    }
    public function showCorrectHomepage () { 
        if (auth()->check()) {
            return view('homepage-feed', ['posts' => auth()->user()->feedPosts()->latest()->paginate(5)]);
        }else {
            $postCount = Cache::remember('postCount', 30, function () {
            //    sleep(5);
               return Post::count(); 
            });
            return view('homepage', ['postCount' => $postCount]);
        }
    }

    public function loginApi (Request $request) {
        $incomingRequest = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (auth()->attempt($incomingRequest)) {
            $user = User::where('username', $incomingRequest['username'])->first();
            $token = $user->createToken('ourapptoken')->plainTextToken;
            return $token;
        }
        
        return 'sorry';

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
            event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'login']));
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
