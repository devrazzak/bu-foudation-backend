<?php

namespace App\Http\Requests\ActivityCategory;

use App\Enums\ActivityCategoryStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivityCategoryRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('activity_categories', 'slug')->ignore($this->route('activity_category'))],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::enum(ActivityCategoryStatus::class)],
        ];
    }
}
