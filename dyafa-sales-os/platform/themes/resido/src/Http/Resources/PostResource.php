<?php

namespace Theme\Resido\Http\Resources;

use Botble\Media\Facades\RvMedia;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'description' => Str::words($this->description, 35),
            'image' => $this->image ? RvMedia::getImageUrl($this->image, 'medium', false, RvMedia::getDefaultImage()) : null,
            'created_at' => $this->created_at->format('d M, Y'),
            'views' => number_format($this->views),
            'categories' => CategoryResource::collection($this->categories),
        ];
    }
}
