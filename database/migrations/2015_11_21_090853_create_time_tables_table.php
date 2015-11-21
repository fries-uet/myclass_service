<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeTablesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'time_tables', function ( Blueprint $table ) {
			$table->increments( 'id' );

			$table->integer( 'user' );
			$table->integer( 'subClass' );
			$table->boolean( 'isTD' )->default( 0 );

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 'time_tables' );
	}
}
