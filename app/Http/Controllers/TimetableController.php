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

class TimetableController extends Controller
{
    public function getTimetable(Request $request)
    {
        $user_id = $request->input('id');

        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();

        $user = User::getInfoById($user_id);
        if ($user == null) {
            $response->error = true;
            $response->error_msg
                = 'Đã có vấn đề xảy ra! Bạn vui long quay lại sau.';

            return response()->json($response);
        }

        /**
         * Giáo viên
         */
        if ($user->type == 'teacher') {
            $u_x_id = $user->id;
            $classSubXS = SubClassSubject::all()->where('teacher', intval($u_x_id))->where('nhom', 0);

            if ($classSubXS->count() == 0) {
                $response->error = true;
                $response->error_msg
                    = 'Đã có vấn đề xảy ra! Bạn vui long quay lại sau.';

                return response()->json($response);
            }

            $arr_items = [];
            foreach ($classSubXS as $i => $s) {
                $s_id = $s->id;
                $subClassSubject = SubClassSubject::all()->where('id', intval($s_id))->first();

                $class_id = $subClassSubject->classSubject;
                $classSubject = ClassSubject::all()->where('id', intval($class_id))->first();

                $subject_id = $classSubject->subject;

                $subject = Subject::all()->where('id', intval($subject_id))->first();

                $item_s = new stdClass();
                $item_s->maMH = $subject->maMH;
                $item_s->maLMH = $classSubject->maLMH;
                $item_s->name = $subject->name;
                $item_s->soTin = $subject->soTin;
                $item_s->viTri = $subClassSubject->viTri;
                $item_s->soTiet = $subClassSubject->soTiet;
                $item_s->soSV = $subClassSubject->soSV;
                $item_s->nhom = $subClassSubject->nhom;
                $item_s->address = $subClassSubject->address;
                $item_s->teacher
                    = User::getInfoById($subClassSubject->teacher);

                $arr_items[] = $item_s;
            }
        } else {

            $timetable = TimeTable::all()->where('user', intval($user_id));
            if ($timetable->count() == 0) {
                $response->error = true;
                $response->error_msg = 'Bạn chưa đồng bộ thời khóa biểu!';

                return response()->json($response);
            }

            $arr_items = [];
            foreach ($timetable as $i => $s) {
                $s_id = $s->subClass;
                $subClassSubject = SubClassSubject::all()->where('id', intval($s_id))->first();

                $class_id = $subClassSubject->classSubject;
                $classSubject = ClassSubject::all()->where('id', intval($class_id))->first();

                $subject_id = $classSubject->subject;

                $subject = Subject::all()->where('id', intval($subject_id))->first();

                $item_s = new stdClass();
                $item_s->maMH = $subject->maMH;
                $item_s->maLMH = $classSubject->maLMH;
                $item_s->name = $subject->name;
                $item_s->soTin = $subject->soTin;
                $item_s->viTri = $subClassSubject->viTri;
                $item_s->soTiet = $subClassSubject->soTiet;
                $item_s->soSV = $subClassSubject->soSV;
                $item_s->nhom = $subClassSubject->nhom;
                $item_s->address = $subClassSubject->address;
                $item_s->teacher
                    = User::getInfoById($subClassSubject->teacher);

                $arr_items[] = $item_s;
            }
        }

        $filter = [];
        $filter[0] = $arr_items[0];
        $j = 0;
        for ($i = 1; $i < count($arr_items); $i++) {
            if ($filter[$j]->maLMH != $arr_items[$i]->maLMH
                || $filter[$j]->nhom != $arr_items[$i]->nhom
            ) {
                $j++;
                $filter[$j] = $arr_items[$i];
            }
        }
        $response->error = false;
        $response->timetable = $filter;

        return response()->json($response);
    }


}
