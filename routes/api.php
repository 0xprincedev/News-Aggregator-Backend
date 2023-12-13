<?php


use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\ArticleController;
use Illuminate\Support\Facades\Route;

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [RegisterController::class, 'register']);

Route::get('article/search', [ArticleController::class, 'index'])->middleware('optionalauth');
Route::post('feeds', [ArticleController::class, 'getFeeds'])->middleware('optionalauth');

Route::group(['middleware' => 'api.auth'], function () {
    Route::get('user', [LoginController::class, 'details']);
    Route::get('logout', [LoginController::class, 'logout']);

    // Route::get('article/search', [ArticleController::class, 'index']);
    // Route::post('feeds', [ArticleController::class, 'getFeeds']);
    Route::post('article/set_like', [ArticleController::class, 'setLike']);
    Route::get('article/get_like', [ArticleController::class, 'getLike']);
});
