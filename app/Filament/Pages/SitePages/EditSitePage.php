<?php

namespace App\Filament\Pages\SitePages;

use App\Models\Page;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use UnitEnum;

/**
 * Editor de textos e imágenes de una página del sitio. Reutiliza la pantalla de SettingsPage
 * pero lee y guarda en la tabla "pages" (contenido JSON + meta).
 */
abstract class EditSitePage extends SettingsPage
{
    /** Clave en la tabla pages (home, nosotros, …). */
    protected static string $pageKey;

    /** Ruta pública de la página, para el botón "Ver en sitio". */
    protected static string $publicRoute;

    protected static string $settings = GeneralSettings::class;

    protected static string|UnitEnum|null $navigationGroup = 'Páginas';

    /** Campos del contenido propio de la página. */
    abstract protected function contentSchema(): array;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            ...$this->contentSchema(),
            Section::make('Buscadores (SEO)')->columnSpanFull()->collapsed()->schema([
                TextInput::make('meta_title')->label('Título de la pestaña')->maxLength(120),
                Textarea::make('meta_description')->label('Descripción para Google')->rows(2)->maxLength(300),
            ]),
        ]);
    }

    protected function fillForm(): void
    {
        $page = Page::firstOrNew(['key' => static::$pageKey]);
        $this->form->fill(($page->content ?? []) + [
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $page = Page::firstOrNew(['key' => static::$pageKey]);
        $page->meta_title = Arr::pull($data, 'meta_title');
        $page->meta_description = Arr::pull($data, 'meta_description');
        $page->content = array_merge($page->content ?? [], $data);
        $page->save();

        $this->rememberData();
        Notification::make()->success()->title('Página guardada')->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')->label('Ver en sitio')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->color('gray')
                ->url(route(static::$publicRoute), shouldOpenInNewTab: true),
        ];
    }
}
