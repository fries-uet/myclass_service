<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class s_list_exam extends Model
{
    protected $table = 's_list_exams';

    protected $fillable = [
        'sent',
        'user_id',
        'subject_code',
        'subject_name',
    ];
}
