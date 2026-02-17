<?php

namespace App\Http\Requests;

use App\Rules\TurnstileRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $turnstileRules = config('turnstile.secret_key')
            ? ['required', 'string', new TurnstileRule]
            : ['nullable'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'cf-turnstile-response' => $turnstileRules,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите имя.',
            'phone.required' => 'Укажите телефон.',
            'email.required' => 'Укажите email.',
            'email.email' => 'Некорректный email.',
            'cf-turnstile-response.required' => 'Пожалуйста, подтвердите, что вы не робот.',
        ];
    }
}
