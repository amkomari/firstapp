<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;

class FollowController extends Controller
{
    public function createFollow(User $user)
    {

        ///you cannot follow yourself
        if ($user->id == auth()->user()->id) {
            return back()->with('failure', 'You cannot follow yourself.');
        }

        ///you cannot follow someone you're already folloing
        $existCheck = Follow::where([['user_id', '=', auth()->user()], ['followeduser', $user->id]])->count();

        if ($existCheck) {
            return back()->with('faliure', 'You are already following that User');
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();

        return back()->with('success', 'User successfilly followed.');
    }

    public function removeFollow(User $user)
    {
        Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]]);
        return back()->with('success', 'User successfilly unfollowed.');
    }
}
