<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something g7777777777777777777777777reat!
|
*/

Route::get('/', 'MicropostsController@index');  // 上書き

// ユーザ登録
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup.get');
Route::post('signup', 'Auth\RegisterController@register')->name('signup.post');

// 認証
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.post');
Route::get('logout', 'Auth\LoginController@logout')->name('logout.get');

//authでログイン中のみ以下操作を可能とする。
Route::group(['middleware' => ['auth']], function () {
    //prefixを使用することでURIの始めに接頭語として加えることができる。
    //例：'follow'　は　/users/{id}/follow　となって、記述を省略することができる。
    Route::group(['prefix' => 'users/{id}'], function () {
        Route::post('follow', 'UserFollowController@store')->name('user.follow');
        Route::delete('unfollow', 'UserFollowController@destroy')->name('user.unfollow');
        Route::get('followings', 'UsersController@followings')->name('users.followings');
        Route::get('followers', 'UsersController@followers')->name('users.followers');
        Route::get('favorites', 'UsersController@favorites')->name('users.favorites');    // 追加
    });
    
    
    Route::resource('users', 'UsersController', ['only' => ['index', 'show']]);
    
    // 追加
    Route::group(['prefix' => 'microposts/{id}'], function () {
        Route::post('favorite', 'FavoritesController@store')->name('favorites.favorite');
        Route::delete('unfavorite', 'FavoritesController@destroy')->name('favorites.unfavorite');
    });
    
    Route::resource('microposts', 'MicropostsController', ['only' => ['store', 'destroy']]);
    /*認証を必要とするルーティンググループ内に、 Micropostsのルーティングを設定します（登録のstoreと削除のdestroyのみ）。
    これで、認証済みのユーザだけがこれらのアクションにアクセスできます。*/
});

