<div class="product-comments content-item" id="tab_comments" ms-controller="productCommentsCtrl" ms-if="data.comments.length > 0">
    <div class="comment-score container">
        <div class="part-title-row">
            <div class="part-title">用户点评</div>
        </div>
        <div class="score-row">
            <div class="rating-stars">
                <div class="stars-back icon-rating-logo"></div>
                <div class="stars-front icon-rating-logo" ms-css-width="{{ data.score.avg_hitour_service_level / 5 * 100 }}%"></div>
            </div>
            <div class="score-text"><em>{{ data.score.avg_hitour_service_level }}</em><span> 分</span></div>
            <div class="score-count"><span>{{ data.score.total }}</span>条评论</div>
        </div>
    </div>
    <div class="comment-list container">
        <div class="comment-item clearfix" ms-repeat-comment="data.comments">
            <div class="user-col">
                <div class="user-name">{{ comment.customer.firstname }}</div>
                <div class="user-score">
                    <div class="rating-stars user-stars">
                        <div class="stars-back icon-rating-logo"></div>
                        <div class="stars-front icon-rating-logo"
                             ms-css-width="{{ comment.hitour_service_level / 5 * 100 }}%"></div>
                    </div>
                    <span class="user-score-text"><span>{{ comment.hitour_service_level }}</span>分</span>
                </div>
            </div>
            <div class="comment-col">
                <p class="comment-text">{{ comment.content }}</p>
            </div>
        </div>
        <div class="comment-page-number">
            <i ms-click="goCommentPage('prev')">上一页</i>
            <i ms-repeat-num="local.comment_page_number" ms-click="goCommentPage($index)"
               ms-class="active : $index == local.active_comment_page">{{ $index + 1 }}</i>
            <i ms-click="goCommentPage('next')">下一页</i>
        </div>
    </div>
</div>