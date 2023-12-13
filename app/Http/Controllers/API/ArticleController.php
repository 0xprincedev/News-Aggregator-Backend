<?php

namespace App\Http\Controllers\API;

use App\Models\Article;
use App\Models\UserLikeArticle;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        // $user = auth()->user();
        if (Auth::check() || Auth::user()) {
            $user = Auth::user();
        } else {
            $user = null;
        }

        $query = $request->q;
        $categories = $request->categories;
        $source = $request->source;
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 20;

        $sqlQuery = Article::orderBy("publishedAt");

        if ($query) {
            $sqlQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")->orWhere('content', 'like', "%{$query}%");
            });
        }


        if ($categories) {
            $sqlQuery->whereIn('category', explode(',', $categories));
        }

        if ($source) {
            $sqlQuery->where('source', $source);
        }

        return $sqlQuery->offset($offset)->limit($limit)->get();
    }

    public function getFeeds(Request $request)
    {
        $categories = array_column(Article::select('category')->groupBy('category')->get()->toArray(), 'category');
        $authors = array_column(Article::select('author')->groupBy('author')->get()->toArray(), 'author');
        $sources = array_column(Article::select('source')->groupBy('source')->get()->toArray(), 'source');

        return ['categories' => $categories, 'authors' => $authors, 'sources' => $sources];
    }

    public function setLike(Request $request)
    {
        $user = auth()->user();

        $model = new UserLikeArticle();
        $model->user_id = $user->id;
        $model->article_id = $request->article_id;
        $model->save();

        return 'success';
    }

    public function getLike(Request $request)
    {
        $result = [];

        foreach ($auth()->user()->likeArticle as $like) {
            array_push($result, $like->artilce);
        }

        return $result;
    }
}
