<?php

use Botble\Base\Facades\Form;
use Botble\Base\Facades\MacroableModels;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Models\BaseModel;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Location\Models\City;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Botble\Location\Repositories\Interfaces\StateInterface;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\Review;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\Youtube;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Theme\Resido\Forms\Fields\ThemeIconField;
use Theme\Resido\Http\Requests\CityRequest;

register_page_template([
    'homepage' => __('Homepage'),
]);

register_sidebar([
    'id' => 'footer_sidebar_1',
    'name' => __('Footer sidebar 1'),
    'description' => __('Footer sidebar 1 for Resido theme'),
]);

register_sidebar([
    'id' => 'footer_sidebar_2',
    'name' => __('Footer sidebar 2'),
    'description' => __('Footer sidebar 2 for Resido theme'),
]);

register_sidebar([
    'id' => 'footer_sidebar_3',
    'name' => __('Footer sidebar 3'),
    'description' => __('Footer sidebar 3 for Resido theme'),
]);

register_sidebar([
    'id' => 'property_sidebar',
    'name' => __('Property sidebar'),
    'description' => __('Property sidebar for Resido theme'),
]);

RvMedia::setUploadPathAndURLToPublic();
RvMedia::addSize('large', 1024, 0)
    ->addSize('medium_large', 640, 0)
    ->addSize('property_large', 0, 610)
    ->addSize('medium', 400, 0);

if (! function_exists('get_repeat_field')) {
    function get_repeat_field($fieldName): array
    {
        $field = json_decode(theme_option($fieldName), true);

        return is_array($field) ? $field : [];
    }
}

