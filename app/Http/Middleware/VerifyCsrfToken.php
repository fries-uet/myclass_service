<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier {
	/**
	 * The URIs that should be excluded from CSRF verification.
	 *
	 * @var array
	 */
	protected $except
		= [
			'v1/login',
			'v1/register',
			'v1/update',
			'v1/classX/byId',
			'v1/post/classX',
			'v1/post/comment/classX',
			'v1/get/classX',
			'v1/getPosts',
		];
}
