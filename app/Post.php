<?php

namespace App;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable
        = [
            'title',
            'content',
            'author',
            'group',
            'isIncognito',
            'base',
            'type',
        ];

    public static function getPostInfoById($id)
    {
        $id = intval($id);
        $posts = Post::all()->where('id', ($id));
        if ($posts->count() == 0) {
            return null;
        }

        $likes = DB::table('likes')
            ->where('post_id', $id)
            ->get();

        var_dump($likes);

        $post = $posts->first();
        $p = new stdClass();
        $p->id = $post->id;
        $p->title = $post->title;
        $p->content = $post->content;
        $p->group = $post->group;
        $p->like = intval($post->like);
        $p->author = User::getInfoById($post->author);
        $p->isIncognito = boolval($post->isIncognito);
        $p->type = $post->type;
        $p->base = $post->base;
        $date = new Carbon($post->created_at);
        $p->created_at = $date->format('d/m/Y');
        $p->comments = Comment::getCommentsByPostId($post->id);

        return $p;
    }
}
