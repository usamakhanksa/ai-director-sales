<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\FeatureRequest;
use Botble\RealEstate\Models\Feature;

class FeatureForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Feature())
            ->setValidatorClass(FeatureRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('icon', 'text', [
                'label' => trans('plugins/real-estate::feature.form.icon'),
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::feature.form.icon'),
                    'data-counter' => 60,
                ],
                'default_value' => 'fas fa-check',
            ]);
    }
}
