<div class="rating_wrap" data-star="{{ $property->reviews_avg_star }}">
    <div class="rating">
        <div class="product_rate" style="width: {{ $property->reviews_avg_star * 20 }}%;">{{ $property->reviews_sum_star }}
        </div>
    </div>
    <span class="reviews_text">( {{ $property->reviews_count }} {{ __('Reviews') }})</span>
</div>
