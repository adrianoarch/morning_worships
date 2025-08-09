<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'receives_email_notification' => ['nullable', 'boolean'],
            'phone' => [
                'nullable',
                'string',
                'min:11',
                'max:15',
            ],
            'timezone' => ['nullable', 'timezone'],
            'language' => ['nullable', 'string', 'max:10'],
        ];
    }

    /**
     * Implement the authorize method to allow the request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.min' => 'O telefone deve conter pelo menos 11 números.',
        ];
    }
}
