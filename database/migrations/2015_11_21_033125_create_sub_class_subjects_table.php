<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubClassSubjectsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'sub_class_subjects', function ( Blueprint $table ) {
			$table->increments( 'id' );

			$table->integer( 'teacher' );
			$table->string( 'address' );
			$table->integer( 'viTri' );
			$table->integer( 'soTiet' );
			$table->integer( 'soSV' );
			$table->integer( 'nhom' );
			$table->integer( 'classSubject' );

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 'sub_class_subjects' );
	}
}
