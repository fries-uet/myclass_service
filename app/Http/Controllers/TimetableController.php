<?php

namespace App\Http\Controllers;

use App\ClassSubject;
use App\SubClassSubject;
use App\Subject;
use App\TimeTable;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use stdClass;

class TimetableController extends Controller {
	public function getTimetable( Request $request ) {
		$user_id = $request->input( 'id' );

		/**
		 * Dữ liệu trả về
		 */
		$response  = new stdClass();
		$timetable = TimeTable::all()->where( 'user', intval( $user_id ) );
		if ( $timetable->count() == 0 ) {
			$response->error     = true;
			$response->error_msg = 'Bạn chưa đồng bộ thời khóa biểu!';

			return response()->json( $response );
		}

		$arr_items = [ ];
		foreach ( $timetable as $s ) {
			$s_id            = $s->subClass;
			$subClassSubject = SubClassSubject::all()
			                                  ->where( 'id', intval( $s_id ) )
			                                  ->first();

			$class_id     = $subClassSubject->classSubject;
			$classSubject = ClassSubject::all()
			                            ->where( 'id', intval( $class_id ) )
			                            ->first();

			$subject_id = $classSubject->subject;

			$subject = Subject::all()->where( 'id', intval( $subject_id ) )
			                  ->first();

			$item_s          = new stdClass();
			$item_s->maMH    = $subject->maMH;
			$item_s->maLMH   = $classSubject->maLMH;
			$item_s->name    = $subject->name;
			$item_s->soTin   = $subject->soTin;
			$item_s->viTri   = $subClassSubject->viTri;
			$item_s->soTiet  = $subClassSubject->soTiet;
			$item_s->soSV    = $subClassSubject->soSV;
			$item_s->nhom    = $subClassSubject->nhom;
			$item_s->teacher = User::getInfoById( $subClassSubject->teacher );

			$arr_items[] = $item_s;
		}

		$response->error     = false;
		$response->timetable = $arr_items;

		return response()->json( $response );
	}


}
