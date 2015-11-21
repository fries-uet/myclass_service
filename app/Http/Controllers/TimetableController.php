<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use stdClass;

class TimetableController extends Controller {
	public function importTimetable( $id ) {
		$users = User::all()->where( 'id', intval( $id ) );
		if ( $users->count() == 0 ) {
			return false;
		}
		$user = $users->first();
		$msv  = $user->msv;

		$response = getTimeTableUET( $msv );

		/**
		 * Dữ liệu trả về
		 */

		if ( $response == false ) {//Không tồn tại MSV
			return false;
		}



		$name = $response['name'];
		$qh   = $response['qh'];

		$user_id = $user->id;

		$timetable = $response['timetable'];
	}
}
