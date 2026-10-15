<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => new BlogCategoryResource($this->whenLoaded('category')),
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => [
                'label' => $this->status->label(),
                'value' => $this->status->value,
            ],
            'short_description' => $this->short_description,
            'content' => $this->content,
            'image' => $this->image,
            'published_at' => $this->published_at,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
