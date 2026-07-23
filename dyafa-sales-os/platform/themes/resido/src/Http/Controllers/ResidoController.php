<?php

namespace Theme\Resido\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\RealEstate\Repositories\Interfaces\ReviewInterface;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Testimonial\Repositories\Interfaces\TestimonialInterface;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Http\Controllers\PublicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Theme\Resido\Http\Resources\CityResource;
use Theme\Resido\Http\Resources\PostResource;
use Theme\Resido\Http\Resources\PropertyHTMLResource;
use Theme\Resido\Http\Resources\PropertyResource;
use Theme\Resido\Http\Resources\ReviewResource;
use Theme\Resido\Http\Resources\TestimonialResource;

class ResidoController extends PublicController
{
    public function getPropertiesByCity(
        string $key,
        Request $request,
        CityInterface $cityRepository,
        PropertyInterface $propertyRepository,
        CategoryInterface $categoryRepository
    ) {
        $city = $cityRepository->getFirstBy([
            'slug' => $key,
        ]);

        if (! $city) {
            abort(404);
        }

        SeoHelper::setTitle(__('Properties in :city', ['city' => $city->name]));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(SeoHelper::getTitle(), $city->url);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, CITY_MODULE_SCREEN_NAME, $city);

        $params = array_merge([
            'paginate' => [
                'per_page' => (int) theme_option('number_of_properties_per_page', 12),
                'current_paged' => (int) $request->input('page', 1),
            ],
            'order_by' => ['re_properties.created_at' => 'DESC'],
            'condition' => [
                'city_id' => $city->id,
            ],
        ], RealEstateHelper::getReviewExtraData());

        $properties = $propertyRepository->advancedGet($params);
        $categories = $categoryRepository->pluck('re_categories.name', 're_categories.id');

