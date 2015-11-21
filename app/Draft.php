<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Draft extends Model {
	protected $table = 'drafts';
	protected $fillable
		= [
			'maMH',
			'tenMH',
			'soTin',
			'maLMH',
			'teacher',
			'soSV',
			'thu',
			'tiet',
			'address',
			'note',
		];
}