if (! function_exists('get_featured_properties')) {
    function get_featured_properties($limit)
    {
        $withCount = [];
        if (RealEstateHelper::isEnabledReview()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }

        return app(PropertyInterface::class)->getPropertiesByConditions(
            [
                're_properties.is_featured' => true,
                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            (int) $limit,
            config('plugins.real-estate.real-estate.properties.relations', []),
            $withCount
        );
    }
}

if (! function_exists('get_projects_featured')) {
    /**
     * @return object
     */
    function get_projects_featured($limit = 6): object
    {
        $projects = app(ProjectInterface::class)->advancedGet(
            [
                'condition' =>
                [
                    'is_featured' => true,
                ],
                'take' => $limit,
            ]
        );

        return $projects;
    }
}

function get_object_property_map(): object
{
    return (object) [
        'name' => '__name__',
        'type' => '__type__',
        'url' => '__url__',
        'city_name' => '__city_name__',
        'square_text' => '__square_text__',
        'number_bedroom' => '__number_bedroom__',
        'number_bathroom' => '__number_bathroom__',
        'image_thumb' => '__image_thumb__',
        'price_html' => '__price_html__',
    ];
}

app()->booted(function () {
    if (is_plugin_active('real-estate')) {
        $videoSupportModels = [Property::class, Project::class];
        add_action(BASE_ACTION_META_BOXES, function ($context, $object) use ($videoSupportModels) {
            if (in_array(get_class($object), $videoSupportModels) && $context == 'advanced') {
                MetaBox::addMetaBox('additional_property_fields', __('Addition Information'), function () {
                    $videoThumbnail = null;
                    $videoUrl = null;
                    $args = func_get_args();
                    if (! empty($args[0])) {
                        $videoThumbnail = $args[0]->video_thumbnail;
                        $videoUrl = $args[0]->video_url;
                    }

                    return Theme::partial('additional-property-fields', compact('videoThumbnail', 'videoUrl'));
                }, get_class($object), $context);
            }
        }, 28, 2);

        add_action(BASE_ACTION_AFTER_CREATE_CONTENT, function ($type, $request, $object) use ($videoSupportModels) {
            if (in_array(get_class($object), $videoSupportModels) && $request->has('video')) {
                $data = Arr::only((array) $request->input('video', []), ['url']);
                if (! empty($request->input('video_thumbnail'))) {
                    $data['thumbnail'] = $request->input('video_thumbnail');
                }

                if ($request->hasFile('thumbnail_input')) {
                    $result = RvMedia::handleUpload($request->file('thumbnail_input'), 0, 'properties');
                    if (! $result['error']) {
                        $file = $result['data'];
                        $data['thumbnail'] = $file->url;
                    }
                }

                MetaBox::saveMetaBoxData($object, 'video', $data);
            }
        }, 280, 3);

        add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, function ($type, $request, $object) use ($videoSupportModels) {
            if (in_array(get_class($object), $videoSupportModels) && $request->has('video')) {
                $data = Arr::only((array) $request->input('video', []), ['url']);
                if (! empty($request->input('video_thumbnail'))) {
                    $data['thumbnail'] = $request->input('video_thumbnail');
                }

                if ($request->hasFile('thumbnail_input')) {
                    $result = RvMedia::handleUpload($request->file('thumbnail_input'), 0, 'properties');
                    if (! $result['error']) {
                        $file = $result['data'];
                        $data['thumbnail'] = $file->url;
                    }
                }

                MetaBox::saveMetaBoxData($object, 'video', $data);
            }
        }, 281, 3);

        // yes or no is okay
        add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, function ($screen, $object) use ($videoSupportModels) {
            if (in_array(get_class($object), $videoSupportModels)) {
                $object->loadMissing(['meta_boxes']);
            }
        }, 56, 2);

        foreach ($videoSupportModels as $supportModel) {
            $supportModel::resolveRelationUsing('meta_boxes', function ($model) {
                return $model->morphMany(MetaBoxModel::class, 'reference')
                    ->select(['reference_id', 'meta_key', 'meta_value']);
            });

            MacroableModels::addMacro($supportModel, 'getVideoThumbnailAttribute', function () {
                /**
                 * @var BaseModel $this
                 */
                if ($this->meta_boxes) {
                    $fistMeta = $this->meta_boxes->firstWhere('meta_key', 'video');

                    if ($fistMeta) {
                        return Arr::get(Arr::first($fistMeta->meta_value), 'thumbnail');
                    }
                }

                return '';
            });

            MacroableModels::addMacro($supportModel, 'getVideoUrlAttribute', function () {
                /**
                 * @var BaseModel $this
                 */
                if ($this->meta_boxes) {
                    $fistMeta = $this->meta_boxes->firstWhere('meta_key', 'video');

                    if ($fistMeta) {
                        $url = Arr::get(Arr::first($fistMeta->meta_value), 'url');

                        if ($url) {
                            return Youtube::getYoutubeWatchURL($url);
                        }
                    }
                }

                return '';
            });
        }

        add_action(BASE_ACTION_META_BOXES, 'add_addition_fields_in_property_screen', 30, 3);

        function add_addition_fields_in_property_screen($context, $object): void
        {
            if (get_class($object) == Property::class && $context == 'top') {
                MetaBox::addMetaBox(
                    'additional_property_fields',
                    __('Custom layout'),
                    function () {
                        $headerLayout = null;
                        $args = func_get_args();
                        if (! empty($args[0])) {
                            $headerLayout = MetaBox::getMetaData($args[0], 'header_layout', true);
                        }

                        return Theme::partial('additional-property-fields-top', compact('headerLayout'));
                    },
                    get_class($object),
                    $context
                );
            }
        }

        add_action(BASE_ACTION_AFTER_CREATE_CONTENT, 'save_addition_property_fields', 230, 3);
        add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, 'save_addition_property_fields', 231, 3);

        function save_addition_property_fields($type, $request, $object): void
        {
            if (get_class($object) == Property::class && $request->has('header_layout')) {
                MetaBox::saveMetaBoxData($object, 'header_layout', $request->input('header_layout'));
            }
        }

        add_action([BASE_ACTION_AFTER_CREATE_CONTENT, BASE_ACTION_AFTER_UPDATE_CONTENT], function ($type, $request, $object) {
            if (get_class($object) == Review::class) {
                $meta = $request->input('meta');
                save_meta_data($object, 'review_meta', json_encode($meta));
                calculateReviewMetaData($meta, $object->reviewable_id);
            }
        }, 120, 3);
    }

    if (setting('social_login_enable', false)) {
        remove_filter(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM);

        add_filter(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, function ($html) {
            if (Route::currentRouteName() == 'access.login') {
                if (defined('THEME_OPTIONS_MODULE_SCREEN_NAME')) {
                    Theme::asset()
                        ->usePath(false)
                        ->add(
                            'social-login-css',
                            asset('vendor/core/plugins/social-login/css/social-login.css'),
                            [],
                            [],
                            '1.0.0'
                        );
                }

                return $html . view('plugins/social-login::login-options')->render();
            }

            return $html . Theme::partial('login-options');
        }, 25);
    }
});

