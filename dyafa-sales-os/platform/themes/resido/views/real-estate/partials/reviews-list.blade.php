<div id="clEight" class="panel-collapse collapse show">
    <div class="block-body">
        <div class="author-review">
            <div class="comment-list">
                <ul>
                    @foreach ($reviews as $review)
                        <li class="article_comments_wrap" data-star="{{ $review->star }}">
                            <article>
                                <div class="article_comments_thumb">
                                    <img src="{{ RvMedia::getImageUrl($review->author->avatar_url, 'thumb') }}"
                                        alt="{{ $review->author->name }}" />
                                </div>
                                <div class="comment-details">
                                    <div class="rating_wrap">
                                        <div class="rating">
                                            <div class="product_rate" style="width: {{ $review->star * 20 }}%;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comment-meta">
                                        <div class="comment-left-meta">
                                            <h4 class="author-name">
                                                {{ $review->author->name }}
                                            </h4>
                                            <div class="comment-date">
                                                {{ $review->created_at?->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comment-text">
                                        <p>{{ $review->content }}</p>
                                    </div>
                                </div>
                            </article>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div id="pagination-review">
                {{ $reviews->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</div>
