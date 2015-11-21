<?php

namespace App\Http\Controllers;

use App\Like;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use stdClass;

class LikeConfirmedController extends Controller {
	public function like( Request $request ) {
		$post_id = $request->input( 'id' );
		$user_id = $request->input( 'user' );

		/**
		 * Dữ liệu trả về
		 */
		$response = new stdClass();

		$likes = Like::all()->where( 'user_id', $user_id )
		             ->where( 'post_id', $post_id );

		if ( $likes == 0 ) {
			$response->error     = true;
			$response->error_msg = 'Bạn đã cảm ơn bài viết này!';

			return response()->json( $response );
		}

		$like = Like::create( [
			'user_id' => $user_id,
			'post_id' => $post_id,
		] );

		$post = DB::table( 'posts' )->where( 'id', intval( $post_id ) );


		$response->error = false;
		$response->msg   = 'Cảm ơn bạn!';

		return response()->json( $response );
	}
}
