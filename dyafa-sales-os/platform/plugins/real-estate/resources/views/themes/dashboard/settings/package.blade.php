@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <packages-component
        ajax-url="{{ route('public.account.ajax.packages') }}"
        subscribe-url="{{ route('public.account.ajax.package.subscribe') }}"
        v-slot="{ data, account, isLoading, isSubscribing, postSubscribe }"
        v-cloak
    >
        <x-core::alert class="current-package">
            {{ trans('plugins/real-estate::dashboard.your_credits') }}: <strong>@{{ account.formatted_credits }}</strong>
        </x-core::alert>

        <div class="packages-listing mb-3">
            <x-core::loading v-if="isLoading" />

            <template v-if="!isLoading && data.length && account">
                <div class="row flex-items-xs-middle flex-items-xs-center">
                    <div
                        :class="data.length === 2 ? 'col-xs-12 col-lg-6' : 'col-xs-12 col-lg-4'"
                        v-for="item in data"
                        :key="item.id"
                    >
                        <div class="card card-md">
                            <div class="ribbon ribbon-top ribbon-bookmark bg-green" v-if="item.percent_save_text">
                                @{{ item.percent_save_text }}
                            </div>
                            <div class="card-body text-center">
                                <div class="display-6 fw-bold my-3">@{{ item.name }}</div>
                                <p v-if="item.description" class="text-muted">@{{ item.description }}</p>
                                <ul class="list-unstyled lh-lg">
                                    <li v-if="item.price">
                                        <x-core::icon name="ti ti-check" class="text-success" />
                                        @{{ item.price_per_post_text }}
                                    </li>
                                    <li v-if="!item.price">
                                        <x-core::icon name="ti ti-check" class="text-success" />
                                        @{{ item.number_posts_free }}
                                    </li>
                                    <li v-if="item.price">
                                        <x-core::icon name="ti ti-check" class="text-success" />
                                        @{{ item.price_text_with_sale_off }}
                                    </li>
                                    <li v-if="!item.price">&nbsp;</li>
                                </ul>
                                <div class="text-center mt-4">
                                    <x-core::button
                                        class="w-100"
                                        v-bind:class="isSubscribing && currentPackageId === item.id ? 'btn btn-success button-loading mt-2' : (item.is_default ? 'btn btn-success mt-2' : 'btn mt-2')"
                                        v-on:click="postSubscribe(item.id)"
                                        v-bind:disabled="isSubscribing"
                                    >
                                        {{ trans('plugins/real-estate::dashboard.purchase') }}
                                    </x-core::button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </packages-component>

    <x-core::card>
        <x-core::card.header>
            <x-core::card.title>
                {{ trans('plugins/real-estate::dashboard.transactions_title') }}
            </x-core::card.title>
        </x-core::card.header>

        <payment-history-component
            ajax-url="{{ route('public.account.ajax.transactions') }}"
            v-slot="{ isLoading, isLoadingMore, data, getData }"
        >
            <x-core::loading v-if="isLoading" />

            <template v-else>
                <div class="empty" v-if="data.meta.total === 0">
                    <div class="empty-icon">
                        <x-core::icon name="ti ti-ghost" />
                    </div>
                    <p class="empty-title">
                        {{ trans('plugins/real-estate::dashboard.no_transactions_title') }}
                    </p>
                    <p class="empty-subtitle text-muted">
                        {{ trans('plugins/real-estate::dashboard.no_transactions') }}
                    </p>
                </div>

                <div v-if="data.meta.total !== 0" class="list-group list-group-flush">
                    <div v-for="item in data.data" :key="item.id" class="list-group-item">
                        <x-core::icon name="ti ti-clock" class="me-2" />
                        <span
                            :title="$sanitize(item.description, { allowedTags: [] })"
                            v-html="$sanitize(item.description)"
                        ></span>
                    </div>
                </div>

                <x-core::card.footer v-if="data.links.next">
                    <a href="javascript:void(0)" v-if="!isLoadingMore" @click="getData(data.links.next)">
                        {{  trans('plugins/real-estate::dashboard.load_more') }}
                    </a>
                    <a href="javascript:void(0)" v-if="isLoadingMore">
                        {{ trans('plugins/real-estate::dashboard.loading_more') }}
                    </a>
                </x-core::card.footer>
            </template>
        </payment-history-component>
    </x-core::card>
@endsection
