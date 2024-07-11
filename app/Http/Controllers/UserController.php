<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class UserController extends Controller
{
    use AuthenticatesUsers;
    /**
     * Display a listing of the resource.
     */

    private function getSharedData(User $profile){


        $currentlyFollowing = 0;

        if (auth()->check()) {
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $profile->id]])->count();
        }

        View::share('sharedData',['currentlyFollowing' => $currentlyFollowing, 'avatar' => $profile->avatar, 'username' => $profile->username, 'postCount' => $profile->posts()->count()]);
    }

    public function profile(User $profile)
    {
        $this->getSharedData($profile);
        return view('profile-posts', ['posts' => $profile->posts()->latest()->get()]);

    }

    public function profileFollowers(User $profile)
    {
        $this->getSharedData($profile);
        return view('profile-followers', ['posts' => $profile->posts()->latest()->get()]);
    }

    public function profileFollowing(User $profile)
    {
        $this->getSharedData($profile);
        return view('profile-following', ['posts' => $profile->posts()->latest()->get()]);
    }


    public function index(LoginRequest $request)
    {
        $username = $request->loginusername;
        $user = User::where('email', $username)->orWhere('username', $username)->first();

        $credentials = [
            'email' => $user->email ?? '',
            'password' => $request->loginpassword,
        ];

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return view('homepage-feed');
        } else {
            return redirect('/');
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
        User::create($incomingFields);
        // dd(auth()->user());
        Session::flash('user', 'Crated successfully');


        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
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
    public function destroy(User $user)
    {
        //
    }

    public function showAvatarForm()
    {
        return view('avatar-form');
    }

    public function storeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:3000',
        ]);
        $user = auth()->user();
        $filename = $user->id . "_" . uniqid() . ".jpg";

        $manager = new ImageManager(new Driver());

        $image = $manager->read($request->file("avatar"));
        $imgData = $image->cover(120, 120)->toJpeg();

        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != "/fallback-avatar.jpg") {
            Storage::delete(str_replace("/storage/", "public", $oldAvatar));
        }
        Session::flash('user', 'Avatar Updated');

        return back();

    }
}
