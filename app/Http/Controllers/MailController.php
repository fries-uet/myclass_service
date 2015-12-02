<?php

namespace App\Http\Controllers;

use FriesMail;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller {
	public function sendMail( $subject, $content, $arrTo ) {
		$friesMap = new FriesMail( $subject, $content );
		$friesMap->setFromName( 'Fries Team' )->setFrom( 'fries.uet@gmail.com' );

		foreach ( $arrTo as $a ) {
			$friesMap->addTo( $a );
		}

		return $friesMap->sendMail();
	}
}
