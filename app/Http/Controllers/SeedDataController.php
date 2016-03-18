<?php

namespace App\Http\Controllers;

use App\ClassSubject;
use App\ClassX;
use App\Draft;
use App\SubClassSubject;
use App\Subject;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

class SeedDataController extends Controller
{
    /**
     * Init data classX
     */
    public function seedDataClassX_es()
    {
        $ks = [
            'QH-2013-I/CQ-',
        ];

        $ns = [
            'C-A',
            'C-AC',
            'C-B',
            'C-C',
            'C-D',
            'C-CLC',
            'T',
            'N',
            'ĐA',
            'ĐB',
            'M',
            'V',
            'H'
        ];

        foreach ($ks as $i => $k) {
            foreach ($ns as $j => $n) {
                $class_name = $k . $n;

                $class = ClassX::all()->where('name', $class_name);
                if ($class->count() == 0) {
                    $cl = ClassX::create([
                        'name' => $k . $n,
                        'teacher' => 68,
                    ]);
                }
            }
        }
    }

    /**
     * Init data origin timetable
     */
    public function seedTimetable()
    {
        $contents = Storage::disk('local')->get('tkb.json');

        $obj = json_decode($contents);
        foreach ($obj as $o) {
            $draf = Draft::create([
                'maMH' => (trim($o->maMH)),
                'tenMH' => (trim($o->tenMH)),
                'soTin' => (trim($o->soTin)),
                'maLMH' => (trim($o->maLMH)),
                'teacher' => (trim($o->teacher)),
                'soSV' => (trim($o->soSV)),
                'thu' => (trim($o->thu)),
                'tiet' => (trim($o->tiet)),
                'address' => (trim($o->address)),
                'note' => (trim($o->note)),
            ]);

            var_dump($draf->maMH);
        }
    }

    /**
     * Init data subject
     */
    public function seedSubject()
    {
        $drafts = Draft::all();

        foreach ($drafts as $index => $draft) {
            $maMH = $draft->maMH;

            /**
             * Kiểm tra xem mã môn học đã có chưa?
             */
            $subjects = Subject::all()->where('maMH', $maMH);
            if ($subjects->count() == 0) {
                $s = Subject::create([
                    'name' => $draft->tenMH,
                    'maMH' => $draft->maMH,
                    'soTin' => $draft->soTin,
                ]);

                var_dump($s->name);
            }
        }
    }

    public function seedClassSubject()
    {
        $drafts = Draft::all();

        foreach ($drafts as $index => $draft) {
            $maMH = $draft->maMH;
            $maLMH = $draft->maLMH;

            $classSubjects = ClassSubject::all()->where('maLMH', $maLMH);
            $subject = Subject::all()->where('maMH', $maMH)->first();

            $subject_id = $subject->id;
            if ($classSubjects->count() == 0) {
                $c = ClassSubject::create([
                    'maLMH' => $maLMH,
                    'subject' => $subject_id,
                ]);

                var_dump($c->maLMH);
            }
        }
    }

    /**
     * Create data teacher
     */
    public function createTeacherUser()
    {
        $drafts = Draft::all();

        $msv = 99999999;
        $pass = 'uet2015';
        $class = 0;
        $type = 'teacher';

        foreach ($drafts as $index => $draft) {
            $name = (trim($draft->teacher));
            $email = str_slug($name) . '@vnu.edu.vn';

            $users = User::all()->where('email', $email);
            if ($users->count() == 0) {
                $u = User::create([
                    'name' => $name,
                    'email' => $email,
                    'msv' => $msv,
                    'class' => $class,
                    'type' => $type,
                    'password' => md5($pass),
                ]);

                var_dump($u->name);
            }

        }
    }

    public function seedSubClassSubject()
    {
        $drafts = Draft::all();
        foreach ($drafts as $index => $draft) {
            $teacher_name = (trim($draft->teacher));
            $email = str_slug($teacher_name) . '@vnu.edu.vn';

            $teacher = User::all()->where('email', $email)->first();
            $teacher_id = $teacher->id;

            $tietX = $draft->tiet;
            $arr = explode('-', $tietX);
            if (count($arr) == 2) {
                $t_begin = intval($arr[0]);
                $t_end = intval($arr[1]);

                $thu = intval($draft->thu);

                $viTri = ($thu - 2) * 10 + $t_begin;
                $soTiet = $t_end - $t_begin + 1;

                $nhom_X = $draft->note;
                $nhom = 0;
                if (strtolower($nhom_X) == 'n1') {
                    $nhom = 1;
                }

                if (strtolower($nhom_X) == 'n2') {
                    $nhom = 2;
                }

                if (strtolower($nhom_X) == 'n3') {
                    $nhom = 3;
                }

                $classSubject = ClassSubject::all()->where('maLMH', $draft->maLMH)->first();

                $sub = SubClassSubject::create([
                    'teacher' => $teacher_id,
                    'address' => trim($draft->address),
                    'viTri' => intval($viTri),
                    'soTiet' => intval($soTiet),
                    'soSV' => intval($draft->soSV),
                    'classSubject' => intval($classSubject->id),
                    'nhom' => intval($nhom),
                ]);
                var_dump($index);
            }
        }
    }
}
