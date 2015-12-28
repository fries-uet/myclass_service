<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class s_score extends Model
{
    protected $table = 's_scores';
    protected $fillable = [
        'code',
        'href',
    ];
}
