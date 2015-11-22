<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use stdClass;

class User extends Model implements AuthenticatableContract,
	AuthorizableContract,
	CanResetPasswordContract {
	use Authenticatable, Authorizable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable
		= [
			'name',
			'email',
			'password',
			'msv',
			'type',
			'class'
		];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [ 'password', 'remember_token' ];

	/**
	 * Get user object by id
	 *
	 * @param $id
	 *
	 * @return null|stdClass
	 */
	public static function getInfoById( $id ) {
		$users = User::all()->where( 'id', intval( $id ) );

		if ( $users->count() == 0 ) {
			return null;
		}

		$user     = $users->first();
		$u        = new stdClass();
		$u->id    = $user->id;
		$u->name  = $user->name;
		$u->lop   = ClassX::getClassName( $user->class );
		$u->email = $user->email;
		if ( $u->type == 'teacher' ) {
			$u->avatar = '0';
		} else {
			$u->avatar = '1';
		}

		$u->type = $user->type;
		if ( $u->type == 'student' ) {
			$u->mssv = $user->msv;
		} else {
			$u->mssv = '';
		}

		return $u;
	}
}
