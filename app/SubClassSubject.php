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

	/**
	 * Get SubClassSubjects by maLMH
	 *
	 * @param $maLMH
	 *
	 * @return null|static
	 */
	public static function getSubClassSubjectsBymaLMH( $maLMH ) {
		$lmhs = ClassSubject::all()->where( 'maLMH', $maLMH );

		if ( $lmhs->count() == 0 ) {
			return null;
		}

		$lmh    = $lmhs->first();
		$lmh_id = $lmh->id;
		$subs   = SubClassSubject::all()->where( 'classSubject', $lmh_id );

		return $subs;
	}
}
