<?php

namespace Botble\RealEstate\Http\Requests\Settings;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class GeneralSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'real_estate_square_unit' => ['nullable', 'string', 'in:m²,ft2,yd2'],
            'real_estate_display_views_count_in_detail_page' => $onOffRule = new OnOffRule(),
            'real_estate_hide_properties_in_statuses' => ['nullable', 'array'],
            'real_estate_hide_properties_in_statuses.*' => ['string'],
            'real_estate_hide_projects_in_statuses' => ['nullable', 'array'],
            'real_estate_hide_projects_in_statuses.*' => ['string'],
            'real_estate_enable_review_feature' => $onOffRule,
            'real_estate_reviews_per_page' => ['nullable', 'numeric'],
            'real_estate_enabled_custom_fields_feature' => $onOffRule,
            'real_estate_mandatory_fields_at_consult_form' => ['nullable', 'array'],
            'real_estate_mandatory_fields_at_consult_form.*' => ['string'],
            'real_estate_hide_fields_at_consult_form' => ['nullable', 'array'],
            'real_estate_hide_fields_at_consult_form.*' => ['string'],
            'real_estate_enabled_projects' => $onOffRule,
            'real_estate_enabled_property_types' => ['nullable', 'array'],
            'real_estate_enabled_property_types.*' => ['string'],
        ];
    }
}
