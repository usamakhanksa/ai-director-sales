<?php

namespace Theme\Resido\Http\Resources;

use Botble\Media\Facades\RvMedia;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'content' => clean($this->content),
            'company' => $this->company,
            'image' => RvMedia::getImageUrl($this->image, null, false, RvMedia::getDefaultImage()),
        ];
    }
}
