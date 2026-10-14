<?php

namespace App\Http\Requests\ActivityCategory;

use App\Enums\ActivityCategoryStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityCategoryRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('activity_categories', 'slug')],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(ActivityCategoryStatus::class)],
        ];
    }
}
