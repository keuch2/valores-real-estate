<?php

namespace App\Http\Controllers;

use App\Models\Advisor;
use App\Models\BoardMember;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        $page = Page::for('home');
        $projects = Project::published()->get();

        return view('pages.home', [
            'page' => $page,
            'projects' => $projects,
            'news' => News::published()->with('project')->take((int) $page->get('news_count', 3))->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'page' => Page::for('nosotros'),
            'board' => BoardMember::where('is_visible', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact', ['page' => Page::for('contacto'), 'advisors' => $this->advisors()]);
    }

    public function careers(): View
    {
        return view('pages.careers', ['page' => Page::for('trabaja')]);
    }

    public function investor(): View
    {
        return view('pages.investor', [
            'page' => Page::for('investor'),
            'projectNames' => join_names(Project::published()->pluck('name')->all()),
        ]);
    }

    public static function advisors()
    {
        return Advisor::where('is_visible', true)->orderBy('sort_order')->get();
    }
}
