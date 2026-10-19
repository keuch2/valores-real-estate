<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\SubmissionService;
use App\Settings\MailSettings;
use App\Settings\WhatsappSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public function __construct(private SubmissionService $submissions) {}

    public function contact(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'telefono' => 'nullable|string|max:40',
            'email' => 'required|email:rfc|max:160',
            'mensaje' => 'required|string|max:5000',
        ], [], $this->attributes());
        $this->submissions->store($request, 'contact', $this->fields($data));

        return $this->done($request);
    }

    public function careers(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'telefono' => 'required|string|max:40',
            'email' => 'required|email:rfc|max:160',
            'area' => 'nullable|string|max:120',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'mensaje' => 'nullable|string|max:5000',
        ], ['cv.max' => 'El CV no puede superar los 5 MB.', 'cv.mimes' => 'El CV debe ser PDF o Word.'], $this->attributes());

        $fields = $this->fields($data) + ['area' => $data['area'] ?? null];
        if ($file = $request->file('cv')) {
            // Disco privado: el CV sólo se descarga desde el CMS con sesión iniciada.
            $fields['cv_path'] = $file->storeAs('cvs/'.now()->format('Y/m'), Str::uuid().'.'.$file->extension(), 'local');
            $fields['cv_original_name'] = Str::limit($file->getClientOriginalName(), 250, '');
        }
        $this->submissions->store($request, 'careers', $fields);

        return $this->done($request);
    }

    public function project(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $project = Project::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'telefono' => 'required|string|max:40',
            'email' => 'required|email:rfc|max:160',
            'mensaje' => 'nullable|string|max:5000',
        ], [], $this->attributes());
        $this->submissions->store($request, 'project', $this->fields($data) + ['project_id' => $project->id]);

        return $this->done($request);
    }

    /** Investor Pass: se guarda y se continúa por WhatsApp (sin email, salvo que se active en el CMS). */
    public function investor(Request $request, WhatsappSettings $wa, MailSettings $mail): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'pais' => 'required|string|max:80',
            'email' => 'required|email:rfc|max:160',
            'telefono' => 'nullable|string|max:40',
            'modalidad' => 'nullable|string|max:120',
        ], [], $this->attributes());

        $whatsappUrl = wa_url(fill_template($wa->investor_template, [
            'nombre' => $data['nombre'],
            'pais' => $data['pais'],
            'email' => $data['email'],
            'telefono' => $data['telefono'] ?? '-',
            'modalidad' => $data['modalidad'] ?? '-',
        ]), 'investor');

        $this->submissions->store($request, 'investor', $this->fields($data) + [
            'country' => $data['pais'],
            'modality' => $data['modalidad'] ?? null,
            'whatsapp_url' => $whatsappUrl,
        ], notify: $mail->notify_investor_by_email);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'whatsapp_url' => $whatsappUrl]);
        }

        return redirect()->away($whatsappUrl);
    }

    private function fields(array $data): array
    {
        return [
            'name' => $data['nombre'],
            'email' => $data['email'] ?? null,
            'phone' => $data['telefono'] ?? null,
            'message' => $data['mensaje'] ?? null,
        ];
    }

    private function attributes(): array
    {
        return ['nombre' => 'nombre', 'telefono' => 'teléfono', 'pais' => 'país', 'mensaje' => 'mensaje', 'cv' => 'CV'];
    }

    private function done(Request $request): JsonResponse|RedirectResponse
    {
        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : back()->with('form_sent', true);
    }
}
