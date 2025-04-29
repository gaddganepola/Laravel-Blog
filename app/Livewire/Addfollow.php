<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Follow;
use Livewire\Component;

class Addfollow extends Component
{

    public $username;
    
    public function follow () {
        if (!auth()->check()) { 
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('username', $this->username)->first();

        //can not follow yourself
        if ($user->id == auth()->user()->id) {
            return back()->with('error', 'You can not follow yourself');
        }
        //can not follow someone already follow
        $existCheck = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();

        if ($existCheck) {
            return back()->with('error', 'You are already following this user');
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();

        session()->flash('success', 'User successfully followed');

        return $this->redirect("/profile/{$this->username}", navigate: true);

    }
    public function render()
    {
        return view('livewire.addfollow');
    }
}
