<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::group(
    ['prefix' => 'v1'], function () {
    /**
     * Get group by base
     */
    Route::any('getGroup', 'ClassXController@getGroup');

    /**
     * Post
     */
    Route::group(['prefix' => 'post'], function () {
        Route::any('', 'PostController@post');

        /**
         * Post comment
         */
        Route::any('comment', 'CommentController@comment');
    });

    Route::any('timetable', 'TimetableController@getTimetable');

    /**
     * Get post
     */
    Route::any('getPosts', 'PostController@getPosts');

    /**
     * Like
     */
    Route::any('like', 'LikeConfirmedController@like');

    /**
     * Vote
     */
    Route::any('vote', 'LikeConfirmedController@vote');

    /**
     * Login
     */
    Route::any('login', 'UserController@login');

    /**
     * Login token
     */
    Route::any('login_token', 'UserController@login_token');

    /**
     * Register
     */
    Route::any('register', 'UserController@register');

    /**
     * Update
     */
    Route::any('update', 'UserController@update');

    /**
     * Update avartar
     */
    Route::any('updateAvatar', 'UserController@updateAvatar');

    Route::get('avatar/{msv}', [
        'as' => 'getAvatar',
        'uses' => 'UserController@getAvatar'
    ]);

    Route::get('activate/{mail}&{activate_code}', [
        'as' => 'activate_code',
        'uses' => 'UserController@activate_code'
    ]);

    /**
     * New feed
     */
    Route::post('feed', [
        'as' => 'feed',
        'uses' => 'UserController@feed'
    ]);
});

/**
 * Seed databases
 */
Route::group(
    ['prefix' => 'seed'], function () {
    Route::get('classX', 'SeedDataController@seedDataClassX_es');
    Route::get('time', 'SeedDataController@seedTimetable');//1
    Route::get('subject', 'SeedDataController@seedSubject');//2
    Route::get('classSubject', 'SeedDataController@seedClassSubject');//3
    Route::get('teacher', 'SeedDataController@createTeacherUser');//4
    Route::get('subClassSubject', 'SeedDataController@seedSubClassSubject');//5
}
);

/**
 * Test
 */
Route::get('test', 'TestController@test_helper');

Route::group(
    ['prefix' => ''], function () {
    Route::get('test', 'ScoreUET@updateScoreUET');

    Route::any('/', [
        'as' => 'subscribe',
        'uses' => 'ScoreUET@registerSubscriber'
    ]);

    Route::any('results', [
        'as' => 'results',
        'uses' => 'ScoreUET@results'
    ]);

    Route::get('success', [
        'as' => 'subscribe.success',
        'uses' => 'ScoreUET@success'
    ]);

    Route::any('confirm', [
        'as' => 'confirm',
        'uses' => 'ScoreUET@confirm'
    ]);

    Route::get('reconfirm', [
        'as' => 'reconfirm',
        'uses' => 'ScoreUET@reconfirm_front'
    ]);

    Route::post('reconfirm', [
        'as' => 'reconfirm',
        'uses' => 'ScoreUET@reconfirm'
    ]);

    Route::any('getInfo', [
        'as' => 'getInfo',
        'uses' => 'ScoreUET@getInfoStudent',
    ]);

//    Route::get('email___________', 'ScoreUET@email');

    Route::get('crawl', 'ScoreUET@crawl');

    /**
     * Updated and send mail
     */
    Route::any('update', 'ScoreUET@update');
});

Route::group(['prefix' => 'gcm'], function () {
    Route::any('register', 'TestController@register');

    Route::any('send', 'TestController@send_push_notification');

});

Route::any('base64', 'TestController@imageBase64');