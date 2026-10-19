<?php

use App\Filament\Pages\Settings\ManageGeneral;
use App\Filament\Pages\Settings\ManageMail;
use App\Filament\Pages\Settings\ManageSpam;
use App\Filament\Pages\Settings\ManageWhatsapp;
use App\Filament\Pages\SitePages\EditAboutPage;
use App\Filament\Pages\SitePages\EditCareersPage;
use App\Filament\Pages\SitePages\EditContactPage;
use App\Filament\Pages\SitePages\EditHomePage;
use App\Filament\Pages\SitePages\EditInvestorPage;
use App\Filament\Pages\SitePages\EditNewsPage;
use App\Filament\Pages\SitePages\EditProjectsPage;
use App\Filament\Resources\FormSubmissions\Pages\ViewFormSubmission;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\FormSubmission;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;
use App\Settings\MailSettings;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    seedSite();
    $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'secreto-123', 'is_active' => true]);
});

it('pide iniciar sesión', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('no deja entrar a usuarios desactivados', function () {
    $user = User::create(['name' => 'X', 'email' => 'x@example.com', 'password' => 'secreto-123', 'is_active' => false]);
    $this->actingAs($user)->get('/admin')->assertForbidden();
});

it('muestra todas las pantallas del CMS', function (string $url) {
    $this->actingAs($this->admin)->get($url)->assertOk();
})->with([
    '/admin', '/admin/projects', '/admin/projects/create', '/admin/news', '/admin/news/create',
    '/admin/board-members', '/admin/advisors', '/admin/form-submissions', '/admin/users',
    '/admin/configuracion/general', '/admin/configuracion/whatsapp', '/admin/configuracion/email', '/admin/configuracion/anti-spam',
    '/admin/paginas/inicio', '/admin/paginas/proyectos', '/admin/paginas/noticias', '/admin/paginas/nosotros',
    '/admin/paginas/trabaja-con-nosotros', '/admin/paginas/contacto', '/admin/paginas/investor-pass',
]);

it('muestra la edición de cada proyecto', function (string $slug) {
    $project = Project::where('slug', $slug)->first();
    $this->actingAs($this->admin)->get(ProjectResource::getUrl('edit', ['record' => $project]))->assertOk();
})->with(['sol-city', 'cumbres', 'la-ribera', 'paraqvaria']);

it('guarda los textos de una página', function () {
    $this->actingAs($this->admin);
    Livewire::test(EditAboutPage::class)
        ->set('data.title', 'Nuevo título de Nosotros')
        ->call('save')
        ->assertHasNoErrors();

    expect(Page::for('nosotros')->fresh()->get('title'))->toBe('Nuevo título de Nosotros');
    $this->get('/nosotros')->assertSee('Nuevo título de Nosotros');
});

it('conserva la contraseña SMTP si se guarda el formulario vacío', function () {
    $mail = app(MailSettings::class);
    $mail->password = 'clave-smtp';
    $mail->save();

    $this->actingAs($this->admin);
    Livewire::test(ManageMail::class)->assertSet('data.password', null)->set('data.host', 'smtp.example.com')->call('save')->assertHasNoErrors();

    $fresh = app(MailSettings::class)->refresh();
    expect($fresh->password)->toBe('clave-smtp')->and($fresh->host)->toBe('smtp.example.com');
});

it('carga cada pantalla de configuración y página como Livewire', function (string $class) {
    $this->actingAs($this->admin);
    Livewire::test($class)->assertOk();
})->with([ManageGeneral::class, ManageWhatsapp::class, ManageMail::class, ManageSpam::class, EditHomePage::class, EditProjectsPage::class,
    EditNewsPage::class, EditCareersPage::class, EditContactPage::class, EditInvestorPage::class]);

it('marca el mensaje como leído al abrirlo y sólo los admins descargan el CV', function () {
    Storage::fake('local');
    Storage::disk('local')->put('cvs/test.pdf', 'pdf');
    $submission = FormSubmission::create(['type' => 'careers', 'name' => 'Juan', 'cv_path' => 'cvs/test.pdf', 'cv_original_name' => 'cv.pdf']);

    $this->actingAs($this->admin);
    Livewire::test(ViewFormSubmission::class, ['record' => $submission->id])
        ->callAction('cv')
        ->assertFileDownloaded('cv.pdf');
    expect($submission->fresh()->read_at)->not->toBeNull();

    auth()->logout();
    $this->get("/admin/form-submissions/{$submission->id}")->assertRedirect('/admin/login');
});
