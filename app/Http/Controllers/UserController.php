<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserController extends Controller
{
    use AuthenticatesUsers;
    /**
     * Display a listing of the resource.
     */
    public function profile(User $profile)
    {
        $currentlyFollowing = 0;

        if (auth()->check()) {
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $profile->id]])->count();
        }

        return view('profile-posts', ['currentlyFollowing' => $currentlyFollowing, 'avatar' => $profile->avatar, 'username' => $profile->username, 'posts' => $profile->posts()->latest()->get(), 'postCount' => $profile->posts()->count()]);
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
        User: $user = auth()->user();
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
        return back()->with('success', 'Avatar Updated');

    }
}