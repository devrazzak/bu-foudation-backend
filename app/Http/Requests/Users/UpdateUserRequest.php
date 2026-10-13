<?php

namespace App\Http\Requests\Users;

use App\Enums\UserStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'required', Rule::enum(UserStatus::class)],
            'terms_accepted' => ['sometimes', 'boolean'],
        ];
    }
}
