<?php

namespace Botble\RealEstate\Services;

use App\Models\User;
use Botble\Base\Events\CreatedContentEvent;
use Botble\RealEstate\Concerns\ChunkImportable;
use Botble\RealEstate\Contracts\ChunkImportable as ChunkImportableContract;
use Botble\RealEstate\Models\Property;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImportPropertyService implements ChunkImportableContract
{
    use ChunkImportable;

    public function save(array $row): void
    {
        $property = new Property();

        $itemData = apply_filters('real_estate_properties_import_row_data_for_saving', Arr::except($row, ['categories', 'facilities', 'features', 'custom_fields']), $row);

        $property->forceFill($itemData);
        $property->save();

        $property->categories()->sync(Arr::get($row, 'categories', []));

        foreach (Arr::get($row, 'facilities', []) as $facilityId => $facilityValue) {
            $property->facilities()->attach($facilityId, ['distance' => $facilityValue]);
        }

        $property->features()->sync(Arr::get($row, 'features', []));

        if ($customFields = Arr::get($row, 'custom_fields')) {
            $property->customFields()->save($customFields);
        }

        $this->request->merge([...$row,
            'slug' => Str::slug($property->name),
            'is_slug_editable' => true,
        ]);

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $this->request, $property));
    }

    public function map($row): array
    {
        $dataFacilities = [];

        if (! empty($row['facilities'])) {
            $facilities = explode(',', Arr::get($row, 'facilities', ''));
            foreach ($facilities as $facility) {
                $facilityExplode = explode(':', $facility);
                $dataFacilities[Arr::first($this->getIdsFromString($facilityExplode[0], $this->facilityRepository))] = $facilityExplode[1];
            }
        }

        $images = explode(',', Arr::get($row, 'images', ''));

        $property = apply_filters('real_estate_properties_import_row_data', [
            'name' => Arr::get($row, 'name'),
            'type' => Arr::get($row, 'type'),
            'description' => Arr::get($row, 'description'),
            'price' => Arr::get($row, 'price'),
            'number_bedroom' => Arr::get($row, 'number_bedroom'),
            'number_bathroom' => Arr::get($row, 'number_bathroom'),
            'number_floor' => Arr::get($row, 'number_floor'),
            'square' => Arr::get($row, 'square'),
            'images' => $this->getImageURLs($images),
            'author_id' => Arr::get($row, 'author_id') ?: 0,
            'author_type' => Arr::get($row, 'author_type') ?: User::class,
            'is_featured' => $this->yesNoToBoolean(Arr::get($row, 'is_featured', false)),
            'content' => Arr::get($row, 'content'),
            'location' => Arr::get($row, 'location'),
            'longitude' => Arr::get($row, 'longitude'),
            'latitude' => Arr::get($row, 'latitude'),
            'auto_renew' => $this->yesNoToBoolean(Arr::get($row, 'auto_renew', false)),
            'expire_date' => Arr::get($row, 'expire_date'),
            'never_expired' => $this->yesNoToBoolean(Arr::get($row, 'never_expired', false)),
            'period' => Arr::get($row, 'period'),
            'moderation_status' => Arr::get($row, 'moderation_status'),
            'status' => Arr::get($row, 'status'),
            'project_id' => Arr::get($row, 'project') ?: 0,
            'facilities' => $dataFacilities,
        ], $row);

        return $this->mapRelationships($row, $property);
    }
}
