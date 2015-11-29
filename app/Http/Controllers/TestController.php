<?php

namespace App\Http\Controllers;

use FriesMail;
use Illuminate\Http\Request;

use App\Http\Requests;

class TestController extends Controller {
	public function test_helper() {
		$friesMap = new FriesMail( 'Đây là title', 'Đây là nội dung' );
		$friesMap->setFromName( 'Fries Team' )->setFrom( 'fries.uet@gmail.com' )->addTo( 'tutv95@gmail.com' )
		         ->sendMail();
	}
}
