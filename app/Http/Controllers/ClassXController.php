<?php

namespace App\Http\Controllers;

use App\ClassX;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use stdClass;

class ClassXController extends Controller {
	public function getGroup( Request $request ) {
		onlyAllowPostRequest( $request );

		$id_user = intval( $request->input( 'id' ) );
		$base    = $request->input( 'base' );

		/**
		 * Dữ liệu trả về
		 */
		$response = new stdClass();

		$users = User::all()->where( 'id', $id_user );
		if ( $users->count() == 0 ) {//
			$response->error     = true;
			$response->error_msg = 'Không tồn tại người dùng này!';

			return response()->json( $response );
		}

		$user = $users->first();
		if ( $base == 'class_xes' ) {
			$id_class = $user->class;
			$classX   = ClassX::all()->where( 'id', $id_class )->first();
		}

		$response->error = false;
		$class_x         = new stdClass();
		$class_x->id     = $classX->id;
		$class_x->base   = $base;
		$class_x->name   = $classX->name;
		$class_x->soSV   = ClassX::getCountStudentByClassId( $id_class );
		$response->group = $class_x;

		return response()->json( $response );
	}
}
