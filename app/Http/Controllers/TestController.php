<?php

namespace App\Http\Controllers;

use App\RegSubject;
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

	/**
	 * Tu TV
	 */
	public function tutv() {
		$mssv    = '12040241';
		$pass    = '06109294';
		$arr_lmh = [
			'MAT1078 3',
			'MAT1078 4',
		];
		$this->dkmh( $mssv, $pass, $arr_lmh );
	}

	public function dkmh( $user, $pass, $arr_lmh ) {
		$url            = 'http://dangkyhoc.daotao.vnu.edu.vn/dang-nhap';
		$browser        = new fCurl();
		$browser->refer = $url;
		$browser->resetopt();
		$browser->get( $browser->refer, true, 1 );
		$browser->get( $browser->refer, true, 1 );

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
			echo 'Login failed!';

			return;
		}

		/**
		 * Lấy source trang thời khóa biểu
		 */
		$url_time = 'http://dangkyhoc.daotao.vnu.edu.vn/danh-sach-mon-hoc/1/1';
		$browser->post( $url_time, null, 1, 0 );
		$source_html = $browser->return;

		$source_html = html_entity_decode( $source_html );
		$source_html = trim( $source_html );

		$arr = explode( '<tr title="', $source_html );

		$maLMH = $arr_lmh;

		foreach ( $arr as $index => $a ) {
			if ( $a == '' ) {
				continue;
			}

			foreach ( $maLMH as $i => $lmh ) {
				if ( strpos( $a, $lmh ) !== false ) {
					// Input checkbox
					$input = explode( 'text-align:center;">', $a )[1];
					$input = explode( '</td>', $input )[0];
					$input = trim( $input );

					// Name subject
					$name = explode( '<td>', $a )[1];
					$name = explode( '</td>', $name )[0];
					$name = explode( '(', $name )[0];
					$name = trim( $name );

					if ( strpos( $a, 'checkbox' ) !== false ) {
						//dk
						$row_index = explode( 'data-rowindex="', $input )[1];
						$row_index = explode( '"', $row_index )[0];

//						dd( $row_index );

						$url_choose = 'http://dangkyhoc.daotao.vnu.edu.vn/chon-mon-hoc/' . $row_index . '/1/1';
						$browser->post( $url_choose, null, 1, 0 );

						$url_confirm = 'http://dangkyhoc.daotao.vnu.edu.vn/xac-nhan-dang-ky/1';
						$browser->post( $url_confirm, null, 1, 0 );

//						$content_email = 'Đã đăng kí thành công môn ' . $name . ' :]]]';
//						$subject       = $name . ' còn trống!';

//						$sender = new MailController();
//						if ( $sender->sendMail( $subject, $content_email, [ 'tutv95@gmail.com' ] ) ) {
//							echo 'success';
//						}
					} else {
						echo $name . ' Full HD<br>';
					}
				}
			}
		}
	}

	public function dkmh2( $user, $pass, $arr_lmh ) {
		$url            = 'http://dangkyhoc.daotao.vnu.edu.vn/dang-nhap';
		$browser        = new fCurl();
		$browser->refer = $url;
		$browser->resetopt();
		$browser->get( $browser->refer, true, 1 );
		$browser->get( $browser->refer, true, 1 );

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
			echo 'Login failed!';

			return;
		}

		/**
		 * Lấy source trang thời khóa biểu
		 */
		$url_time = 'http://dangkyhoc.daotao.vnu.edu.vn/danh-sach-mon-hoc/1/2';
		$browser->post( $url_time, null, 1, 0 );
		$source_html = $browser->return;

		$source_html = html_entity_decode( $source_html );
		$source_html = trim( $source_html );

		$arr = explode( '<tr title="', $source_html );

		$maLMH = $arr_lmh;

		foreach ( $arr as $index => $a ) {
			if ( $a == '' ) {
				continue;
			}

			foreach ( $maLMH as $i => $lmh ) {
				if ( strpos( $a, $lmh ) !== false ) {
					// Input checkbox
					$input = explode( 'text-align:center;">', $a )[1];
					$input = explode( '</td>', $input )[0];
					$input = trim( $input );

					// Name subject
					$name = explode( '<td>', $a )[1];
					$name = explode( '</td>', $name )[0];
					$name = explode( '(', $name )[0];
					$name = trim( $name );

					if ( strpos( $a, 'checkbox' ) !== false ) {
						//dk
						$row_index = explode( 'data-rowindex="', $input )[1];
						$row_index = explode( '"', $row_index )[0];

						$url_choose = 'http://dangkyhoc.daotao.vnu.edu.vn/chon-mon-hoc/' . $row_index . '/1/2';
						$browser->post( $url_choose, null, 1, 0 );

						$url_confirm = 'http://dangkyhoc.daotao.vnu.edu.vn/xac-nhan-dang-ky/1';
						$browser->post( $url_confirm, null, 1, 0 );

						$content_email = 'Đã đăng kí thành công môn ' . $name . ' :]]]';
						$subject       = $name . ' còn trống!';

						$sender = new MailController();
						if ( $sender->sendMail( $subject, $content_email, [ 'tutv95@gmail.com' ] ) ) {
							echo 'success';
						}
					} else {
						echo $name . ' Full HD<br>';
					}
				}
			}
		}
	}

	public function testCronb() {
		$sender = new MailController();
		$sender->sendMail( 'ok', date( 'Y-m-d H:m:i', time() ), [ 'tutv95@gmail.com' ] );
	}
}
