<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //
    public function viewSinglePost (Post $post) {

        //markdown for structure the body content 
        $ourHTML = strip_tags(Str::markdown($post->body), '<p><br><em><strong><h1><h2><h3><h4><h5><h6><ul><li><ol>');
        $post['body'] = $ourHTML;
        return view('view-single-post', ['post' => $post]);
    }
    public function storeNewPost (request $request) {
        $incomingRequest = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        //strip tags for security can not inject html
        $incomingRequest['title'] = strip_tags($incomingRequest['title']);
        $incomingRequest['body'] = strip_tags($incomingRequest['body']);
        $incomingRequest['user_id'] = auth()->id();

        $post = Post::create($incomingRequest);
        return redirect("/post/{$post->id}")->with('success', 'Your post has been created');
    }
    public function showCreateForm () {
        return view('create-post');
    }
}
