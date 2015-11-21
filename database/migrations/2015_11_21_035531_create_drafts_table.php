<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDraftsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'drafts', function ( Blueprint $table ) {
			$table->increments( 'id' );

			$table->string( 'maMH' );
			$table->string( 'tenMH' );
			$table->string( 'soTin' );
			$table->string( 'maLMH' );
			$table->string( 'teacher' );
			$table->string( 'soSV' );
			$table->string( 'thu' );
			$table->string( 'tiet' );
			$table->string( 'address' );
			$table->string( 'note' );

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 'drafts' );
	}
}
