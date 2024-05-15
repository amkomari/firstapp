<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get('/admins-only', function () {
    return 'Only Admins page GATE';
})->middleware('can:visitAdminPages');

/////USERS
Route::get('/', [LoginController::class, "index"]);

Route::post('/logout', [UserController::class, "logout"]);
Route::post('/register', [UserController::class, "store"]);
Route::post('/login', [UserController::class, "index"])->name('login');

Route::get('/manage-avatar', [UserController::class, "showAvatarForm"]);
Route::post('/manage-avatar', [UserController::class, "storeAvatar"]);

///// Follow
Route::get('/create-follow/{user:username}', [FollowController::class, 'createFollow']);
Route::get('/remove-follow/{user:username}', [FollowController::class, 'removeFollow']);

//////POSTS

Route::get('/create-post', [PostController::class, "create"])->middleware('auth');
Route::post('/create-post', [PostController::class, "store"])->middleware('auth');
Route::get('/post/{post}', [PostController::class, "index"])->middleware('auth');
Route::post('/post/delete/{post}', [PostController::class, "destroy"])->middleware('can:delete,post');
Route::get('/post/{post}/edit', [PostController::class, 'show'])->middleware('can:update,post');
Route::put('/post/{post}/edit', [PostController::class, 'update'])->middleware('can:update,post');
/////Profile Routs

Route::get('/profile/{profile:username}', [UserController::class, 'profile'])->middleware('auth');
