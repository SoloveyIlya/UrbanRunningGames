<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'topic' => ['required', 'in:participation,merch,partnership,other'],
            'phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
            'consent' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Пожалуйста, укажите ваше имя',
            'topic.required' => 'Пожалуйста, выберите тему обращения',
            'message.required' => 'Пожалуйста, напишите ваше сообщение',
            'message.min' => 'Сообщение должно содержать минимум 10 символов',
            'email.email' => 'Пожалуйста, укажите корректный email адрес',
            'consent.required' => 'Необходимо согласие на обработку персональных данных',
            'consent.accepted' => 'Необходимо согласие на обработку персональных данных',
        ];
    }
}
