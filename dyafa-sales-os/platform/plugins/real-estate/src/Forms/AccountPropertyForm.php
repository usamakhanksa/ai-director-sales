<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fields\CustomEditorField;
use Botble\RealEstate\Forms\Fields\MultipleUploadField;
use Botble\RealEstate\Http\Requests\AccountPropertyRequest;
use Botble\RealEstate\Models\Property;

class AccountPropertyForm extends PropertyForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::addScriptsDirectly('vendor/core/core/base/libraries/tinymce/tinymce.min.js');

        if (! $this->formHelper->hasCustomField('customEditor')) {
            $this->formHelper->addCustomField('customEditor', CustomEditorField::class);
        }

        if (! $this->formHelper->hasCustomField('multipleUpload')) {
            $this->formHelper->addCustomField('multipleUpload', MultipleUploadField::class);
        }

        $this
            ->setupModel(new Property())
            ->setFormOption('template', 'plugins/real-estate::account.forms.base')
            ->hasFiles()
            ->setValidatorClass(AccountPropertyRequest::class)
            ->remove('is_featured')
            ->remove('project_id')
            ->remove('moderation_status')
            ->remove('content')
            ->remove('images[]')
            ->remove('never_expired')
            ->modify('auto_renew', 'onOff', [
                'label' => trans('plugins/real-estate::property.renew_notice', ['days' => RealEstateHelper::propertyExpiredDays()]),
                'default_value' => false,
            ], true)
            ->remove('author_id')
            ->addAfter('description', 'content', 'customEditor', ContentFieldOption::make()->required()->toArray())
            ->addAfter('content', 'images', 'multipleUpload', [
                'label' => trans('plugins/real-estate::account-property.images', ['max' => RealEstateHelper::maxPropertyImagesUploadByAgent()]),
            ]);
    }
}
