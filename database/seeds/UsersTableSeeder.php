<?php

use App\ClassSubject;
use App\ClassX;
use App\SubClassSubject;
use App\TimeTable;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();

        $teacher_id = DB::table('users')->insertGetId([
            'name' => 'TS.Tô Văn Khánh',
            'msv' => '99999999',
            'class' => 6,
            'type' => 'teacher',
            'activated' => 1,
            'email' => 'khanhtv@vnu.edu.vn',
            'password' => md5('123456'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
