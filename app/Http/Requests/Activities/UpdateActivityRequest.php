<?php

namespace App\Http\Requests\Activity;

use App\Enums\ActivityStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('activities', 'slug')->ignore($this->route('activity'))],
            'short_description' => ['sometimes', 'string', 'max:500'],
            'content' => ['sometimes', 'string'],
            'category_id' => ['sometimes', 'integer', Rule::exists('activity_categories', 'id')],
            'status' => ['sometimes', Rule::enum(ActivityStatus::class)],
            'cover_image' => ['sometimes', 'image', 'max:2048'],
            'location' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
        ];
    }
}
