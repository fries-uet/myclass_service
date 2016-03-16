<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use stdClass;

class CommentController extends Controller
{
    public function comment(Request $request)
    {
        onlyAllowPostRequest($request);

        $all = $request->only([
            'post',
            'author',
            'content',
        ]);

        $post_id = intval($all['post']);
        $author_id = intval($all['author']);
        $content = $all['content'];

        $comment = Comment::create([
            'post' => $post_id,
            'author' => $author_id,
            'content' => $content,
        ]);

        $post = DB::table('posts')->where('id', $post_id)->update([
            'updated_at' => Carbon::now(),
        ]);

        $c = Comment::getCommentInfoById($comment->id);

        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();
        $response->error = false;
        $response->comment = $c;

        return response()->json($response);
    }
}
