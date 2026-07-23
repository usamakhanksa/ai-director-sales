<?php

namespace Theme\Resido\Http\Resources;

use Botble\Media\Facades\RvMedia;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user_name' => $this->author->full_name,
            'user_avatar' => $this->author->avatar->url ? RvMedia::getImageUrl($this->author->avatar->url, 'thumb') : $this->author->avatar_url,
            'created_at' => $this->created_at->format('d M, Y'),
            'comment' => $this->comment,
            'star' => $this->star,
        ];
    }
}
