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

Route::get( '/', function () {
	return view( 'welcome' );
} );

Route::group( [ 'prefix' => 'v1' ], function () {
	/**
	 * Get group by base
	 */
	Route::any( 'getGroup', 'ClassXController@getGroup' );

	/**
	 * Post
	 */
	Route::group( [ 'prefix' => 'post' ], function () {
		Route::any( '', 'PostController@post' );

		/**
		 * Post comment
		 */
		Route::any( 'comment', 'CommentController@comment' );
	} );

	Route::any( 'timetable', 'TimetableController@getTimetable' );

	/**
	 * Get post
	 */
	Route::any( 'getPosts', 'PostController@getPosts' );

	/**
	 * Like
	 */
	Route::any( 'like', 'LikeConfirmedController@like' );

	/**
	 * Vote
	 */
	Route::any( 'like', 'LikeConfirmedController@vote' );

	/**
	 * Login
	 */
	Route::any( 'login', 'UserController@login' );

	/**
	 * Register
	 */
	Route::any( 'register', 'UserController@register' );

	/**
	 * Update
	 */
	Route::any( 'update', 'UserController@update' );
} );

/**
 * Seed databases
 */
Route::group( [ 'prefix' => 'seed' ], function () {
	Route::get( 'classX', 'SeedDataController@seedDataClassX_es' );
	Route::get( 'time', 'SeedDataController@seedTimetable' );//1
	Route::get( 'subject', 'SeedDataController@seedSubject' );//2
	Route::get( 'classSubject', 'SeedDataController@seedClassSubject' );//3
	Route::get( 'teacher', 'SeedDataController@createTeacherUser' );//4
	Route::get( 'subClassSubject',
		'SeedDataController@seedSubClassSubject' );//5
} );

/**
 * Test
 */
Route::get( 'test', 'TestController@test_helper' );