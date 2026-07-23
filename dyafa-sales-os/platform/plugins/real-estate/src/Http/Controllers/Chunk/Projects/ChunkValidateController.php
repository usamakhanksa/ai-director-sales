<?php

namespace Botble\RealEstate\Http\Controllers\Chunk\Projects;

use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Http\Controllers\Chunk\ChunkController;
use Botble\RealEstate\Http\Requests\ChunkFileRequest;
use Exception;
use Illuminate\Validation\Rule;

class ChunkValidateController extends ChunkController
{
    public function __invoke(ChunkFileRequest $request)
    {
        try {
            $filePath = $this->getFilePath($request->input('file'), 'app/project-import');

        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }

        $offset = $request->integer('offset');
        $limit = $request->integer('limit', 10);
        $rows = $this->getLocationRows($filePath, $offset, $limit);

        $rules = [
            '*.name' => 'required|string|max:120',
            '*.description' => 'nullable|string|max:400',
            '*.content' => 'required|string',
            '*.number_block' => 'numeric|min:0|max:100000|nullable',
            '*.number_floor' => 'numeric|min:0|max:100000|nullable',
            '*.number_flat' => 'numeric|min:0|max:100000|nullable',
            '*.price_from' => 'numeric|min:0|nullable',
            '*.price_to' => 'numeric|min:0|nullable',
            '*.latitude' => ['max:20', 'nullable', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            '*.longitude' => [
                'max:20',
                'nullable',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],
            '*.status' => Rule::in(ProjectStatusEnum::values()),
            '*.date_finish' => 'nullable|date',
            '*.date_sell' => 'nullable|date',
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
