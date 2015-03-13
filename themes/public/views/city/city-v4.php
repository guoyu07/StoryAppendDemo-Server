<div class="page city" ms-controller="cityCtrl">

    <?php include(dirname(__FILE__) . '/modules/city_common_header/city_common_header.php'); ?>
    <div class="city-content wrap">
        <div class="card-row">
            <div class="side-bar card-5">
                <div class="cate-wrap">
                    <div class="cate cate-v2" ms-if="data.v2_tree && data.v2_tree.length > 0">
                        <div class="cate-title"><span>精选自由行方案</span></div>
                        <ul class="cate-A">
                            <li class="cate-A-item" ms-repeat-c="data.v2_tree"
                                ms-class-1="active : local.current_v2_id == c.id"
                                ms-class-2="item-hover : local.current_v2_id != c.id"
                                ms-click="v2Redirect(c.id)">
                                {{ c.label }}
                            </li>
                        </ul>
                    </div>
                    <div class="cate cate-group" ms-if="data.group_tree.sub_groups && data.group_tree.sub_groups.length > 0">
                        <div class="cate-title"><span>发现 -- {{ data.city.cn_name }}</span></div>
                        <ul class="cate-B">
                            <li class="cate-B-item" ms-repeat-b="data.group_tree.sub_groups"
                                ms-class-1="active : data.group_id == b.group_id && local.is_search_result == false"
                                ms-class-2="item-hover : data.group_id != b.group_id"
                                ms-click="groupRedirect(b.link_url)">
                                <span>{{ b.name }}</span><i>({{ b.product_count }})</i>
                            </li>
                        </ul>
                    </div>
                    <div class="cate cate-tag" ms-if="data.tag_tree && data.tag_tree.length > 0">
                        <div class="cate-title"><span>分类浏览</span></div>
                        <div class="tags-wrap">
                            <div class="cate-A" ms-repeat-a="data.tag_tree">
                                <div class="cate-A-item"
                                     ms-class-1="active : local.current_tag_id == a.en_name"
                                     ms-class-2="item-hover : local.current_tag_id != a.en_name"
                                     ms-click-1="tagRedirect(a.en_name, a.tag_id)" ms-click-2="showSubTags(this)">
                                    <span>{{ a.name }}</span><i>({{ a.product_count }})</i>
                                </div>
                                <ul class="cate-B none" ms-if="a.sub_tags && a.sub_tags.length > 0">
                                    <li class="cate-B-item" ms-repeat-b="a.sub_tags"
                                        ms-class-1="active : local.current_tag_id == b.en_name"
                                        ms-class-2="item-hover : local.current_tag_id != b.en_name"
                                        ms-click="tagRedirect(b.en_name, b.tag_id)">
                                        <span>{{ b.name }}</span><i>({{ b.product_count }})</i>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="products card-12 card-15-lg city-v2" ms-if="!data.type && data.use_v2">
                <div class="products-groups hotel-plus" ms-if="data.hotel_plus">
                    <div class="product-list card-row">
                        <a class="product-item card-12" ms-href="data.hotel_plus.promotion_url" target="_blank">
                            <div class="name-block">
                                <div class="name">
                                    <p class="hotel-plus-content">HOTEL +</p>
                                    <h2 class="hotel-plus-title">{{ data.city.cn_name }}酒店套餐，不止是酒店</h2>
                                    <p class="product-desc">落地后的一切事宜，hotel+帮您一站式搞定</p>
                                </div>
                                <div class="counts">
                                    <div class="counts-center">
                                        <span class="show-unit">{{ data.hotel_plus.hotel_count }}家精选酒店</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="products-groups top-10" ms-if="data.top_10">
                    <div class="group-title clearfix">
                        <h3 class="group-name">{{ data.top_10.name }}</h3>
                    </div>
                    <div class="first-row card-row">
                        <a class="product-item product-card card-7-lg" ms-repeat-product="data.top_10.products"
                           ms-href="product.link_url" target="_blank">
                            <div class="product-wrap clearfix">
                                <img class="product-image" ms-src="{{ product.cover_image.image_url }}?imageView/5/w/245/h/170" />
                                <div class="over-image">
                                    <?php include(dirname(__FILE__) . '/_special-tag.php'); ?>
                                </div>
                                <div class="name-block">
                                    <div class="name-text">{{ product.description.name }}</div>
                                    <div class="benefit-text">{{ product.description.benefit }}</div>
                                </div>
                                <div class="price-block">
                                    &yen;<em>{{ product.show_prices.price }}</em>
                                    <span class="old-price-text">市场价</span>
                                    <span class="old-price-gold">&yen;{{ product.show_prices.orig_price }}</span>
                                </div>
                                <div class="sales-block">
                                    已售
                                    <span class="sales_volume">{{ product.sales_volume }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="more-products-group products-list card-row" ms-if="local.has_more_products.top_10">
                        <a class="product-item card-12 card-7-lg" ms-repeat-product="data.top_10.more_products"
                           ms-href="product.link_url" target="_blank">
                            <?php include(dirname(__FILE__) . '/_product-wrap.php'); ?>
                        </a>
                    </div>
                    <div class="more-indicator" ms-if="local.has_more_products.top_10" ms-click="toggleShow('top_10')">
                        <span ms-visible="local.is_show_more.top_10">
                            收起
                            <span class="icon icon-arrow-up switch-icon"></span>
                        </span>
                        <span ms-visible="!local.is_show_more.top_10">
                            查看更多
                            <span class="icon icon-arrow-down switch-icon"></span>
                        </span>
                    </div>
                </div>
                <div class="products-groups package" ms-if="data.package">
                    <div class="group-title clearfix">
                        <h3 class="group-name">{{ data.package.name }}</h3>
                    </div>
                    <div class="products-list card-row">
                        <a class="product-item card-12" ms-repeat-product="data.package.products"
                           ms-href="product.link_url" target="_blank">
                            <div class="second-bg"></div>
                            <div class="name-block">
                                <div class="product-icon icon-bed icon"></div>
                                <div class="name">
                                    <h2 class="product-name">{{ product.description.name }}</h2>

                                    <p class="product-desc" ms-visible="product.product_count > 0">
                                        附赠{{product.product_count}}张热门景点门票
                                    </p>
                                </div>
                                <div class="price">
                                    <div class="price-center">
                                        <span class="show-unit">￥</span>
                                        <span class="show-price">{{ product.show_prices.price }}/</span>
                                        <span class="show-unit">{{ product.show_prices.title }}</span>
                                    </div>
                                    <div class="price-center">
                                        <span class="show-save">节省</span>
                                        <span
                                            class="show-save">{{ product.show_prices.orig_price - product.show_prices.price }}元</span>
                                    </div>
                                </div>
                            </div>
                            <img class="package-cover"
                                 ms-src="{{ product.group_cover_image || product.cover_image.image_url }}?imageView/5/w/760/h/135">
                        </a>
                    </div>
                    <div class="more-products-group card-row" ms-if="local.has_more_products.package">
                        <a class="product-item card-6" ms-repeat-product="data.package.more_products" ms-href="product.link_url"
                           target="_blank">
                            <div class="name-block">
                                <span class="icon icon-bed"></span>
                                <span class="product-name">{{ product.description.name }}</span>
                            </div>
                            <img class="package-cover"
                                 ms-src="{{ product.group_cover_image || product.cover_image.image_url }}?imageView/5/w/375/h/135">
                        </a>
                    </div>
                    <div class="more-indicator" ms-if="!data.hotel_plus && local.has_more_products.package" ms-click="toggleShow('package')">
                                    <span ms-visible="local.is_show_more.package">
                                        收起
                                        <span class="icon icon-arrow-up"></span>
                                    </span>
                                    <span ms-visible="!local.is_show_more.package">
                                        查看更多
                                        <span class="icon icon-arrow-down"></span>
                                    </span>
                    </div>
                    <a class="more-indicator" ms-if="data.hotel_plus" ms-href="data.hotel_plus.promotion_url" target="_blank">
                                    <span ms-visible="!local.is_show_more.package">
                                        查看更多
                                        <span class="icon icon-arrow-down"></span>
                                    </span>
                    </a>
                </div>
                <div class="products-groups line" ms-if="data.line">
                    <div class="group-title clearfix">
                                    <h3 class="group-name">{{ data.line.name }}</h3>
                                </div>
                    <div class="line-item" ms-repeat-product="data.line.display_product">
                        <a  ms-href="product.link_url">
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
                                                <img class="right-cover" ms-if="product.line_image_url" ms-src="{{ product.line_image_url }}?imageView/5/w/230/h/200">
                                            </div>
                                        </div>
                            <img class="line-cover" ms-src="{{ product.group_cover_image || product.cover_image.image_url }}?imageView/5/w/770/h/232">
                        </a>
                    </div>
                    <a ms-href="data.line.link_url">
                        <div class="more-indicator" ms-if="data.line.link_url && data.line.product_count > 4">
                                        <span>
                                            查看更多
                                            <span class="icon icon-arrow-down"></span>
                                        </span>
                        </div>
                    </a>
                </div>
                <div class="products-groups experience" ms-if="data.experience">
                    <div class="group-title clearfix">
                        <h3 class="group-name">{{ data.experience.name }}</h3>
                    </div>
                    <div class="articles-list clearfix">
                        <div ms-repeat-article="data.experience.data">
                            <a class="article-item round-card card-6"
                               ms-href="{{ data.city.country_name }}/{{ data.city.city_name }}/group/{{ article.link_to }}"
                               ms-if="article.link_to && article.group">
                                <div class="name-wrap">
                                    <div class="name-block clearfix">
                                        <div class="article-name">{{ article.title }}</div>
                                        <div class="article-link">{{ article.product_count }}种体验 ></div>
                                    </div>
                                </div>
                                <img class="article-cover"
                                     ms-src="{{ article.group_cover_image ? article.group_cover_image : article.head_image_url }}?imageView/5/w/230/h/274"/>
                            </a>
                            <a class="article-item round-card card-6"
                               ms-href="article.link_url" target="_blank" ms-if="!(article.link_to && article.group)">
                                <div class="name-wrap">
                                    <div class="name-block clearfix">
                                        <div class="article-name">{{ article.title }}</div>
                                        <div class="article-link">{{ article.product_count }}种体验 ></div>
                                    </div>
                                </div>
                                <img class="article-cover"
                                     ms-src="{{ article.group_cover_image ? article.group_cover_image : article.head_image_url }}?imageView/5/w/240/h/274"/>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="products card-12 card-15-lg" ms-if="data.type || (!data.type && !data.use_v2)">
                <div class="products-groups" ms-repeat-group="data.products_groups" data-repeat-rendered="renderCallback">
                    <div class="group-title" ms-if="group.name">
                        <h3 class="group-name">{{ group.name }}</h3>
                        <p class="group-desc">{{ group.description }}</p>
                    </div>
                    <a ms-href="data.article.link_url" target="_blank" ms-if="data.article && local.is_search_result == false">
                        <div class="group-article">
                            <img class="card-cover"
                                 ms-src="{{ data.article.group_cover_image ? data.article.group_cover_image : data.article.head_image_url }}?imageView2/5/w/706/h/146" />
                            <div class="text-shadow-radial clearfix">
                                <div class="article-city">
                                    <div>{{ data.city.cn_name }}</div>
                                    <div>就该这么玩</div>
                                </div>
                                <div class="article-line"></div>
                                <div class="article-link">
                                    查看微攻略
                                </div>
                                <div class="article-name">
                                    <span>{{ data.article.title }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    <div class="search-result-header clearfix" ms-if="local.is_search_result">
                        <div class="result-title">与 <span>{{ local.search_key_cache }}</span> 相关的商品</div>
                        <div class="result-count">{{ local.search_result_length }}个结果</div>
                    </div>
                    <div class="search-none-ps" ms-if="group.products.length <= 0 && local.is_search_result">抱歉！没有找到关于“<span>{{ local.search_key_cache }}</span>”的相关的商品。</div>
                    <div class="products-list card-row">
                        <a class="product-item card-12 card-7-lg" ms-repeat-product="group.products"
                           ms-href="product.link_url" target="_blank"
                           ms-visible="$index < local.product_length_limit.limit_length">
                            <?php include(dirname(__FILE__) . '/_product-wrap.php'); ?>
                        </a>
                        <div class="group-more-btn"
                             ms-if="local.product_length_limit.limit_mode == 'cates' && group.product_count > local.product_length_limit.limit_length"
                             ms-click="sectionRedirect(group)">
                            查看更多 <i class="icon-arrow-right"></i>
                        </div>
                        <div class="load-more-btn"
                             ms-if="local.product_length_limit.limit_mode == 'cate' && local.product_length_limit.left_toload > 0">
                            还有{{ local.product_length_limit.left_toload }}个旅游体验哦！
                        </div>
                    </div>
                </div>
            </div>
            <div class="loading-indicator card-12"></div>
        </div>
    </div>


    <!-- 回到顶部 收藏 浮块 -->
    <?php include(dirname(__FILE__) . '/../common/modules/back_to_top/back_to_top.php'); ?>
    <!--<div class="back-top-simi icon-rocket"></div>-->

</div>





