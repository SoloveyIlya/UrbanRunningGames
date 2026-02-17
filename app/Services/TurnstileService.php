<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    public function __construct(
        protected string $secretKey,
        protected string $siteverifyUrl
    ) {}

    public static function fromConfig(): ?self
    {
        $secret = config('turnstile.secret_key');
        if ($secret === null || $secret === '') {
            return null;
        }
        return new self($secret, config('turnstile.siteverify_url'));
    }

    /**
     * Верификация токена через Cloudflare Siteverify API.
     *
     * @return array{success: bool, 'error-codes'?: array<string>}
     */
    public function verify(string $token, ?string $remoteIp = null): array
    {
        $token = trim($token);
        if ($token === '') {
            return ['success' => false, 'error-codes' => ['missing-input-response']];
        }

        // Тестовые ключи Cloudflare: dummy-токен содержит "DUMMY", принимаем локально без вызова API
        $isTestSecret = str_starts_with($this->secretKey, '1x0000000000000000000000000000000AA');
        if ($isTestSecret && str_contains($token, 'DUMMY')) {
            return ['success' => true];
        }

        $payload = [
            'secret' => $this->secretKey,
            'response' => $token,
        ];
        $isLocal = in_array($remoteIp, ['127.0.0.1', '::1', 'localhost'], true);
        if ($remoteIp !== null && $remoteIp !== '' && ! $isLocal) {
            $payload['remoteip'] = $remoteIp;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->withOptions(['verify' => config('app.env') === 'production'])
                ->post($this->siteverifyUrl, $payload);

            $body = $response->json();
            if (! is_array($body)) {
                return ['success' => false, 'error-codes' => ['internal-error']];
            }
            return $body;
        } catch (\Throwable $e) {
            report($e);
            return ['success' => false, 'error-codes' => ['internal-error']];
        }
    }
}