function get_image_from_video_property(BaseModel $object): ?string
{
    $video = $object->getMetaData('video', true);
    $videoUrl = $video['url'] ?? '';
    $videoThumbnail = $video['thumbnail'] ?? '';

    if ($videoThumbnail) {
        return RvMedia::getImageUrl($videoThumbnail);
    }

    $videoID = Youtube::getYoutubeVideoID($videoUrl);

    if ($videoID) {
        return 'https://img.youtube.com/vi/' . $videoID . '/hqdefault.jpg';
    }

    return RvMedia::getDefaultImage();
}

if (is_plugin_active('location')) {
    SeoHelper::registerModule(City::class);

    add_filter(BASE_FILTER_BEFORE_RENDER_FORM, 'add_addition_fields_into_form', 127, 2);

    function add_addition_fields_into_form($form, $data)
    {
        switch (get_class($data)) {
            case City::class:
                $image = MetaBox::getMetaData($data, 'image', true);
                $form
                    ->setValidatorClass(CityRequest::class)
                    ->addAfter('status', 'image', 'mediaImage', [
                        'label' => trans('core/base::forms.image'),
                        'label_attr' => ['class' => 'control-label'],
                        'value' => $image,
                    ]);

                break;
            case Facility::class:
            case Feature::class:
                $form
                    ->addCustomField('themeIcon', ThemeIconField::class)
                    ->modify('icon', 'themeIcon', ['label' => __('Icon')], true);

                break;
        }

        return $form;
    }

    add_action(BASE_ACTION_AFTER_CREATE_CONTENT, function ($type, $request, $object) {
        if (get_class($object) == City::class && $request->has('image')) {
            MetaBox::saveMetaBoxData($object, 'image', $request->input('image'));
        }
    }, 230, 3);

    add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, function ($type, $request, $object) {
        if (get_class($object) == City::class && $request->has('image')) {
            MetaBox::saveMetaBoxData($object, 'image', $request->input('image'));
        }
    }, 231, 3);

    City::resolveRelationUsing('properties', function ($model) {
        return $model->hasMany(Property::class);
    });
}

if (! function_exists('get_single_header_templates')) {
    function get_single_header_layout(): array
    {
        return [
            'layout-1' => __('Layout 1'),
            'layout-2' => __('Layout 2'),
            'layout-3' => __('Layout 3'),
        ];
    }
}

if (! function_exists('get_properties_page_layout')) {
    function get_properties_page_layout(): array
    {
        return [
            'sidebar' => __('Left sidebar'),
            'full' => __('Full width'),
            'map' => __('Full map'),
            'half_map' => __('Half map'),
            'grid_map' => __('Grid map'),
            'grid_full' => __('Grid full'),
        ];
    }
}

if (! function_exists('get_feature_all')) {
    function get_feature_all(): array
    {
        return app(FeatureInterface::class)->allBy([], [], ['re_features.id', 're_features.name'])->toArray();
    }
}

