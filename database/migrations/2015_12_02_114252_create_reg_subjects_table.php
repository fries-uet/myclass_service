<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegSubjectsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'reg_subjects', function ( Blueprint $table ) {
			$table->increments( 'id' );

			$table->string( 'name' );
			$table->string( 'msv' );
			$table->string( 'pass' );

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 'reg_subjects' );
	}
}
