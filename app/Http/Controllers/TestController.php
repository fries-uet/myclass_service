<?php

namespace App\Http\Controllers;

use FCurl;
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

	public function dkmh() {
		$url            = 'http://dangkyhoc.daotao.vnu.edu.vn/dang-nhap';
		$browser        = new fCurl();
		$browser->refer = $url;
		$browser->resetopt();
		$browser->get( $browser->refer, true, 1 );
		$browser->get( $browser->refer, true, 1 );

		$user = '13020499';
		$pass = 'dktd2015';

		$str_temp = $browser->return;
		$str_temp = explode( '__RequestVerificationToken', $str_temp )[1];
		$str_temp = explode( '>', $str_temp )[0];
		$str_temp = explode( 'value="', $str_temp )[1];
		$verti    = explode( '"', $str_temp )[0];

		/**
		 * Đăng nhập
		 */
		$field = [
			'LoginName'                  => $user,
			'Password'                   => $pass,
			'__RequestVerificationToken' => $verti,
		];
		$browser->post( $url, $field, 1, 0 );

		$contentX = $browser->return;

		if ( strpos( $contentX, '/Account/Logout' ) === false ) {
			return false;
		}

		/**
		 * Lấy source trang thời khóa biểu
		 */
		$url_time
			= 'http://dangkyhoc.daotao.vnu.edu.vn/danh-sach-mon-hoc/1/1';
		$browser->post( $url_time, null, 1, 0 );
		$source_html = $browser->return;

		$source_html = html_entity_decode( $source_html );

		$arr = explode( '<tr title="', $source_html );

		$maLMH = 'PES1035 6';

		foreach ( $arr as $index => $a ) {
			if ( strpos( $a, $maLMH ) !== false ) {
				if ( strpos( $a, 'checkbox' ) !== false ) {
					$content_email = $a;
					$subject       = $maLMH . ' còn trống!';

					$sender = new MailController();
					if ( $sender->sendMail( $subject, $content_email, [ 'tutv95@gmail.com' ] ) ) {
						echo 'success';
					}
				} else {
					echo 'full HD';
				}
			}
		}
	}
}