        return Theme::scope('real-estate.properties', compact('properties', 'categories'))
            ->render();
    }

    public function getProjectsByCity(
        string $slug,
        Request $request,
        ProjectInterface $projectRepository,
        CityInterface $cityRepository,
        BaseHttpResponse $response
    ) {
        $city = $cityRepository->getFirstBy(compact('slug'));

        if (! $city) {
            abort(404);
        }

        SeoHelper::setTitle(__('Projects in :city', ['city' => $city->name]));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(SeoHelper::getTitle(), route('public.project-by-city', $city->slug));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, CITY_MODULE_SCREEN_NAME, $city);

        $perPage = (int) $request->input('per_page') ? (int) $request->input('per_page') : (int) theme_option(
            'number_of_projects_per_page',
            12
        );

        $filters = [
            'keyword' => $request->input('k'),
            'blocks' => $request->input('blocks'),
            'min_floor' => $request->input('min_floor'),
            'max_floor' => $request->input('max_floor'),
            'min_flat' => $request->input('min_flat'),
            'max_flat' => $request->input('max_flat'),
            'category_id' => $request->input('category_id'),
            'city' => $slug,
            'location' => $request->input('location'),
            'sort_by' => $request->input('sort_by'),
        ];

        $params = [
            'paginate' => [
                'per_page' => $perPage ?: 12,
                'current_paged' => (int) $request->input('page', 1),
            ],
            'order_by' => ['re_projects.created_at' => 'DESC'],
            'with' => RealEstateHelper::getProjectRelationsQuery(),
        ];

        $projects = $projectRepository->getProjects($filters, $params);

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $response->setData(Theme::partial('search-suggestion', ['items' => $projects]));
            }

            return $response->setData(Theme::partial('real-estate.projects.items', ['projects' => $projects]));
        }

        $categories = get_property_categories([
            'indent' => '↳',
            'conditions' => ['status' => BaseStatusEnum::PUBLISHED],
        ]);

        return Theme::scope('real-estate.projects', compact('projects', 'categories'))
            ->render();
    }

    public function ajaxGetProperties(Request $request, BaseHttpResponse $response)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $properties = [];
        $with = [];
        $withCount = [];

        if (RealEstateHelper::isEnabledReview()) {
            $withCount = [
                'reviews',
            ];
        }
        switch ($request->input('type')) {
            case 'related':
                $properties = app(PropertyInterface::class)
                    ->advancedGet(array_merge([
                        'condition' => [
                            're_properties.is_featured' => true,
                            're_properties.type' => PropertyTypeEnum::RENT,
                            're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
                        ],
                        'take' => (int) theme_option('number_of_properties_for_rent', 8),

                    ], RealEstateHelper::getReviewExtraData()));
                // no break
            case 'rent':
                $properties = app(PropertyInterface::class)
                    ->advancedGet(array_merge([
                        'condition' => [
                            're_properties.is_featured' => true,
                            're_properties.type' => PropertyTypeEnum::RENT,
                            're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
                        ],
                        'take' => (int) theme_option('number_of_properties_for_rent', 8),
                    ], RealEstateHelper::getReviewExtraData()));

                break;

            case 'sale':
                $properties = app(PropertyInterface::class)
                    ->advancedGet(array_merge([
                        'condition' => [
                            're_properties.is_featured' => true,
                            're_properties.type' => PropertyTypeEnum::SALE,
                            're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
                        ],
                        'take' => (int) theme_option('number_of_properties_for_sale', 8),
                    ], RealEstateHelper::getReviewExtraData()));

                break;

            case 'recently-viewed-properties':
                $cookieName = App::getLocale() . '_recently_viewed_properties';
                $jsonRecentViewProduct = null;

                if (isset($_COOKIE[$cookieName])) {
                    $jsonRecentViewProduct = $_COOKIE[$cookieName];
                }

                if (! empty($jsonRecentViewProduct)) {
                    $ids = collect(json_decode($jsonRecentViewProduct, true))->flatten()->all();

                    $properties = app(PropertyInterface::class)
                        ->advancedGet(array_merge([
                            'condition' => [
                                ['re_properties.id', 'IN', $ids],
                                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
                            ],
                            'take' => (int) theme_option('number_of_recently_viewed_properties', 3),
                        ], RealEstateHelper::getReviewExtraData()));
                    $reversed = array_reverse($ids);

                    $properties = $properties->sortBy(function ($model) use ($reversed) {
                        return array_search($model->id, $reversed);
                    });
                }

                break;
        }

        return $response
            ->setData(PropertyHTMLResource::collection($properties))
            ->toApiResponse();
    }

    public function ajaxGetPropertiesForMap(Request $request, BaseHttpResponse $response)
    {
        $params = [
            'with' => config('plugins.real-estate.real-estate.properties.relations'),
            'paginate' => [
                'per_page' => 20,
                'current_paged' => (int) $request->input('page', 1),
            ],
        ];

        $properties = app(PropertyInterface::class)->getProperties(['type' => $request->input('type')], $params);

        return $response
            ->setData(PropertyResource::collection($properties))
            ->toApiResponse();
    }

    public function ajaxGetPosts(Request $request, BaseHttpResponse $response)
    {
        if (! $request->ajax() || ! $request->wantsJson()) {
            abort(404);
        }

        $posts = app(PostInterface::class)->getFeatured(4, ['slugable', 'categories', 'categories.slugable']);

        return $response
            ->setData(PostResource::collection($posts))
            ->toApiResponse();
    }

    public function getAgents(Request $request, AccountInterface $accountRepository)
    {
        $accounts = $accountRepository->advancedGet([
            'paginate' => [
                'per_page' => 12,
                'current_paged' => (int) $request->input('page'),
            ],
            'withCount' => [
                'properties' => function ($query) {
                    return RepositoryHelper::applyBeforeExecuteQuery($query, $query->getModel());
                },
            ],
        ]);

        SeoHelper::setTitle(__('Agents'));
        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add(__('Agents'), route('public.agents'));

        return Theme::scope('real-estate.agents', compact('accounts'))->render();
    }

    public function getAgent(
        string $username,
        Request $request,
        AccountInterface $accountRepository,
        PropertyInterface $propertyRepository
    ) {
        $account = $accountRepository->getFirstBy(['username' => $username]);

        if (! $account) {
            abort(404);
        }

        SeoHelper::setTitle($account->name);

        $propertyTypes = PropertyTypeEnum::labels();
        $propertiesRelated = [];
        $totalProperties = 0;

        foreach ($propertyTypes as $keyPropertyType => $propertyType) {
            $properties = $propertyRepository->advancedGet([
                'condition' => [
                    'author_id' => $account->id,
                    'author_type' => Account::class,
                    'type' => $keyPropertyType,
                ],
                'paginate' => [
                    'per_page' => 12,
                    'current_paged' => (int) $request->input('page'),
                ],
                'with' => config('plugins.real-estate.real-estate.properties.relations'),
            ]);

            $propertiesRelated[] = [
                'type' => $propertyType,
                'properties' => $properties,
            ];

            $totalProperties += $properties->count();
        }

        return Theme::scope('real-estate.agent', compact('propertiesRelated', 'totalProperties', 'account'))
            ->render();
    }

    public function ajaxGetCities(Request $request, CityInterface $cityRepository, BaseHttpResponse $response)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $keyword = BaseHelper::stringify($request->input('q'));

        $cities = $cityRepository->filters($keyword, 10, [], ['cities.*']);

        return $response->setData(CityResource::collection($cities))->toApiResponse();
    }

    public function getWishlist(Request $request, PropertyInterface $propertyRepository)
    {
        SeoHelper::setTitle(__('Wishlist'))
            ->setDescription(__('Wishlist'));

        $cookieName = App::getLocale() . '_wishlist';
        $jsonWishlist = null;
        if (isset($_COOKIE[$cookieName])) {
            $jsonWishlist = $_COOKIE[$cookieName];
        }

        $properties = collect([]);

        if (! empty($jsonWishlist)) {
            $arrValue = collect(json_decode($jsonWishlist, true))->flatten()->all();
            $properties = $propertyRepository->advancedGet([
                'condition' => [
                    ['re_properties.id', 'IN', $arrValue],
                ],
                'order_by' => [
                    're_properties.id' => 'DESC',
                ],
                'paginate' => [
                    'per_page' => (int) theme_option('number_of_properties_per_page', 12),
                    'current_paged' => (int) $request->input('page', 1),
                ],
                'with' => config('plugins.real-estate.real-estate.properties.relations'),
            ]);
        }

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Wishlist'));

        return Theme::scope('real-estate.wishlist', compact('properties'))->render();
    }

    public function ajaxGetTestimonials(
        Request $request,
        BaseHttpResponse $response,
        TestimonialInterface $testimonialRepository
    ) {
        if (! $request->ajax() || ! $request->wantsJson()) {
            abort(404);
        }

        $testimonials = $testimonialRepository->allBy(['status' => BaseStatusEnum::PUBLISHED]);

        return $response->setData(TestimonialResource::collection($testimonials));
    }

    public function ajaxGetRealEstateReviews(
        $id,
        Request $request,
        BaseHttpResponse $response,
        ReviewInterface $reviewRepository
    ) {
        $reviews = $reviewRepository->advancedGet([
            'condition' => [
                'status' => ReviewStatusEnum::APPROVED,
                'reviewable_type' => $request->input('reviewable_type', Property::class),
                'reviewable_id' => $id,
            ],
            'order_by' => ['created_at' => 'desc'],
            'paginate' => [
                'per_page' => (int) $request->input('per_page', 10),
                'current_paged' => (int) $request->input('page', 1),
            ],
        ]);

        return $response->setData(ReviewResource::collection($reviews))->toApiResponse();
    }

    public function ajaxGetRealEstateRating(
        $id,
        Request $request,
        BaseHttpResponse $response,
        PropertyInterface $propertyRepository
    ) {
        if (! $request->ajax() || ! $request->wantsJson()) {
            abort(404);
        }

        $property = $propertyRepository->getProperty(
            $id,
            RealEstateHelper::getPropertyRelationsQuery(),
            RealEstateHelper::getReviewExtraData()
        );
        if (! $property->reviews_count) {
            return $response->setData([
                'message' => __('No review found'),
            ])->toApiResponse();
        }
        $review_meta = json_decode(get_meta_data($property, 'review_meta', true), true);
        $dataRating = [
            'summary_avg' => [
                'cleanliness' => $review_meta['cleanliness'],
                'location' => $review_meta['location'],
                'service' => $review_meta['service'],
                'value' => $review_meta['value'],
            ],
            'star' => round($property->reviews_avg_star, 1),
        ];

        return $response->setData($dataRating)->toApiResponse();
    }

    public function ajaxGetSubCategories(
        Request $request,
        CategoryInterface $categoryRepository,
        BaseHttpResponse $response
    ) {
        if (! $request->ajax()) {
            abort(404);
        }

        $categories = $categoryRepository->allBy([
            'parent_id' => $request->input('id'),
        ]);

        return $response->setData($categories)->toApiResponse();
    }
}
