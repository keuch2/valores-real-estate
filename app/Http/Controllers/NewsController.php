<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $news = News::published()->with('project')->get();
        // Filtros: "Todas", luego las etiquetas de proyectos (en su orden) y después el resto, alfabético.
        $projectTags = Project::published()->get()->map->newsTag();
        $tags = $news->map->tagLabel()->unique();
        $ordered = $projectTags->intersect($tags)->merge($tags->diff($projectTags)->sort())->values();

        return view('pages.news', [
            'page' => Page::for('noticias'),
            'news' => $news,
            'tags' => collect(['Todas'])->merge($ordered)->all(),
        ]);
    }

    public function show(string $slug): View
    {
        $item = News::published()->where('kind', 'article')->where('slug', $slug)->firstOrFail();

        return view('pages.news-show', [
            'item' => $item,
            'related' => News::published()->whereKeyNot($item->id)->with('project')->take(3)->get(),
        ]);
    }
}
