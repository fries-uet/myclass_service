<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GCM_Token extends Model
{
    protected $table = 'g_c_m__tokens';
    protected $fillable = [
        'token',
        'user_id',
    ];
}
