<?php

namespace Theme\Resido\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray($request): array
    {
        $state_name = empty($this->state) ? '' : (', ' . $this->state->name);

        return [
            'id' => $this->id,
            'name' => $this->name . ($request->input('only_city_name') ? '' : $state_name),
        ];
    }
}
