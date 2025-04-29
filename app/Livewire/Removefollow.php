<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Follow;
use Livewire\Component;

class Removefollow extends Component
{
    public $username;

    public function unfollow () {
        if (!auth()->check()) { 
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('username', $this->username)->first();
        Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->delete();

        session()->flash('success', 'You have successfully unfollowed this user');
        return $this->redirect("/profile/{$this->username}", navigate: true);
    }
    public function render()
    {
        return view('livewire.removefollow');
    }
}
