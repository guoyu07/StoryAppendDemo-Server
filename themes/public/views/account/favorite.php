<div class="main-wrap favorite-container" ms-controller="favorite">
    <div class="container">
        <div ms-if="!hasCollection" class="fav-notification">
            <p class="title"><i class="i icon-heart-filled"></i>您尚未在收藏夹里保存任何东西。</p>
            <p class="desc">这很容易：只要在任何商品页面点击心形按钮即可将心仪的商品存入收藏夹</p>
        </div>
        <div ms-if="hasCollection" class="tab-nav">
            <ul>
                <li ms-repeat-el="data" ms-data-index="$index" ms-class="active:$index==0"
                    class="tab" ms-click="switchTab($index)">{{el.title}}</li>
            </ul>
        </div>
        <div class="tab-content">
            <div class="one-part" ms-repeat-product="singleGroup.products">
                <div class="one-collection" ms-if="product.online">
                    <i class="one-delete i icon-x-circle-bg" ms-click="deleteOneProduct($index)"></i>
                    <div class="left-part">
                        <a ms-href="{{product.link}}"><img ms-src="{{product.cover_image}}?imageView/1/w/461/h/223"></a>
                    </div>
                    <div class="right-part">
                        <div class="one-title"><a ms-href="product.link">{{product.name}}</a></div>
                        <div ms-if="product.benefit" class="one-benefit">
                            <i class="i icon-tag2"></i>
                            {{product.benefit}}
                        </div>
                        <div class="price-zone">
                            <span class="RMB">￥</span><span class="price">{{product.price}}</span><span class="appendix">起</span>
                        </div>
                    </div>
                </div>
                <div class="expired-collection" ms-if="!product.online">
                    <i class="one-delete i icon-x-circle-bg" ms-click="deleteOneProduct($index)"></i>
                    <span class="expired-notice"><i class="i icon-exclamation-circle"></i>产品已失效</span>
                    <span class="expired-name">{{product.name}}</span>
                </div>
            </div>
        </div>
    </div>
</div>