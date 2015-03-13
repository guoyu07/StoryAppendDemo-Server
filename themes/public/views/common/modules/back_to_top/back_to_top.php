<div class="back-to-top">
    <div class="btn-wrap">
        <div class="to-top-btn btn icon-arrow-up" title="回到顶部"></div>
        <div class="like-btn btn" title="添加收藏" ms-if="is_favorite === 0 || is_favorite == 1" ms-click="toggleFavor(this.$vmodel)"
             ms-class-1="icon-heart-empty:is_favorite == 0"
             ms-class-2="icon-heart-filled:is_favorite == 1"></div>
        <div class="wechat-qr-btn btn icon-wechat"></div>
    </div>
    <div class="toggle-wrap">
        <div class="wechat-qr-toggle">
            <img ms-src="{{ 'themes/public/images/common/qrcode002.png' }}" alt="玩途 官方微信公众号">
        </div>
    </div>
</div>