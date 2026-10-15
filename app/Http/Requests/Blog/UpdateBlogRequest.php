<?php

namespace App\Http\Requests\Blog;

use App\Enums\BlogStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogRequest extends FormRequest
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
            'category_id' => ['sometimes', 'integer', Rule::exists('blog_categories', 'id')],
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('blogs', 'slug')->ignore($this->route('blog'))],
            'status' => ['sometimes', Rule::enum(BlogStatus::class)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'published_at' => ['nullable', 'dateTime'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ];
    }
}
