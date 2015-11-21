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
	 * ClassX
	 */
	Route::group( [ 'prefix' => 'classX' ], function () {
		Route::any( 'byId', 'ClassXController@getClassXById' );
	} );

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

	/**
	 * Get post
	 */
	Route::any( 'getPosts', 'PostController@getPosts' );

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
} );

/**
 * Test
 */
Route::get( 'test', 'TestController@test_helper' );