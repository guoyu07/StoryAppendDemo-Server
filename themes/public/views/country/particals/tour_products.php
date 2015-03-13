<div class="item-wrap">
    <a class="product-image-row" ms-href="tour.detail.link_url" target="_blank">
        <img ms-src="{{ tour.image_url }}?imageView2/5/w/328/h/280">
        <div class="image-cover">
            <table class="cover-bottom">
                <tr>
                    <td class="tour-days"><span class="price-font">{{ tour.detail.tour_days }}</span>天</td>
                    <td class="tour-meet"><span>途径：{{ tour.detail.tour_cities }}</span></td>
                </tr>
            </table>
        </div>
    </a>
    <div class="product-intro-row">
        <div class="tour-name">
            <a ms-href="tour.detail.link_url" target="_blank">{{ tour.name }}</a>
        </div>
        <div class="tour-season">最佳旅行时间：<span>{{ tour.detail.suitable_time }}</span></div>
        <div class="tour-price price-font">
            <div class="orig-price">
                &yen;<em>{{ tour.detail.price.orig_price }}</em>
            </div>
            <div class="now-price">
                &yen;<em>{{ tour.detail.price.price }}</em>/{{ tour.detail.price.title }}
            </div>
        </div>
    </div>
</div>