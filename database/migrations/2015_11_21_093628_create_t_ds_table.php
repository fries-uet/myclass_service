<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTDsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 't_ds', function ( Blueprint $table ) {
			$table->increments( 'id' );

			$table->string( 'name' );
			$table->string( 'maLMH' );
			$table->integer( 'viTri' );
			$table->integer( 'soTiet' );
			$table->integer( 'address' );

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 't_ds' );
	}
}
