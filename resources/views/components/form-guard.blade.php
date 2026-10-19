@props(['action'])
@csrf
<input type="hidden" name="_form_action" value="{{ $action }}">
<input type="hidden" name="_ts" value="{{ encrypt(now()->timestamp) }}">
<input type="hidden" name="g-recaptcha-response" value="">
<div class="hp-field" aria-hidden="true"><label>Sitio web<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
