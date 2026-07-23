<?php

namespace Theme\Resido\Http\Resources;

use Botble\Theme\Facades\Theme;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyHTMLResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'HTML' => Theme::partial('real-estate.properties.item-grid', ['property' => $this, 'lazyload' => false]),
        ];
    }
}
