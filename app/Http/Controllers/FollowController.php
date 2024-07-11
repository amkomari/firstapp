<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\Session;

class FollowController extends Controller
{
    public function createFollow(User $user)
    {

        ///you cannot follow yourself
        if ($user->id == auth()->user()->id) {
            Session::flash('follow', 'You cannot follow yourself.');
            return back();
        }

        ///you cannot follow someone you're already folloing
        $existCheck = Follow::where([['user_id', '=', auth()->user()], ['followeduser', $user->id]])->count();

        if ($existCheck) {
            Session::flash('follow', 'You are already following that User');
            return back();
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();
        Session::flash('follow', 'User successfilly followed.');


        return back();
    }

    public function removeFollow(User $user)
    {
        Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->delete();
        Session::flash('follow', 'User successfilly unfollowed.');

        return back();
    }
}
