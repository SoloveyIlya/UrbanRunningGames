@php
    $siteKey = config('turnstile.site_key');
@endphp
@if($siteKey)
    <div class="form-group turnstile-wrap">
        <div class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-theme="light"></div>
        @error('cf-turnstile-response')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>
@endif
