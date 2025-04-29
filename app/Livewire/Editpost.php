<?php

namespace App\Livewire;

use Livewire\Component;

class Editpost extends Component
{
    public $post;
    public $title;
    public $body;

    public function mount () {
        $this->title = $this->post->title;
        $this->body = $this->post->body;
    }

    public function update () {

        $incomingRequest = $this->validate([
            'title' => 'required',
            'body' => 'required'
         ]);

         $incomingRequest['title'] = strip_tags($incomingRequest['title']);
         $incomingRequest['body'] = strip_tags($incomingRequest['body']);

         $this->authorize('update', $this->post);

         $this->post->update($incomingRequest);

         session()->flash('success', 'Your post has been updated');

         return $this->redirect("/post/{$this->post->id}/edit", navigate: true);



         return back()->with('success', 'Your post has been updated');
    }
    public function render()
    {
        return view('livewire.editpost');
    }
}
