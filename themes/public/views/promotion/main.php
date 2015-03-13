<div class="promotion" ms-controller="promoInfo">
    <div class="header">
        <div class="banner" ms-css-background-image="url({{ promo_banner_img }})"></div>
        <div class="intro container" ms-visible="promo_title && promo_desc">
            <h1>{{ promo_title }}</h1>

            <p class="desc" ms-html="promo_desc"></p>
        </div>
    </div>
    <div class="groups-ctn" ms-controller="promoGroups">
        <div class="group" ms-repeat-gp="groups">
            <div class="container">
                <div class="title">
                    <div class="clearfix">
                        <h2>{{ gp.name }}</h2>
                        <a ms-href="{{ gp.attach_url }}" class="more-link" ms-visible="gp.attach_url">查看更多 &gt;</a>
                    </div>

                    <p class="desc" ms-html="gp.description"></p>
                </div>
                <div class="product-list clearfix">
                    <div class="product-item" ms-repeat-pd="gp.promotion_product">
                        <a ms-href="pd.link_url" target="_blank" class="item-link">
                            <div class="cover-img" data-original="#" ms-if="$index % 10 == 0 || $index % 10 == 6" ms-css-background-image="url({{pd.cover_image.image_url}}?imageView/1/w/655/h/310)"></div>
                            <div class="cover-img" data-original="#" ms-if="$index % 10 != 0 && $index % 10 != 6" ms-css-background-image="url({{pd.cover_image.image_url}}?imageView/1/w/310/h/310)"></div>
                            <div class="price">&yen;<em>{{ pd.show_prices.price }}</em></div>
                            <div class="summersale-tag" ms-if="pd.activity_info && pd.activity_info.length != 0">
                                <img ms-src="pd.activity_info.tag_small_url">
                            </div>
                            <div class="special-tag" ms-if="pd.show_prices.special_info">
                                <span>{{ pd.show_prices.special_info.reseller }}</span><br>
                                <span>{{ pd.show_prices.special_info.slogan }}</span><br>
                                <span>{{ (1 - pd.show_prices.price / pd.show_prices.orig_price) * 100 | number(0) }}% OFF</span>
                            </div>
                            <div class="item-hover">
                                <h3>{{ pd.description.name }}</h3>
                                {{ pd.description.service_include | html }}
                            </div>
                        </a>

                        <div class="item-info">
                            <h2><a ms-href="pd.link_url">{{ pd.description.name }}</a></h2>

                            <div class="benefit-tag" ms-if="pd.description.benefit">{{ pd.description.benefit }}</div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>