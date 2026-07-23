<?php

namespace Botble\RealEstate\Services;

use Botble\ACL\Models\User;
use Botble\Base\Events\CreatedContentEvent;
use Botble\RealEstate\Concerns\ChunkImportable;
use Botble\RealEstate\Contracts\ChunkImportable as ChunkImportableContract;
use Botble\RealEstate\Models\Project;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImportProjectService implements ChunkImportableContract
{
    use ChunkImportable;

    public function save(array $row): void
    {
        $project = new Project();
        $itemData = apply_filters('real_estate_projects_import_row_data_for_saving', Arr::except($row, ['categories', 'facilities', 'features', 'custom_fields']), $row);

        $project->forceFill($itemData);
        $project->save();

        $project->categories()->sync(Arr::get($row, 'categories', []));
        $project->features()->sync(Arr::get($row, 'features', []));

        foreach (Arr::get($row, 'facilities', []) as $facilityId => $facilityValue) {
            $project->facilities()->attach($facilityId, ['distance' => $facilityValue]);
        }

        if ($customFields = Arr::get($row, 'custom_fields')) {
            $project->customFields()->save($customFields);
        }

        $this->request->merge(array_merge($row, [
            'slug' => Str::slug($project->name),
            'is_slug_editable' => true,
        ]));

        event(new CreatedContentEvent(PROJECT_MODULE_SCREEN_NAME, $this->request, $project));
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

        $data = apply_filters('real_estate_projects_import_row_data', [
            'name' => Arr::get($row, 'name'),
            'description' => Arr::get($row, 'description'),
            'content' => Arr::get($row, 'content'),
            'images' => $this->getImageURLs($images),
            'location' => Arr::get($row, 'location'),
            'number_block' => Arr::get($row, 'number_block'),
            'number_floor' => Arr::get($row, 'number_floor'),
            'number_flat' => Arr::get($row, 'number_flat'),
            'is_featured' => $this->yesNoToBoolean(Arr::get($row, 'is_featured', false)),
            'date_finish' => Arr::get($row, 'date_finish'),
            'date_sell' => Arr::get($row, 'date_sell'),
            'price_from' => Arr::get($row, 'price_from'),
            'price_to' => Arr::get($row, 'price_to'),
            'author_id' => Arr::get($row, 'author_id') ?: 0,
            'author_type' => Arr::get($row, 'author_type') ?: User::class,
            'longitude' => Arr::get($row, 'longitude'),
            'latitude' => Arr::get($row, 'latitude'),
            'status' => Arr::get($row, 'status'),
            'facilities' => $dataFacilities,
        ]);

        return $this->mapRelationships($row, $data);
    }

    public function mapRelationships(mixed $row, array $data): array
    {
        $data['country_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'country'), $this->countryRepository));
        $data['state_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'state'), $this->stateRepository));
        $data['city_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'city'), $this->cityRepository));
        $data['author_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'author_id'), $this->accountRepository, ));
        $data['currency_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'currency'), $this->currencyRepository, 'title'));
        $data['investor_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'investor_id'), $this->investorRepository));
        $data['categories'] = $this->getIdsFromString(Arr::get($row, 'categories'), $this->categoryRepository);
        $data['features'] = $this->getIdsFromString(Arr::get($row, 'features'), $this->featureRepository);

        if ($customFields = Arr::get($row, 'custom_fields')) {
            $data['custom_fields'] = $this->customFieldValue->find($customFields);
        }

        return $data;
    }
}
