
<!--stylesheet-->
<link rel="stylesheet" href="themes/public/views/homeExt/stylesheets/guide2345.css">

<!--template-->
<div class="guide-2345 main-content" ms-controller="guide2345">
    <div class="guide-container">
        <!--Top Navigation-->
        <div class="navigation" ms-mouseleave="listeners.onNavOut()">
            <div class="country-list">
                <div class="each-continent" ms-repeat-continent="continents" ms-visible="continent.continent_id != 5"
                     ms-class-1="border-top:$index != 0" ms-data-continentid="continent.continent_id"
                     ms-mouseenter="listeners.onListHover(continent.continent_id);"
                     ms-mouseleave="listeners.onListOut()">
                    <div class="continent-name">
                        <span>{{ continent.cn_name }}</span>
                        <span>{{ continent.en_name }}</span>
                    </div>
                    <div class="continent-countries">
                        <ul>
                            <li ms-repeat-country="continent.countries" ms-visible="country.is_hot == 1">
                                <a target="_blank" ms-href="{{country.link_url}}">{{ country.cn_name }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="city-list">
                <div class="each-country" ms-repeat-subcountry="one_continent.countries">
                    <div class="country-name">
                        <a target="_blank" ms-href="{{subcountry.link_url}}" title="查看国家主页">{{ subcountry.cn_name }}</a>
                    </div>
                    <div class="country-cities" ms-repeat-group="subcountry.city_groups">
                        <ul>
                            <li ms-repeat-city="group.cities">
                                <a target="_blank" ms-href="{{city.link_url}}">{{ city.cn_name }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="nav-carousel">
                <div class="hi-carousel" id="my_carousel">
                    <div class="carousel-list">
                        <a class="carousel-item" target="_blank" ms-repeat-image="carousel_images" ms-class-1="active:$index == 0"
                            ms-visible="$index==0" ms-attr-index="$index" ms-href="{{image.link_url}}">
                            <img ms-src="{{ image.image_url }}?imageView/1/w/748/h/503">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!--Hot Sights-->
        <div class="hot-sight">
            <div class="title">
                <h2>全球热门目的地</h2>
                <p>探索精彩世界</p>
            </div>
            <div class="dest-content">
                <div class="dest-block" ms-repeat-view="best_views" ms-css-left="view.left + 'px'" ms-css-top="view.top + 'px'">
                    <a target="_blank" ms-href="{{view.link_url}}">
                        <img ms-src="view.cover_image">
                        <p>{{ view.name }}</p>
                    </a>
                </div>
            </div>
        </div>

        <!--Hot Products-->
        <div class="hot-product">
            <div class="title">
                <h2>热门商品推荐</h2>
            </div>
            <div class="tab">
                <ul>
                    <li class="tab-item" ms-repeat-names="hot_groups"
                        ms-class-1="active:$index==0" ms-class-2="divider:$index!=0"
                        ms-attr-groupid="$index" ms-click="changeTab()">{{names.name}}</li>
                </ul>
            </div>
            <div class="products-content" ms-repeat-group="hot_groups" ms-attr-groupid="$index" ms-class-1="group-active:$index==0">
                <div class="product-block" ms-repeat-product="group.products">
                    <div class="product-wrap clearfix">
                        <div class="product-name-block">
                            <h2 class="product-name-cnname">
                                <a target="_blank" ms-href="{{product.link_url}}">{{ product.description.name }}</a>
                            </h2>
                        </div>
                        <div class="product-img-block">
                            <img ms-src="{{ product.cover_image.image_url }}?imageView/1/w/180/h/130">
                        </div>
                        <div class="product-service-block" ms-html="product.description.service_include"></div>
                        <div class="product-price">
                            <div class="hi-price">
                                &yen;<em>{{ product.show_prices.price }}</em>&nbsp;&nbsp;起
                            </div>
                            <div class="orig-price">
                                市场价 &yen;<em>{{ product.show_prices.orig_price }}</em>
                            </div>
                            <div class="sales-block">
                                已售
                                <span class="sales_volume">{{ product.sales_volume }}</span>
                            </div>
                        </div>
                        <div class="product-links">
                            <a class="detail-link" target="_blank" ms-href="{{product.link_url}}">查看详情 ></a>
                            <a class="type-link" target="_blank" ms-href="{{product.city_url}}">同类商品 ></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Self_drive Travel-->
        <div class="hot-lanes ">
            <div class="title">
                <h2>自驾线路</h2>
                <span>全球最美自驾线路</span>
            </div>
            <div class="lane-content">
                <div class="line-item" ms-repeat-product="drive_lanes">
                    <a target="_blank" ms-href="product.link_url">
                        <div class="second-bg"></div>
                        <div class="name-block clearfix">
                            <div class="left-content">
                                <div class="product-name">{{ product.product_name || product.description.name }}</div>
                                <div class="line-city" ms-if="product.tour_cities">
                                    <span class="line-city-text">途经城市:</span>
                                    <span class="line-city-names">{{ product.tour_cities }}</span>
                                </div>
                                <div class="line-bottom">
                                    <span class="icon icon-car car-icon"></span>
                                    <span class="show-block line-days">{{ product.tour_days }}</span>
                                    <span class="show-save">天</span>
                                    <div class="show-line"></div>
                                    <span class="show-block show-unit">￥</span>
                                    <span class="show-price">{{ product.show_prices.price }}/</span>
                                    <span class="show-save">{{ product.show_prices.title }}</span>
                                    <span class="show-block show-save">节省{{ product.show_prices.orig_price - product.show_prices.price }}元</span>
                                </div>
                            </div>
                            <div class="right-content">
                                <img class="right-cover" ms-if="product.line_image_url" ms-src="{{ product.line_image_url }}?imageView/1/w/125/h/110">
                            </div>
                        </div>
                        <img class="line-cover" ms-src="{{ product.group_cover_image || product.cover_image.image_url }}?imageView/1/w/470/h/150">
                    </a>
                </div>
            </div>
        </div>

        <!--Hot Hotels-->
        <div class="hot-hotels hot-lanes">
            <div class="title">
                <h2>Hotel+</h2>
                <span>不只是酒店</span>
            </div>
            <div class="hotel-content">
                <a class="product-item product-card" ms-repeat-product="hotels"
                   ms-href="product.link_url" target="_blank">
                    <div class="product-wrap clearfix">
                        <img class="product-image" ms-src="{{ product.cover_image.image_url }}?imageView/1/w/300/h/170" />
                        <div class="name-block">
                            <div class="name-text">{{ product.description.name }}</div>
                            <div class="hotel-level">酒店星级
                                <i class="i icon-rating-star star-inside" ms-css-width="64 * (product.star_level / 5) + 'px'"></i>
                            </div>
                            <div class="benefit-text">{{ product.description.benefit }}</div>
                        </div>
                        <div class="price-block">
                            &yen;<em>{{ product.show_prices.price }}</em>
                        </div>
                        <div class="sales-block">
                            <i class="i icon-location"></i>
                            <span class="sales_volume">{{ product.location }}</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="themes/public/views/homeExt/javascripts/guide2345.js"></script>