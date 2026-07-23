<?php

namespace Botble\RealEstate\Http\Controllers\Chunk\Properties;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Http\Controllers\Chunk\ChunkController;
use Botble\RealEstate\Http\Requests\ChunkFileRequest;
use Exception;
use Illuminate\Validation\Rule;

class ChunkValidateController extends ChunkController
{
    public function __invoke(ChunkFileRequest $request)
    {
        try {
            $filePath = $this->getFilePath($request->input('file'), 'app/property-import');

        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }

        $offset = $request->integer('offset');
        $limit = $request->integer('limit', 10);
        $rows = $this->getLocationRows($filePath, $offset, $limit);
        $rowsCount = count($rows);

        $rules = [
            '*.name' => 'required|string|max:220',
            '*.description' => 'nullable|string|max:400',
            '*.number_bedroom' => 'numeric|min:0|max:100000|nullable',
            '*.number_bathroom' => 'numeric|min:0|max:100000|nullable',
            '*.number_floor' => 'numeric|min:0|max:100000|nullable',
            '*.price' => 'numeric|min:0|nullable',
            '*.latitude' => ['max:20', 'nullable', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            '*.longitude' => [
                'max:20',
                'nullable',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],
            '*.status' => Rule::in(PropertyStatusEnum::values()),
            '*.moderation_status' => Rule::in(ModerationStatusEnum::values()),
            '*.custom_fields.*.name' => ['required', 'string', 'max:255'],
            '*.custom_fields.*.value' => ['required', 'string', 'max:255'],
        ];

        $failed = $this->validator($rows, $rules);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/real-estate::import.validating_message', [
                'from' => number_format($offset),
                'to' => number_format($offset + count($rows)),
            ]))
            ->setData([
                'offset' => $offset + count($rows),
                'count' => count($rows),
                'failed' => $failed,
            ]);
    }
}
