<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'terms_accepted' => ['nullable', 'boolean'],
        ];
    }
}
