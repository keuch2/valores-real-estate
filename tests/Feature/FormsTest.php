<?php

use App\Mail\SubmissionMail;
use App\Models\FormSubmission;
use App\Settings\SpamSettings;
use App\Settings\WhatsappSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedSite();
    Mail::fake();
});

$contact = fn (array $extra = []) => array_merge([
    'nombre' => 'Ana Pérez', 'email' => 'ana@example.com', 'telefono' => '0981 000 000', 'mensaje' => 'Hola',
], guard('contact'), $extra);

it('guarda el mensaje de contacto y avisa por email', function () use ($contact) {
    $this->postJson('/contacto', $contact())->assertOk()->assertJson(['ok' => true]);

    $submission = FormSubmission::sole();
    expect($submission->type)->toBe('contact')->and($submission->email)->toBe('ana@example.com');
    Mail::assertSent(SubmissionMail::class, fn ($mail) => $mail->hasTo('valores@valores.com.py'));
});

it('valida los campos obligatorios', function () use ($contact) {
    $this->postJson('/contacto', $contact(['email' => 'no-es-email', 'mensaje' => '']))
        ->assertStatus(422)->assertJsonValidationErrors(['email', 'mensaje']);
    expect(FormSubmission::count())->toBe(0);
});

it('descarta en silencio a los bots que completan el campo trampa', function () use ($contact) {
    $this->postJson('/contacto', $contact(['website' => 'http://spam.example']))->assertOk();
    expect(FormSubmission::count())->toBe(0);
});

it('rechaza envíos demasiado rápidos', function () use ($contact) {
    $this->postJson('/contacto', $contact(['_ts' => encrypt(now()->timestamp)]))->assertStatus(422);
    expect(FormSubmission::count())->toBe(0);
});

it('limita los envíos por IP', function () use ($contact) {
    foreach (range(1, 3) as $i) {
        $this->postJson('/contacto', $contact())->assertOk();
    }
    $this->postJson('/contacto', $contact())->assertStatus(429);
});

describe('reCAPTCHA', function () use ($contact) {
    beforeEach(function () {
        $spam = app(SpamSettings::class);
        $spam->recaptcha_enabled = true;
        $spam->recaptcha_site_key = 'site';
        $spam->recaptcha_secret = 'secret';
        $spam->save();
    });

    it('rechaza puntajes bajos', function () use ($contact) {
        Http::fake(['www.google.com/*' => Http::response(['success' => true, 'score' => 0.2, 'action' => 'contact'])]);
        $this->postJson('/contacto', $contact(['g-recaptcha-response' => 'token']))->assertStatus(422);
        expect(FormSubmission::count())->toBe(0);
    });

    it('acepta puntajes altos y guarda el puntaje', function () use ($contact) {
        Http::fake(['www.google.com/*' => Http::response(['success' => true, 'score' => 0.9, 'action' => 'contact'])]);
        $this->postJson('/contacto', $contact(['g-recaptcha-response' => 'token']))->assertOk();
        expect(FormSubmission::sole()->recaptcha_score)->toBe(0.9);
    });

    it('si Google no responde, acepta el envío marcado como posible spam', function () use ($contact) {
        Http::fake(['www.google.com/*' => Http::response(null, 500)]);
        $this->postJson('/contacto', $contact(['g-recaptcha-response' => 'token']))->assertOk();
        expect(FormSubmission::sole()->is_spam_suspect)->toBeTrue();
    });

    it('carga el script de reCAPTCHA sólo en páginas con formularios', function () {
        $this->get('/contacto')->assertSee('recaptcha/api.js?render=site', false);
        $this->get('/nosotros')->assertDontSee('recaptcha/api.js', false);
    });
});

it('guarda el CV en el disco privado', function () {
    Storage::fake('local');
    $this->post('/trabaja-con-nosotros', [
        'nombre' => 'Juan', 'telefono' => '0981', 'email' => 'juan@example.com', 'area' => 'Marketing',
        'cv' => UploadedFile::fake()->create('cv.pdf', 300, 'application/pdf'),
    ] + guard('careers'), ['Accept' => 'application/json'])->assertOk();

    $submission = FormSubmission::sole();
    Storage::disk('local')->assertExists($submission->cv_path);
    expect($submission->cv_original_name)->toBe('cv.pdf')
        ->and(Storage::disk('public')->exists($submission->cv_path))->toBeFalse();
});

it('rechaza CV de otro tipo o de más de 5 MB', function (UploadedFile $file) {
    $this->post('/trabaja-con-nosotros', [
        'nombre' => 'Juan', 'telefono' => '0981', 'email' => 'juan@example.com', 'cv' => $file,
    ] + guard('careers'), ['Accept' => 'application/json'])->assertStatus(422)->assertJsonValidationErrors('cv');
})->with([
    fn () => UploadedFile::fake()->create('cv.exe', 10, 'application/octet-stream'),
    fn () => UploadedFile::fake()->create('cv.pdf', 6000, 'application/pdf'),
]);

it('Investor Pass guarda el envío y continúa por WhatsApp sin email', function () {
    $wa = app(WhatsappSettings::class);
    $wa->investor_number = '595981111222';
    $wa->save();

    $response = $this->postJson('/investor-pass', [
        'nombre' => 'John Smith', 'pais' => 'Brasil', 'email' => 'john@example.com', 'telefono' => '+55 11', 'modalidad' => 'Turismo (próximamente)',
    ] + guard('investor'))->assertOk();

    $url = $response->json('whatsapp_url');
    expect($url)->toStartWith('https://wa.me/595981111222?text=')
        ->and(rawurldecode($url))->toContain('soy John Smith (Brasil)')->toContain('modalidad: Turismo');
    expect(FormSubmission::sole()->whatsapp_url)->toBe($url);
    Mail::assertNothingSent();
});

it('Investor Pass sin JavaScript redirige directo a WhatsApp con el número general', function () {
    $this->post('/investor-pass', ['nombre' => 'A', 'pais' => 'B', 'email' => 'a@example.com'] + guard('investor'))
        ->assertRedirectContains('https://wa.me/595994442828?text=');
});

it('la consulta de proyecto queda asociada al proyecto', function () {
    $this->postJson('/proyectos/cumbres/consulta', [
        'nombre' => 'Ana', 'telefono' => '0981', 'email' => 'ana@example.com', 'mensaje' => 'Lote 5',
    ] + guard('project'))->assertOk();

    expect(FormSubmission::sole()->project->slug)->toBe('cumbres');
});
