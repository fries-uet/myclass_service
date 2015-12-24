<?php

namespace App\Http\Controllers;

use FCurl;
use Illuminate\Http\Request;

use App\Http\Requests;

class ScoreUET extends Controller {

	public function updateScoreUET() {
		$arr_score = $this->getScoreUET();

		print_r( $arr_score );
	}


	/**
	 * Get list status score UET
	 *
	 * @return array
	 */
	public function getScoreUET() {
		$url = 'http://www.coltech.vnu.edu.vn/news4st/test.php';

		$browser        = new fCurl();
		$browser->refer = $url;
		$browser->resetopt();
		$fields = [
			'lstClass' => 819,
		];
		$browser->post( $url, $fields, 1, 0 );

		$content = $browser->return;

		$table = explode( '<table border="0" cellspacing="2" cellpadding="0" width="100%">', $content )[1];
		$table = trim( $table );

		$li = explode( '<LI>', $table );

		$arr_score = [ ];

		for ( $i = 1; $i < count( $li ); $i ++ ) {
			$name = explode( '</td>', $li[ $i ] )[0];
			$name = trim( $name );

			/**
			 * Đã có điểm
			 */
			if ( strpos( $name, '<b>' ) !== false ) {
				$link = explode( 'href="../', $name )[1];
				$link = explode( '" class=', $link )[0];
				$href = 'http://www.coltech.vnu.edu.vn/' . $link;

				$maLMH = explode( '<b>', $name )[1];
				$maLMH = explode( '</b>', $maLMH )[0];
				$maLMH = explode( '(', $maLMH )[0];
				$maLMH = explode( ' - ', $maLMH );

				if ( count( $maLMH ) > 1 ) {
					$maLMH = $maLMH[ count( $maLMH ) - 1 ];
					$maLMH = strtoupper( $maLMH );

					$arr_score[] = [
						'href'  => $href,
						'maLMH' => $maLMH,
					];
				}
			} else {// Chưa có điểm
				$maLMH = explode( ' - ', $name );

				if ( count( $maLMH ) > 1 ) {
					$maLMH = $maLMH[ count( $maLMH ) - 1 ];
					$maLMH = strtoupper( $maLMH );

					$arr_score[] = [
						'href'  => false,
						'maLMH' => $maLMH,
					];
				}
			}
		}

		return $arr_score;
	}
}
