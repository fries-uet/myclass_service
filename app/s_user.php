<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class s_user extends Model
{
    protected $table = 's_users';

    protected $fillable = [
        'email',
        'name',
        'msv',
        'is_active',
    ];
}
