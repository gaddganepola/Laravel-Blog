<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Mail\NewPostEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Can;

class PostController extends Controller
{
    //
    public function search ($term) {
        $posts = Post::search($term)->get();
        $posts->load('user:id,username,avatar');
        return $posts;
    }
    public function actuallyUpdate (Request $request, Post $post) {
         $incomingRequest = $request->validate([
            'title' => 'required',
            'body' => 'required'
         ]);

         $incomingRequest['title'] = strip_tags($incomingRequest['title']);
         $incomingRequest['body'] = strip_tags($incomingRequest['body']);

         $post->update($incomingRequest);

         return back()->with('success', 'Your post has been updated');
    }
    public function showEditForm (Post $post) {
        return view('edit-post', ['post' => $post]);
    }

    public function deleteApi (Post $post) {
        // if (auth()->user()->cannot('delete', $post)) {
        //     return redirect('/profile/' . auth()->user()->username)->with('error', 'Your are not able to delete this post');
        // }
        $post->delete();
        return 'deleted';
    }
    public function delete (Post $post) {
        // if (auth()->user()->cannot('delete', $post)) {
        //     return redirect('/profile/' . auth()->user()->username)->with('error', 'Your are not able to delete this post');
        // }
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'Your post has been deleted');
    }
    public function viewSinglePost (Post $post) {

        //markdown for structure the body content 
        $ourHTML = strip_tags(Str::markdown($post->body), '<p><br><em><strong><h1><h2><h3><h4><h5><h6><ul><li><ol>');
        $post['body'] = $ourHTML;
        return view('view-single-post', ['post' => $post]);
    }

    public function storeNewPostApi (request $request) {
        $incomingRequest = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        //strip tags for security can not inject html
        $incomingRequest['title'] = strip_tags($incomingRequest['title']);
        $incomingRequest['body'] = strip_tags($incomingRequest['body']);
        $incomingRequest['user_id'] = auth()->id();

        $post = Post::create($incomingRequest);

        // Mail::to('test@gmail.com')->send(new NewPostEmail());
        return $post->id;
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

        Mail::to('test@gmail.com')->send(new NewPostEmail());
        return redirect("/post/{$post->id}")->with('success', 'Your post has been created');
    }
    public function showCreateForm () {
        return view('create-post');
    }
}
