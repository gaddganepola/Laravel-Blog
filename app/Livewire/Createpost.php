<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use App\Mail\NewPostEmail;
use App\Jobs\SendNewPostEmail;
use Illuminate\Support\Facades\Mail;

class Createpost extends Component
{
    public $title;
    public $body;

    public function create () {
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $incomingRequest = $this->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        //strip tags for security can not inject html
        $incomingRequest['title'] = strip_tags($incomingRequest['title']);
        $incomingRequest['body'] = strip_tags($incomingRequest['body']);
        $incomingRequest['user_id'] = auth()->id();

        $post = Post::create($incomingRequest);

        dispatch(new SendNewPostEmail(['sendTo' => auth()->user()->email, 'title' => $post->title, 'name' => auth()->user()->username]));

        // Mail::to(auth()->user()->email)->send(new NewPostEmail(['title' => $post->title, 'name' => auth()->user()->username]));

        session()->flash('success', 'Your post has been created');

        return $this->redirect("/post/{$post->id}", navigate: true);
    }
    
    public function render()
    {
        return view('livewire.createpost');
    }
}
