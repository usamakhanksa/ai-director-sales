<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Location\Models\City;
use Botble\Location\Models\State;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Requests\SendConsultRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\RssFeed\Facades\RssFeed;
use Botble\RssFeed\FeedItem;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Exception;
use Illuminate\Http\Request;

class PublicController extends BaseController
{
    public function postSendConsult(
        SendConsultRequest $request,
        PropertyInterface $propertyRepository,
        ProjectInterface $projectRepository
    ) {
        do_action('form_extra_fields_validate', $request);

        try {
            $sendTo = null;
            $link = null;
            $subject = null;

            if ($request->input('type') == 'project') {
                $request->merge(['project_id' => $request->input('data_id')]);
                $project = $projectRepository->findById($request->input('data_id'));
                if ($project) {
                    $link = $project->url;
                    $subject = $project->name;
                }
            } else {
                $request->merge(['property_id' => $request->input('data_id')]);
                $property = $propertyRepository->findById($request->input('data_id'), ['author']);
                if ($property) {
                    $link = $property->url;
                    $subject = $property->name;

                    if ($property->author->email) {
                        $sendTo = $property->author->email;
                    }
                }
            }

            $ipAddress = $request->ip();

            $consult = Consult::query()->create(array_merge($request->input(), ['ip_address' => $ipAddress]));

            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'consult_name' => $consult->name,
                    'consult_email' => $consult->email,
                    'consult_phone' => $consult->phone,
                    'consult_content' => $consult->content,
                    'consult_link' => $link,
                    'consult_subject' => $subject,
                    'consult_ip_address' => $consult->ip_address,
                ])
                ->sendUsingTemplate('notice', $sendTo);

