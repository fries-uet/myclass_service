<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Read extends Model {
	protected $table = 'reads';

	protected $fillable = [
		'post_id',
		'user_id',
		'read'
	];

}
