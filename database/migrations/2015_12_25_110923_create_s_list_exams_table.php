<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSListExamsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 's_list_exams', function ( Blueprint $table ) {
			$table->increments( 'id' );

			$table->integer( 'user_id' );
			$table->integer( 'subject' );

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 's_list_exams' );
	}
}
