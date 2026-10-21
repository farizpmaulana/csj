<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        return view('articles.index', [
            // The category filter runs in the browser, so every published
            // article is rendered rather than paginated.
            'articles' => Article::query()
                ->published()
                ->newestFirst()
                ->get(),
        ]);
    }

    public function show(string $article): View
    {
        return view('articles.show', [
            'articleModel' => Article::query()
                ->where('slug', $article)
                ->published()
                ->firstOrFail(),
        ]);
    }
}
