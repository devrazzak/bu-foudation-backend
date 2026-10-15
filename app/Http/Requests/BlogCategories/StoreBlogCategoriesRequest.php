<?php

namespace App\Http\Requests\BlogCategories;

use App\Enums\BlogCategoryStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlogCategoriesRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('blog_categories', 'slug')],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(BlogCategoryStatus::class)],
        ];
    }
}
