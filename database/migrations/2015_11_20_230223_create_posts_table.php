<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'posts', function ( Blueprint $table ) {
			$table->increments( 'id' );

			$table->string( 'title' );
			$table->longText( 'content' );
			$table->integer( 'group' );//Nằm trong lớp nào
			$table->integer( 'author' );
			$table->integer( 'like' )->default( 0 );
			$table->boolean( 'isIncognito' )->default( 0 );
			$table->string( 'type' )->default( 'post' );
			$table->string( 'base' );//Nằm trong bảng nào?

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 'posts' );
	}
}