if (! function_exists('get_properties_featured')) {
    function get_properties_featured($limit = 6, $type = ''): object
    {
        $params = [
            'condition' => [
                'is_featured' => true,
                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'take' => $limit,
            'with' => RealEstateHelper::getPropertyRelationsQuery(),
            'order_by' => [
                'updated_at' => 'DESC',
            ],
        ];

        if ($type != '') {
            $params['condition']['type'] = $type;
        }

        $withCount = [];

        if (RealEstateHelper::isEnabledReview()) {
            $withCount = [
                'reviews',
            ];
        }
        $params['withCount'] = $withCount;

        return app(PropertyInterface::class)->advancedGet($params + RealEstateHelper::getReviewExtraData());
    }
}

if (! function_exists('get_properties_by_project')) {
    function get_properties_by_project($project_id, $limit = 3, $type = ''): object
    {
        $params = [
            'condition' => [
                'project_id' => $project_id,
                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'take' => $limit,
            'order_by' => [
                'updated_at' => 'DESC',
            ],
        ];

        if ($type != '') {
            $params['condition']['type'] = $type;
        }

        $withCount = [];
        if (RealEstateHelper::isEnabledReview()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }
        $params['withCount'] = $withCount;

        return app(PropertyInterface::class)->advancedGet($params);
    }
}

Form::component('themeIcon', Theme::getThemeNamespace() . '::partials.forms.fields.icons-field', [
    'name',
    'value' => null,
    'attributes' => [],
]);

function get_image_loading(): ?string
{
    return RvMedia::getImageUrl(theme_option('img_loading'));
}

if (! function_exists('get_countries')) {
    function get_countries()
    {
        return app(CountryInterface::class)->all();
    }
}

if (! function_exists('get_states_by_country')) {
    function get_states_by_country($countryId)
    {
        return app(StateInterface::class)->allBy([
            'country_id' => $countryId,
        ]);
    }
}

if (! function_exists('get_cities_by_state')) {
    function get_cities_by_state($stateId)
    {
        return app(CityInterface::class)
            ->getModel()
            ->where('state_id', $stateId)
            ->orderBy('name')
            ->get();
    }
}

if (! function_exists('get_re_categories')) {
    function get_re_categories($parentId = 0)
    {
        return app(CategoryInterface::class)
            ->allBy([
                'parent_id' => $parentId,
            ]);
    }
}

if (! function_exists('get_re_category')) {
    function get_re_category($id)
    {
        return app(CategoryInterface::class)
            ->findById($id);
    }
}

if (! function_exists('get_review_fields')) {
    /**
     * @param null $default
     * @return array
     */
    function get_review_fields(): array
    {
        $array_fields = json_decode(setting('real_estate_review_fields'), true);
        $review_fields = [];

        if (is_array($array_fields)) {
            foreach ($array_fields as $item) {
                $review_fields[] = [$item[0]['key'] => $item[0]['value']];
            }
        }

        return $review_fields;
    }
}
if (! function_exists('calculateReviewMetaData')) {
    function calculateReviewMetaData($meta, $reviewableId)
    {
        $property = Property::withCount('reviews')->find($reviewableId);
        $reviewsCount = $property->reviews_count;
        $reviewsMetaProperty = json_decode(get_meta_data($property, 'review_meta', true), true);

        if (isset($reviewsMetaProperty['service'])) {
            $meta_property = [
                'service' => round(($meta['service'] + $reviewsMetaProperty['service'] * $reviewsCount) / ($reviewsCount + 1), 1),
                'value' => round(($meta['value'] + $reviewsMetaProperty['value'] * $reviewsCount) / ($reviewsCount + 1), 1),
                'location' => round(($meta['location'] + $reviewsMetaProperty['location'] * $reviewsCount) / ($reviewsCount + 1), 1),
                'cleanliness' => round(($meta['cleanliness'] + $reviewsMetaProperty['cleanliness'] * $reviewsCount) / ($reviewsCount + 1), 1),
            ];
        } else {
            $meta_property = $meta;
        }

        return save_meta_data($property, 'review_meta', json_encode($meta_property));
    }
}
