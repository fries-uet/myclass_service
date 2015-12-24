<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSUsersTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 's_users', function ( Blueprint $table ) {
			$table->increments( 'id' );

			$table->string( 'email' );
			$table->string( 'msv' );
			$table->boolean( 'is_active' );

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 's_users' );
	}
}
