<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubClassSubject extends Model {
	protected $table = 'sub_class_subjects';
	protected $fillable
		= [
			'teacher',
			'address',
			'viTri',
			'soTiet',
			'soSV',
			'classSubject',
			'nhom',
		];
}
