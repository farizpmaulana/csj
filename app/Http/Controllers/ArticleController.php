<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        return view('articles.index', [
            'articles' => config('articles'),
        ]);
    }

    public function show(string $article): View
    {
        $articles = config('articles');

        abort_unless(isset($articles[$article]), 404);

        return view('articles.show', [
            'articleSlug' => $article,
            'article' => $articles[$article],
        ]);
    }
}
