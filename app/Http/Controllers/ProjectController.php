<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::published()->get();

        return view('pages.projects', [
            'page' => Page::for('proyectos'),
            'projects' => $projects,
            'pins' => $this->pins($projects),
        ]);
    }

    public function show(string $slug): View|RedirectResponse
    {
        $project = Project::where('slug', $slug)->where('is_published', true)->first();
        if (! $project) {
            $moved = Project::where('is_published', true)->whereJsonContains('previous_slugs', $slug)->first();
            abort_unless($moved, 404);

            return redirect()->route('projects.show', $moved->slug, 301);
        }
        $project->load(['images', 'typologies', 'units']);
        $number = Project::published()->pluck('id')->search($project->id) + 1;

        return view('pages.project', [
            'project' => $project,
            'number' => $number,
            'advisors' => SiteController::advisors(),
        ]);
    }

    public static function pins($projects): array
    {
        return $projects->values()->map(fn (Project $p, $i) => [
            'num' => $i + 1,
            'name' => $p->name,
            'll' => [$p->lat, $p->lng],
            'url' => $p->url(),
        ])->filter(fn ($pin) => $pin['ll'][0] !== null)->values()->all();
    }
}
