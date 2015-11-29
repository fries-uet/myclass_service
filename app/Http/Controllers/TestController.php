<?php

namespace App\Http\Controllers;

use FriesMail;
use Hash;
use Illuminate\Http\Request;

use App\Http\Requests;
use Session;

class TestController extends Controller {
	public function test_helper() {
//		$friesMap = new FriesMail( 'Đây là title', 'Đây là nội dung' );
//		$friesMap->setFromName( 'Fries Team' )->setFrom( 'fries.uet@gmail.com' )->addTo( 'tutv95@gmail.com' )
//		         ->sendMail();

		$a = bcrypt( 'aaa' );

		Session::put( 'key', "{$a}" );

		dd( session( 'key' ) );
	}
}
