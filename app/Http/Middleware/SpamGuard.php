<?php

namespace App\Http\Middleware;

use App\Settings\SpamSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Anti-spam de los formularios públicos: campo trampa, tiempo mínimo de llenado y reCAPTCHA v3.
 * Deja en el request los atributos "recaptcha_score" y "spam_suspect" para el registro.
 */
class SpamGuard
{
    public function __construct(private SpamSettings $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Campo trampa: los bots lo completan. Se responde como si todo hubiera ido bien y no se guarda nada.
        if (filled($request->input('website'))) {
            return $request->expectsJson()
                ? response()->json(['ok' => true])
                : back()->with('form_sent', true);
        }

        try {
            $startedAt = (int) decrypt((string) $request->input('_ts'));
        } catch (Throwable) {
            $startedAt = 0;
        }
        if ($startedAt === 0 || now()->timestamp - $startedAt < $this->settings->min_fill_seconds) {
            $this->fail('No pudimos procesar el envío. Esperá unos segundos e intentá de nuevo.');
        }

        if ($this->settings->recaptcha_enabled && $this->settings->recaptcha_secret) {
            $this->verifyRecaptcha($request);
        }

        return $next($request);
    }

    private function verifyRecaptcha(Request $request): void
    {
        try {
            $result = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->settings->recaptcha_secret,
                'response' => (string) $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ])->throw()->json();
        } catch (Throwable) {
            if (! $this->settings->recaptcha_fail_open) {
                $this->fail('No pudimos verificar el envío. Intentá de nuevo en unos minutos.');
            }
            $request->attributes->set('spam_suspect', true);

            return;
        }

        $score = (float) ($result['score'] ?? 0);
        $request->attributes->set('recaptcha_score', $score);
        $expectedAction = (string) $request->input('_form_action');
        $actionOk = empty($result['action']) || $result['action'] === $expectedAction;
        if (empty($result['success']) || ! $actionOk || $score < $this->settings->recaptcha_min_score) {
            $this->fail('No pudimos verificar el envío. Si el problema persiste, escribinos por WhatsApp.');
        }
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['form' => $message]);
    }
}
