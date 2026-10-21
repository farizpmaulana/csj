<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Brand;
use App\Models\Client;
use App\Models\GalleryImage;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'brands' => Brand::query()
                ->where('is_visible', true)
                ->orderBy('position')
                ->get(),
            'clients' => Client::query()
                ->where('is_visible', true)
                ->orderBy('position')
                ->get(),
            'gallery' => GalleryImage::query()
                ->where('is_visible', true)
                ->orderBy('position')
                ->get(),
            'latestArticles' => Article::query()
                ->published()
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
