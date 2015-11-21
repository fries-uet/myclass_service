<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeTable extends Model {
	protected $table = 'time_tables';
	protected $fillable
		= [
			'user',
			'subClass',
			'isTD',
		];
}
