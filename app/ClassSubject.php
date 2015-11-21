<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model {
	protected $table = 'class_subjects';
	protected $fillable
		= [
			'maLMH',
			'subject',
		];

}