            return $this
                ->httpResponse()
                ->setMessage(trans('plugins/real-estate::consult.email.success'));
        } catch (Exception $exception) {
            info($exception->getMessage());

            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/real-estate::consult.email.failed'));
        }
    }

    public function getProjects(Request $request)
    {
        SeoHelper::setTitle(__('Projects'));

        $projects = RealEstateHelper::getProjectsFilter((int) theme_option('number_of_projects_per_page') ?: 12, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $projects]));
            }

            return $this
                ->httpResponse()
                ->setData(Theme::partial('real-estate.projects.items', compact('projects')));
        }

        return Theme::scope('real-estate.projects', compact('projects'))->render();
    }

    public function getProperties(Request $request)
    {
        SeoHelper::setTitle(__('Properties'));

        $properties = RealEstateHelper::getPropertiesFilter((int) theme_option('number_of_properties_per_page') ?: 12, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->query('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $properties]));
            }

            return $this
                ->httpResponse()
                ->setData(Theme::partial('real-estate.properties.items', compact('properties')));
        }

        return Theme::scope('real-estate.properties', compact('properties'))->render();
    }

    public function changeCurrency(Request $request, $title = null)
    {
        if (empty($title)) {
            $title = $request->input('currency');
        }

        if (! $title) {
            return $this->httpResponse();
        }

        $currency = Currency::query()
            ->where('title', $title)
            ->first();

        if ($currency) {
            cms_currency()->setApplicationCurrency($currency);
        }

        return $this->httpResponse();
    }

    public function getPropertyFeeds(PropertyInterface $propertyRepository)
    {
        if (! is_plugin_active('rss-feed')) {
            abort(404);
        }

        $data = $propertyRepository->getProperties([], [
            'take' => 20,
            'with' => ['slugable', 'categories', 'author'],
        ]);

        $feedItems = collect();

        foreach ($data as $item) {
            $imageURL = RvMedia::getImageUrl($item->image, null, false, RvMedia::getDefaultImage());

            $feedItem = FeedItem::create()
                ->id($item->id)
                ->title(BaseHelper::clean($item->name))
                ->summary(BaseHelper::clean($item->description))
                ->updated($item->updated_at)
                ->enclosure($imageURL)
                ->enclosureType(RvMedia::getMimeType(RvMedia::getRealPath($item->image ?: RvMedia::getDefaultImage())))
                ->enclosureLength(RssFeed::remoteFilesize($imageURL))
                ->category((string) $item->category->name)
                ->link((string) $item->url);

            if (method_exists($feedItem, 'author')) {
                $feedItem = $feedItem->author($item->author_id && $item->author->name ? $item->author->name : '');
            } else {
                $feedItem = $feedItem
                    ->authorName($item->author_id && $item->author->name ? $item->author->name : '')
                    ->authorEmail($item->author_id && $item->author->email ? $item->author->email : '');
            }

            $feedItems[] = $feedItem;
        }

        return RssFeed::renderFeedItems(
            $feedItems,
            'Properties feed',
            'Latest properties from ' . theme_option('site_title')
        );
    }

    public function getProjectFeeds(ProjectInterface $projectRepository)
    {
        if (! is_plugin_active('rss-feed')) {
            abort(404);
        }

        $data = $projectRepository->getProjects(
            [],
            [
                'take' => 20,
                'width' => ['categories'],
            ]
        );

        $feedItems = collect();

        foreach ($data as $item) {
            $imageURL = RvMedia::getImageUrl($item->image, null, false, RvMedia::getDefaultImage());

            $feedItem = FeedItem::create()
                ->id($item->id)
                ->title(BaseHelper::clean($item->name))
                ->summary(BaseHelper::clean($item->description))
                ->updated($item->updated_at)
                ->enclosure($imageURL)
                ->enclosureType(RvMedia::getMimeType(RvMedia::getRealPath($item->image ?: RvMedia::getDefaultImage())))
                ->enclosureLength(RssFeed::remoteFilesize($imageURL))
                ->category((string) $item->category->name)
                ->link((string) $item->url);

            if (method_exists($feedItem, 'author')) {
                $feedItem = $feedItem->author($item->author_id && $item->author->name ? $item->author->name : '');
            } else {
                $feedItem = $feedItem
                    ->authorName($item->author_id && $item->author->name ? $item->author->name : '')
                    ->authorEmail($item->author_id && $item->author->email ? $item->author->email : '');
            }

            $feedItems[] = $feedItem;
        }

        return RssFeed::renderFeedItems(
            $feedItems,
            'Projects feed',
            'Latest projects from ' . theme_option('site_title')
        );
    }

    public function getProjectsByCity(string $slug, Request $request)
    {
        $city = City::query()->wherePublished()->where('slug', $slug)->firstOrFail();

        SeoHelper::setTitle(__('Projects in :city', ['city' => $city->name]));

        Theme::breadcrumb()
            ->add(SeoHelper::getTitle(), route('public.projects-by-city', $city->slug));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, CITY_MODULE_SCREEN_NAME, $city);

        $perPage = $request->integer('per_page') ?: (int) theme_option('number_of_projects_per_page', 12);

        $request->merge(['city' => $slug]);

        $projects = RealEstateHelper::getProjectsFilter($perPage, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $projects]));
            }

            return $this
                ->httpResponse()
                ->setData(Theme::partial('real-estate.projects.items', ['projects' => $projects]));
        }

        return Theme::scope('real-estate.projects', [
            'projects' => $projects,
            'ajaxUrl' => route('public.projects-by-city', $city->slug),
            'actionUrl' => route('public.projects-by-city', $city->slug),
        ])
            ->render();
    }

    public function getPropertiesByCity(string $slug, Request $request)
    {
        $city = City::query()->wherePublished()->where('slug', $slug)->firstOrFail();

        SeoHelper::setTitle(__('Properties in :city', ['city' => $city->name]));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, CITY_MODULE_SCREEN_NAME, $city);

        Theme::breadcrumb()
            ->add(SeoHelper::getTitle(), route('public.properties-by-city', $city->slug));

        $perPage = $request->integer('per_page') ?: (int) theme_option('number_of_properties_per_page', 12);

        $request->merge(['city' => $slug]);

        $properties = RealEstateHelper::getPropertiesFilter($perPage, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $properties]));
            }

            return $this
                ->httpResponse()
                ->setData(Theme::partial('real-estate.properties.items', ['properties' => $properties]));
        }

        return Theme::scope('real-estate.properties', [
            'properties' => $properties,
            'ajaxUrl' => route('public.properties-by-city', $city->slug),
            'actionUrl' => route('public.properties-by-city', $city->slug),
        ])
            ->render();
    }

    public function getProjectsByState(string $slug, Request $request)
    {
        $state = State::query()
            ->wherePublished()
            ->where('slug', $slug)
            ->firstOrFail();

        SeoHelper::setTitle(__('Projects in :state', ['state' => $state->name]));

        Theme::breadcrumb()
            ->add(SeoHelper::getTitle(), route('public.projects-by-city', $state->slug));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, STATE_MODULE_SCREEN_NAME, $state);

        $perPage = $request->integer('per_page') ?: (int) theme_option('number_of_projects_per_page', 12);

        $request->merge(['state' => $slug]);

        $projects = RealEstateHelper::getProjectsFilter($perPage, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $projects]));
            }

            return $this
                ->httpResponse()
                ->setData(Theme::partial('real-estate.projects.items', ['projects' => $projects]));
        }

        return Theme::scope('real-estate.projects', [
            'projects' => $projects,
            'ajaxUrl' => route('public.projects-by-state', $state->slug),
            'actionUrl' => route('public.projects-by-state', $state->slug),
        ])
            ->render();
    }

    public function getPropertiesByState(
        string $slug,
        Request $request
    ) {
        $state = State::query()
            ->wherePublished()
            ->where('slug', $slug)
            ->firstOrFail();

        SeoHelper::setTitle(__('Properties in :state', ['state' => $state->name]));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, STATE_MODULE_SCREEN_NAME, $state);

        Theme::breadcrumb()
            ->add(SeoHelper::getTitle(), route('public.properties-by-state', $state->slug));

        $perPage = $request->integer('per_page') ?: (int) theme_option('number_of_properties_per_page', 12);

        $request->merge(['state' => $slug]);

        $properties = RealEstateHelper::getPropertiesFilter($perPage, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $properties]));
            }

            return $this
                ->httpResponse()
                ->setData(Theme::partial('real-estate.properties.items', ['properties' => $properties]));
        }

        return Theme::scope('real-estate.properties', [
            'properties' => $properties,
            'ajaxUrl' => route('public.properties-by-state', $state->slug),
            'actionUrl' => route('public.properties-by-state', $state->slug),
        ])
            ->render();
    }

    public function getAgents()
    {
        if (RealEstateHelper::isDisabledPublicProfile()) {
            abort(404);
        }

        $accounts = Account::query()
            ->where('is_public_profile', true)
            ->orderByDesc('id')
            ->withCount([
                'properties' => function ($query) {
                    return RepositoryHelper::applyBeforeExecuteQuery($query, $query->getModel());
                },
            ])
            ->with(['avatar'])
            ->paginate(12);

        SeoHelper::setTitle(__('Agents'));

        Theme::breadcrumb()->add(__('Agents'), route('public.agents'));

        return Theme::scope('real-estate.agents', compact('accounts'))->render();
    }
}
