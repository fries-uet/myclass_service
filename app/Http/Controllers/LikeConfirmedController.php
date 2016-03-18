<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use stdClass;

class LikeConfirmedController extends Controller
{
    public function like(Request $request)
    {
        onlyAllowPostRequest($request);

        $post_id = $request->input('id');
        $user_id = $request->input('user');

        $posts = Post::all()->where('id', intval($post_id));
        $count_like = intval($posts->first()->like);

        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();

        $likes = Like::all()->where('user_id', intval($user_id))->where('post_id', intval($post_id));

        if ($likes->count() > 0) {
            $is_like = (bool)$likes->first()->is_like;
            if (!$is_like) {
                DB::table('likes')
                    ->where('user_id', intval($user_id))->where('post_id', intval($post_id))
                    ->update([
                        'is_like' => 1,
                        'updated_at' => Carbon::now(),
                    ]);

                $count_like += 2;
            } else {
                $response->error = true;
                $response->error_msg = 'Bạn đã thích bài viết này!';

                return response()->json($response);
            }
        } else {
            $like = Like::create([
                'user_id' => $user_id,
                'post_id' => $post_id,
                'is_like' => 1
            ]);

            $count_like++;
        }

        if ($posts->count() == 0) {
            $response->error = true;
            $response->error_msg = 'Bài viết không tồn tại!';

            return response()->json($response);
        }

        $p = DB::table('posts')->where('id', intval($post_id))->update([
            'like' => $count_like,
            'updated_at' => Carbon::now(),
        ]);

        $response->error = false;
        $response->msg = 'Cảm ơn bạn!';

        return response()->json($response);
    }

    public function dislike(Request $request)
    {
        onlyAllowPostRequest($request);

        $post_id = $request->input('id');
        $user_id = $request->input('user');

        $posts = Post::all()->where('id', intval($post_id));
        $count_like = intval($posts->first()->like);

        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();

        $likes = Like::all()->where('user_id', intval($user_id))->where('post_id', intval($post_id));

        if ($likes->count() > 0) {
            $is_like = (bool)$likes->first()->is_like;
            if ($is_like) {
                DB::table('likes')
                    ->where('user_id', intval($user_id))->where('post_id', intval($post_id))
                    ->update([
                        'is_like' => 0,
                        'updated_at' => Carbon::now(),
                    ]);

                $count_like -= 2;
            } else {
                $response->error = true;
                $response->error_msg = 'Bạn đã không thích bài viết này!';

                return response()->json($response);
            }
        } else {
            $like = Like::create([
                'user_id' => $user_id,
                'post_id' => $post_id,
                'is_like' => 0
            ]);

            $count_like--;
        }

        if ($posts->count() == 0) {
            $response->error = true;
            $response->error_msg = 'Bài viết không tồn tại!';

            return response()->json($response);
        }

        $p = DB::table('posts')->where('id', intval($post_id))->update([
            'like' => $count_like,
            'updated_at' => Carbon::now(),
        ]);

        $response->error = false;
        $response->msg = 'Cảm ơn bạn!';

        return response()->json($response);
    }

    public function vote(Request $request)
    {
        onlyAllowPostRequest($request);

        $comment_id = $request['id'];
        $user_id = $request['user'];

        /**
         * Dữ liệu trả về
         */
        $response = new stdClass();

        $user = User::getInfoById($user_id);
        if ($user == null) {
            $response->error = true;
            $response->error_msg = 'Đã có lỗi gì đó xảy ra!';

            return response()->json($response);
        }

        if ($user->type != 'teacher') {
            $response->error = true;
            $response->error_msg = 'Bạn không có quyền này!!';

            return response()->json($response);
        }

        $p = DB::table('comments')->where('id', intval($comment_id))->update([
            'confirmed' => 1,
            'updated_at' => Carbon::now(),
        ]);

        $response->error = false;
        $response->error_msg = 'Cảm ơn bạn đã xác nhận đây là bình luận hay!';

        return response()->json($response);
    }
}
