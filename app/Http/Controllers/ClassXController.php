<?php

namespace App\Http\Controllers;

use App\ClassSubject;
use App\ClassX;
use App\SubClassSubject;
use App\Subject;
use App\TimeTable;
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

		$user     = $users->first();
		$arrGroup = [ ];

		if ( $base == 'class_xes' ) {
			$id_class = $user->class;
			$classX   = ClassX::all()->where( 'id', $id_class )->first();

			$class_x          = new stdClass();
			$class_x->id      = $classX->id;
			$class_x->base    = $base;
			$class_x->name    = $classX->name;
			$class_x->soSV    = ClassX::getCountStudentByClassId( $id_class );
			$class_x->teacher = User::getInfoById( $classX->teacher );
			$arrGroup         = [ $class_x ];
		}

		if ( $base == 'classSubject' ) {
			$timeTables = TimeTable::all()->where( 'user', $user->id );
			if ( $timeTables->count() == 0 ) {
				$response->error     = true;
				$response->error_msg = 'Tài khoản chưa có lớp môn học nào!';

				return response()->json( $response );
			}

			foreach ( $timeTables as $tt ) {
				$sub_id = $tt->subClass;

				$subClassSubject = SubClassSubject::all()->where( 'id',
					intval( $sub_id ) )->first();

				$teacher_id = $subClassSubject->teacher;

				$lmh_id       = $subClassSubject->classSubject;
				$classSubject = ClassSubject::all()
				                            ->where( 'id', intval( $lmh_id ) )
				                            ->first();

				$maLMH      = $classSubject->maLMH;
				$subject_id = $classSubject->subject;
				$subject    = Subject::all()
				                     ->where( 'id', intval( $subject_id ) )
				                     ->first();

				$cl          = new stdClass();
				$cl->base    = 'classSubject';
				$cl->id      = $classSubject->id;
				$cl->maLMH   = $maLMH;
				$cl->name    = $subject->name;
				$cl->soSV    = $subClassSubject->soSV;
				$cl->teacher = User::getInfoById( $teacher_id );

				if ( $subClassSubject->nhom == 0 ) {
					$arrGroup[] = $cl;
				}
			}
		}

		$filter    = [ ];
		$filter[0] = $arrGroup[0];
		$j         = 0;
		for ( $i = 1; $i < count( $arrGroup ); $i ++ ) {
			if ( $filter[ $j ]->maLMH != $arrGroup[ $i ]->maLMH
			) {
				$j ++;
				$filter[ $j ] = $arrGroup[ $i ];
			}
		}

		$arrGroup = $filter;

		$response->error = false;
		$response->group = $arrGroup;

		return response()->json( $response );
	}

	public static function getArrEmail( $id ) {
//		if ( $base == true ) {
		$class_xes = ClassX::all()->where( 'id', intval( $id ) );

		if ( $class_xes->count() == 0 ) {
			return [ ];
		}

		$arrEmail = [ ];
		$users    = User::all()->where( 'class', intval( $id ) );
		if ( $users->count() == 0 ) {
			return [ ];
		}

		foreach ( $users as $index => $u ) {
			$arrEmail[] = $u->email;
		}

		return $arrEmail;
//		}


	}
}
