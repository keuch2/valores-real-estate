<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegacyRedirectController extends Controller
{
    private const PAGES = [
        'index' => 'home',
        'proyectos' => 'projects.index',
        'noticias' => 'news.index',
        'nosotros' => 'about',
        'contacto' => 'contact',
        'trabaja-con-nosotros' => 'careers',
        'investor-pass' => 'investor',
    ];

    public function __invoke(Request $request, string $page): RedirectResponse
    {
        if ($page === 'proyecto') {
            $slug = (string) $request->query('p');
            $exists = $slug !== '' && Project::where('is_published', true)
                ->where(fn ($q) => $q->where('slug', $slug)->orWhereJsonContains('previous_slugs', $slug))->exists();

            return $exists ? redirect()->route('projects.show', $slug, 301) : redirect()->route('projects.index', [], 301);
        }
        abort_unless(isset(self::PAGES[$page]), 404);

        return redirect()->route(self::PAGES[$page], [], 301);
    }

    public function brochure(string $slug): RedirectResponse
    {
        $project = Project::where('slug', $slug)->whereNotNull('brochure_path')->firstOrFail();

        return redirect()->away(media_url($project->brochure_path), 301);
    }
}
