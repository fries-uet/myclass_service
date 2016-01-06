<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GCMUser extends Model
{
    protected $table = 'g_c_m_users';

    protected $fillable = [
        'regID',
        'name',
        'email',
    ];
}
