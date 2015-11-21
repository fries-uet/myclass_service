<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassX extends Model {
	protected $table = 'class_xes';
	protected $fillable
		= [
			'name',
		];

	/**
	 * Get class name by id
	 *
	 * @param $id
	 *
	 * @return bool|string
	 */
	public static function getClassName( $id ) {
		$classes = ClassX::all()->where( 'id', $id );
		if ( $classes->count() == 0 ) {
			return false;
		}

		$class = $classes->first();

		return $class->name;
	}

	/**
	 * Get id class by class name
	 *
	 * @param $class_name
	 *
	 * @return bool|int
	 */
	public static function getIdByClassName( $class_name ) {
		$classXes = ClassX::all()->where( 'name', $class_name );

		if ( $classXes->count() > 0 ) {
			$class = $classXes->first();

			return $class->id;
		}

		return false;
	}

	/**
	 * Get number students of class
	 *
	 * @param $id
	 *
	 * @return int
	 */
	public static function getCountStudentByClassId( $id ) {
		$users = User::all()->where( 'class', $id );

		return $users->count();
	}
}
