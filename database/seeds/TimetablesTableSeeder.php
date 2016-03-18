<?php

use App\ClassSubject;
use App\ClassX;
use App\Http\Controllers\SeedDataController;
use App\SubClassSubject;
use App\TimeTable;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TimetablesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $t = new SeedDataController();

        $t->seedDataClassX_es();
        $t->seedTimetable();
        $t->seedSubject();
        $t->seedClassSubject();
        $t->createTeacherUser();
        $t->seedSubClassSubject();

        $user_id = DB::table('users')->insertGetId([
            'name' => 'Trần Văn Tú',
            'msv' => '13020499',
            'class' => 6,
            'type' => 'student',
            'activated' => 1,
            'email' => 'tutv_58@vnu.edu.vn',
            'password' => md5('123456'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $user = DB::table('users')
            ->where('id', intval($user_id))
            ->get();

        /**
         * Get timetable UET
         */
        $res = getTimeTableUET($user->msv);

        dd($res);

        /**
         * Dữ liệu trả về
         */

        $name = $res['name'];
        $qh = $res['qh'];
        $timetable = $res['timetable'];

        /**
         * Tìm kiếm lớp khóa học
         */
        $classXes = ClassX::all()->where('name', $qh);
        if ($classXes->count() > 0) {
            $classX_id = $classXes->first()->id;

        } else {
            $classX = ClassX::create([
                'name' => $qh,
            ]);

            $classX_id = $classX->id;
        }

        /**
         * Import timetable
         */
        foreach ($timetable as $index => $t) {
            $maLMH = $t->maLMH;
            $nhom = intval($t->nhom);

            if ($nhom == 0) {//Nhóm lý thuyết
                $lmhs = ClassSubject::all()->where('maLMH', $maLMH);

                if ($lmhs->count() > 0) {
                    $lmh = $lmhs->first();
                    $lmh_id = $lmh->id;
                    $subs = SubClassSubject::all()->where('classSubject', $lmh_id);

                    foreach ($subs as $s) {
                        $sub_id = $s->id;

                        $tt = TimeTable::create([
                            'user' => $user->id,
                            'subClass' => $sub_id,
                        ]);
                    }
                }
            } else {//Nhóm thực hành
                $lmhs = ClassSubject::all()->where('maLMH', $maLMH);

                if ($lmhs->count() > 0) {
                    $lmh = $lmhs->first();
                    $lmh_id = $lmh->id;
                    $subs = SubClassSubject::all()->where('classSubject', $lmh_id);
                    if ($subs->count() > 0) {
                        foreach ($subs as $s) {
                            $sub_id = $s->id;
                            if (intval($s->nhom) == 0
                                || intval($s->nhom == $nhom)
                            ) {
                                $tt = TimeTable::create([
                                    'user' => $user->id,
                                    'subClass' => $sub_id,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
