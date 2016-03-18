<?php

use App\Http\Controllers\SeedDataController;
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
    }
}
