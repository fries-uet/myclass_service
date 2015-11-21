<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Post;
use Illuminate\Http\Request;

use App\Http\Requests;
use stdClass;

class CommentController extends Controller {
	public function comment( Request $request ) {
		onlyAllowPostRequest( $request );

		$all = $request->only( [
			'post',
			'author',
			'content',
		] );

		$comment = Comment::create( [
			'post'    => intval( $all['post'] ),
			'author'  => intval( $all['author'] ),
			'content' => $all['content'],
		] );

		$c = Comment::getCommentInfoById( $comment->id );

		/**
		 * Dữ liệu trả về
		 */
		$response          = new stdClass();
		$response->error   = false;
		$response->comment = $c;

		return response()->json( $response );
	}
}
