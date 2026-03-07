<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TurnstileRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = TurnstileService::fromConfig();
        if ($service === null) {
            return; // ключи не заданы — пропускаем проверку
        }

        $token = is_string($value) ? trim($value) : '';
        if ($token === '') {
            $fail('Пожалуйста, подтвердите, что вы не робот.');
            return;
        }

        $remoteIp = request()->header('CF-Connecting-IP')
            ?: request()->header('X-Forwarded-For')
            ?: request()->ip();

        $result = $service->verify($token, $remoteIp);

        if (! ($result['success'] ?? false)) {
            $codes = $result['error-codes'] ?? ['invalid-input-response'];
            $fail('Проверка не пройдена. Обновите страницу и попробуйте снова.');
        }
    }
}
