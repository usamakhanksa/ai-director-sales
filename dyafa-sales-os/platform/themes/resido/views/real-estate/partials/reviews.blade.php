@php
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add('review-js', 'js/review.js');
    $canReview =
        auth('account')->check() &&
        auth('account')
            ->user()
            ->canReview($model);
    $review_meta_list = json_decode(get_meta_data($property, 'review_meta', true), true);
@endphp

<div id="reviews-component" data-api-get-rating="{{ route('public.ajax.real-estate-rating', $model->id) }}"
    data-review-fields="{{ json_encode(get_review_fields()) }}">
    @if ($model->reviews_count > 0)
        <div class="rating-overview">
            <div class="rating-overview-box">
                <span class="rating-overview-box-total"> {{ round($model->reviews_avg_star, 1) }} </span>
                <span class="rating-overview-box-percent">{{ __('out of 5.0') }}</span>
                <div class="rating_wrap">
                    <div class="rating">
                        <div class="product_rate" data-rate="{{ $model->reviews_avg_star }}"
                            style="width: {{ $model->reviews_avg_star * 20 }}%;"></div>
                    </div>
                </div>
            </div>
            @if (is_array($review_meta_list))
                <div class="rating-bars">

                    @foreach (get_review_fields() as $reviewField)
                        <div class="rating-bars-item">
                            <span class="rating-bars-name">{{ trans('review.' . $reviewField['field']) }}</span>

                            <span class="rating-bars-inner">
                                <span class="rating-bars-rating high"
                                    data-rating="{{ $review_meta_list[$reviewField['field']] }}">
                                    <span class="rating-bars-rating-inner"
                                        style="width: {{ $review_meta_list[$reviewField['field']] * 20 }}%;"></span>
                                </span>
                                <strong>{{ round($review_meta_list[$reviewField['field']], 1) }}</strong>
                            </span>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="property_block_wrap style-2">
            <div class="property_block_wrap_header">
                <a data-bs-toggle="collapse" data-parent="#comment" data-bs-target="#clEight" aria-controls="clEight"
                    href="javascript:void(0);" aria-expanded="true">
                    <h4 class="property_block_title">{{ $model->reviews_count }} {{ __('Reviews') }}</h4>
                </a>
            </div>
            <div class="loading-spinner"></div>
            <div class="reviews-list"
                data-url="{{ route('public.ajax.review.index', $model->slug) }}?reviewable_type={{ get_class($model) }}"
                data-type="{{ get_class($model) }}"></div>
        </div>
    @endif
</div>

@if ($canReview)
    <div class="property_block_wrap style-2">

        <div class="property_block_wrap_header">
            <a data-bs-toggle="collapse" data-parent="#comment" data-bs-target="#clTen" aria-controls="clTen"
                href="javascript:void(0);" aria-expanded="true">
                <h4 class="property_block_title">{{ __('Write a Review') }}</h4>

            </a>
            @if (!auth('account')->check())
                <p class="text-danger">{{ __('Please') }} <a class="text-danger"
                        href="{{ route('public.account.login') }}">{{ __('login') }}</a>
                    {{ __('to write review!') }}</p>
            @endif
        </div>
        <div id="clTen" class="panel-collapse collapse show">
            <div class="block-body">
                {!! Form::open([
                    'route' => ['public.ajax.review.store', 'slug' => $model->slug],
                    'method' => 'post',
                    'class' => 'form--review-product review-form',
                ]) !!}
                <input type="hidden" name="reviewable_id" value="{{ $model->id }}">
                <input type="hidden" name="reviewable_type" value="{{ get_class($model) }}">
                <div class="row py-3">
                    <div class="col-md-8">
                        <div class="row">
                            @foreach (get_review_fields() as $item)
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <label for="select-star">{{ trans('review.' . $item['field']) }}</label>
                                    <select class="rating" name="meta[{{ $item['field'] }}]"
                                        id="select-star-{{ $item['field'] }}" data-read-only="false"
                                        @disabled(!$canReview)>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5" selected>5</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="avg-total-pilx">
                            <input type="hidden" name="star" value="5">
                            <h4 class="high user_commnet_avg_rate">5</h4>
                            <span>{{ __('Average Rating') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="form-group">
                            <textarea name="content" class="form-control ht-80" placeholder="{{ __('Messages') }}" @disabled(!$canReview)></textarea>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="form-group">
                            <button class="btn btn-theme-light-2 rounded" type="submit"
                                @disabled(!$canReview)>{{ __('Submit Review') }}</button>
                        </div>
                    </div>

                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endif
