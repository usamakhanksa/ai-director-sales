<?php

namespace Botble\RealEstate\Forms\Fronts;

use Botble\RealEstate\Forms\AccountForm;
use Botble\RealEstate\Forms\Fronts\Auth\Concerns\HasSubmitButton;
use Botble\RealEstate\Http\Requests\SettingRequest;

class ProfileForm extends AccountForm
{
    use HasSubmitButton;

    public function setup(): void
    {
        parent::setup();

        $this
            ->setValidatorClass(SettingRequest::class)
            ->contentOnly()
            ->modify('description', 'textarea', [
                'attr' => [
                    'rows' => 3,
                ],
            ])
            ->modify('email', 'text', [
                'required' => false,
                'attr' => [
                    'disabled' => true,
                ],
            ], true)
            ->remove(['is_change_password', 'password', 'password_confirmation', 'avatar_image', 'is_featured', 'is_public_profile'])
            ->addAfter('dob', 'gender', 'select', [
                'label' => trans('plugins/real-estate::dashboard.gender'),
                'choices' => [
                    'male' => trans('plugins/real-estate::dashboard.gender_male'),
                    'female' => trans('plugins/real-estate::dashboard.gender_female'),
                    'other' => trans('plugins/real-estate::dashboard.gender_other'),
                ],
            ])
            ->submitButton(trans('plugins/real-estate::dashboard.save'), isWrapped: false, attributes: [
                'class' => 'btn btn-primary',
            ]);
    }
}
