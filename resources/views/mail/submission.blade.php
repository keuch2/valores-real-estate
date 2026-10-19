<x-mail::message>
# Nuevo mensaje: {{ $type }}

<x-mail::table>
| | |
|:--|:--|
@foreach ($rows as $label => $value)
| **{{ $label }}** | {{ $value }} |
@endforeach
</x-mail::table>

@if ($submission->message)
**Mensaje**

{{ $submission->message }}
@endif

<x-mail::button :url="$url">
Ver en el CMS
</x-mail::button>

Podés responder este email para contestarle directamente.
</x-mail::message>
