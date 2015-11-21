<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use stdClass;

class Comment extends Model {
	protected $table = 'comments';
	protected $fillable
		= [
			'content',
			'author',
			'post',
			'confirmed',
		];

	/**
	 * Get comment info by id
	 *
	 * @param $comment_id
	 *
	 * @return null|stdClass
	 */
	public static function getCommentInfoById( $comment_id ) {
		$comments = Comment::all()->where( 'id', intval( $comment_id ) );
		if ( $comments->count() == 0 ) {
			return null;
		}

		$comment      = $comments->first();
		$c            = new stdClass();
		$c->id        = $comment->id;
		$c->content   = $comment->content;
		$c->confirmed = boolval( $comment->confirmed );
		$c->author    = User::getInfoById( $comment->author );

		return $c;
	}

	/**
	 * Get list comment by post_id
	 *
	 * @param $post_id
	 *
	 * @return null
	 */
	public static function getCommentsByPostId( $post_id ) {
		$comments = Comment::all()->where( 'post', intval( $post_id ) );
		if ( $comments->count() == 0 ) {
			return [ ];
		}

		$listComments = [ ];
		foreach ( $comments as $comment ) {
			$listComments[] = Comment::getCommentInfoById( $comment->id );
		}

		return $listComments;
	}
}
