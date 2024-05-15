<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {
        $post['body']= strip_tags(Str::markdown($post->body), '<p></p><ul></ul><ol></ol><em></em><br>');
        return view('single-post',['post'=>$post]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('create-post');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $incomingFields = $request->validate([
            'title'=>'required',
            'body'=>'required'
        ]);
        $incomingFields['title']= strip_tags($incomingFields['title']);
        $incomingFields['body']= strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id();

        $newPost= Post::create($incomingFields);
        return redirect("/post/{$newPost->id}")->with('success','New post successfully created .');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
       return view('edit-post', ['post'=> $post ]); //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (auth()->user()->can('destroy',$post)){
            
            return "you cannot delete this";
        }
        $post->delete();
        return redirect('/profile/'.auth()->user()->username)->with('delete');
    }
}
