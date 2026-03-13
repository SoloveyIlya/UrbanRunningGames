<?php

namespace App\Http\Requests;

use App\Rules\TurnstileRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\pL\pM\s\-\.]+$/u',
            ],
            'phone' => [
                'required',
                'string',
                'max:50',
                'regex:/^[\d\s\+\-\(\)]{10,50}$/',
            ],
            'email' => ['required', 'email', 'max:255'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'cf-turnstile-response' => $turnstileRules,
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $phone = $this->input('phone');
            if (is_string($phone)) {
                $digits = preg_replace('/\D/', '', $phone);
                $len = strlen($digits);
                if ($len > 0 && $len < 10) {
                    $validator->errors()->add('phone', 'Укажите корректный номер телефона (не менее 10 цифр).');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите имя.',
            'name.min' => 'Имя должно содержать не менее 2 символов.',
            'name.regex' => 'Имя может содержать только буквы, пробелы, дефис и точку.',
            'phone.required' => 'Укажите телефон.',
            'phone.regex' => 'Укажите корректный номер телефона (например, +7 999 123-45-67).',
            'email.required' => 'Укажите email.',
            'email.email' => 'Укажите корректный email.',
            'email.max' => 'Email не должен превышать 255 символов.',
            'comment.max' => 'Комментарий не должен превышать 2000 символов.',
            'cf-turnstile-response.required' => 'Пожалуйста, подтвердите, что вы не робот.',
        ];
    }
}
