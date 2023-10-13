<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WellKnownController;
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

Route::get('/', function () {
    return view('welcome')->with(['public' => true]);
});

Route::get('.well-known/webfinger', [WellKnownController::class, 'webfinger']);
Route::get('.well-known/nodeinfo', [WellKnownController::class, 'nodeinfoBasic']);
Route::get('nodeinfo/2.1', [WellKnownController::class, 'nodeinfo']);

Route::get('timeline', [ProfileController::class, 'timeline']);

Route::post('@{username}', [PostController::class, 'createFromApi']);

Route::get('@{username}', [ProfileController::class, 'show']);
Route::get('@{username}/followers', [ProfileController::class, 'followers']);
Route::get('@{username}/following', [ProfileController::class, 'following']);
Route::get('@{username}/outbox', [ProfileController::class, 'outbox']);
Route::get('@{username}/collections/featured', [ProfileController::class, 'featured']);
Route::get('@{username}/{postUuid}', [ProfileController::class, 'post']);

Route::post('inbox', [InboxController::class, 'globalInbox']);
Route::post('@{username}/inbox', [InboxController::class, 'userInbox']);

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard']);

    Route::get('dashboard/add', [AdminController::class, 'showCreateProfile']);
    Route::post('dashboard/add', [AdminController::class, 'createProfile']);

    Route::get('dashboard/@{username}', [AdminController::class, 'showProfile']);
    Route::post('dashboard/@{username}', [AdminController::class, 'updateProfile']);

    Route::post('dashboard/@{username}/posts', [PostController::class, 'create']);

    Route::get('dashboard/@{username}/{postId}', [PostController::class, 'showEdit']);
    Route::post('dashboard/@{username}/{postId}', [PostController::class, 'edit']);
    Route::delete('dashboard/@{username}/{postId}', [PostController::class, 'delete']);
});

require __DIR__.'/auth.php';
