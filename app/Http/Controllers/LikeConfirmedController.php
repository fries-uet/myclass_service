<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
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

		$likes = Like::all()->where( 'user_id', intval( $user_id ) )
		             ->where( 'post_id', intval( $post_id ) );

		if ( $likes->count() > 0 ) {
			$response->error     = true;
			$response->error_msg = 'Bạn đã cảm ơn bài viết này!';

			return response()->json( $response );
		}

		$like = Like::create( [
			'user_id' => $user_id,
			'post_id' => $post_id,
		] );

		$posts = Post::all()->where( 'id', intval( $post_id ) );

		if ( $posts->count() == 0 ) {
			$response->error     = true;
			$response->error_msg = 'Bạn đã cảm ơn bài viết này!';

			return response()->json( $response );
		}

		$count_like = intval( $posts->first()->like );
		$count_like ++;

		$p = DB::table( 'posts' )->where( 'id', intval( $post_id ) )
		       ->update( [ 'like' => $count_like ] );

		$response->error = false;
		$response->msg   = 'Cảm ơn bạn!';

		return response()->json( $response );
	}
}
