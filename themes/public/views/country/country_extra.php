<div class="page-country-extra" ms-controller="countryExtraCtrl">

    <div class="country-extra-header"
         ms-css-background-image="url({{ data.country.country_image.cover_url }}?imageView/5/w/1400/h/300)">
        <div class="wrap">
            <h1 class="country-extra-name">
                <div class="cn-name">{{ data.country.cn_name }}</div>
                <div class="en-name">{{ data.country.en_name }}</div>
            </h1>
        </div>
        <div class="country-extra-nav" ms-class-1="has-secondary: local.has_secondary_nav">
            <div class="wrap">
                <div class="nav-wrap">
                    <div class="nav-item" ms-repeat-item="data.tabs" ms-class-1="active : $index == local.active_nav" ms-class-2="border-left : $index != 0" ms-click="switchNavTab($index)">
                        {{ item.name }}
                    </div>
                </div>
            </div>
            <div class="secondary" ms-if="local.has_secondary_nav">
                <div class="wrap">
                    <div class="nav-wrap">
                        <a class="nav-item" ms-repeat-city="local.current_cities" ms-class-2="border-left : $index == 0" ms-href="{{ city.city_link }}">
                            {{ city.city_name }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="country-extra-tab-content" ms-class-1="has-secondary: local.has_secondary_nav">
        <div class="content-item" ms-repeat-tab="data.tabs" data-repeat-rendered="renderCallback">
            <div ms-if="local.active_nav == $index">
                <div class="country-desc-row wrap">
                    <div class="desc-title">{{ tab.title }}</div>
                    <p class="desc-subtitle">{{ tab.brief }}</p>
                    <p class="desc-text">{{ tab.description }}</p>
                </div>
                <div class="tab-groups">
                    <div class="group" ms-repeat-group="tab.groups" ms-class="odd : $index % 2 == 0">
                        <!--  一般分组 -->
                        <div class="wrap common-group" ms-if="group.type != 6">
                            <div class="group-header clearfix">
                                <div class="header-img-col" ms-visible="group.cover_image_url != ''">
                                    <img ms-src="{{ group.cover_image_url }}?imageView2/5/w/135">
                                </div>
                                <div class="header-intro-col">
                                    <div class="group-name-row">
                                        <h2 class="group-name">
                                            <a ms-visible="group.city_link_url" ms-href="group.city_link_url">
                                                {{ group.name }}
                                            </a>
                                            <span ms-visible="!group.city_link_url">{{ group.name }}</span>
                                        </h2>
                                        <span class="group-subtitle">{{ group.summary }}</span>
                                    </div>
                                    <div class="group-desc">{{ group.description }}</div>
                                </div>
                            </div>
                            <div class="group-tabs" ms-visible="group.type == 5">
                                <div class="group-tab" ms-repeat-tab="group.refs"
                                     ms-class-1="active: $index == group.active_tab"
                                     ms-class-2="border-left: $index == 0"
                                     ms-click="switchGroupTab(group, $index)">{{ tab.detail.name }}
                                </div>
                            </div>
                            <div class="group-content">
                                <!-- 商品分组 -->
                                <div class="hi-carousel scroll group-wrap normal-group"
                                     ms-attr-id="group_{{ group.group_id }}"
                                     ms-if="group.type == 1">
                                    <div class="overflow-hidden">
                                        <div class="carousel-list">
                                            <div class="carousel-item normal-item" ms-repeat-product="group.refs">
                                                <?php include(dirname(__FILE__) . '/particals/normal_products.php'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="to to-prev icon-arrow-left"></div>
                                    <div class="to to-next icon-arrow-right"></div>
                                </div>
                                <!-- 线路分组 -->
                                <div class="hi-carousel scroll group-wrap tour-group"
                                     ms-attr-id="group_{{ group.group_id }}"
                                     ms-if="group.type == 2">
                                    <div class="overflow-hidden">
                                        <div class="carousel-list">
                                            <div class="carousel-item tour-item"
                                                 ms-repeat-tour="group.refs">
                                                <?php include(dirname(__FILE__) . '/particals/tour_products.php'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="to to-prev icon-arrow-left"></div>
                                    <div class="to to-next icon-arrow-right"></div>
                                </div>
                                <!-- 文章文组 -->
                                <div class="hi-carousel scroll group-wrap article-group"
                                     ms-attr-id="group_{{ group.group_id }}"
                                     ms-if="group.type == 3">
                                    <div class="overflow-hidden">
                                        <div class="carousel-list">
                                            <div class="carousel-item article-item" ms-repeat-article="group.refs">
                                                <?php include(dirname(__FILE__) . '/particals/article_products.php'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="to to-prev icon-arrow-left"></div>
                                    <div class="to to-next icon-arrow-right"></div>
                                </div>
                                <!-- 城市文组 -->
                                <div class="hi-carousel scroll group-wrap city-group"
                                     ms-attr-id="group_{{ group.group_id }}"
                                     ms-if="group.type == 4">
                                    <div class="overflow-hidden">
                                        <div class="carousel-list">
                                            <div class="carousel-item city-item" ms-repeat-city="group.refs">
                                                <?php include(dirname(__FILE__) . '/particals/city_products.php'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="to to-prev icon-arrow-left"></div>
                                    <div class="to to-next icon-arrow-right"></div>
                                </div>
                                <!-- 有二级分组 -->
                                <div class="sub_groups" ms-if="group.type == 5">
                                    <div class="sub_group" ms-repeat-subgroup="group.refs">
                                        <!-- 商品分组 -->
                                        <div class="hi-carousel scroll group-wrap normal-group"
                                             ms-attr-id="group_{{ group.group_id }}_{{ $index }}"
                                             ms-if="subgroup.detail.type == 1"
                                             ms-visible="group.active_tab == $index">
                                            <div class="overflow-hidden">
                                                <div class="carousel-list">
                                                    <div class="carousel-item normal-item"
                                                         ms-repeat-product="subgroup.detail.refs">
                                                        <?php include(dirname(__FILE__) . '/particals/normal_products.php'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="to to-prev icon-arrow-left"></div>
                                            <div class="to to-next icon-arrow-right"></div>
                                        </div>
                                        <!-- 线路分组 -->
                                        <div class="hi-carousel scroll group-wrap tour-group"
                                             ms-attr-id="group_{{ group.group_id }}_{{ $index }}"
                                             ms-if="subgroup.detail.type == 2"
                                             ms-visible="group.active_tab == $index">
                                            <div class="overflow-hidden">
                                                <div class="carousel-list">
                                                    <div class="carousel-item tour-item"
                                                         ms-repeat-tour="subgroup.detail.refs">
                                                        <?php include(dirname(__FILE__) . '/particals/tour_products.php'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="to to-prev icon-arrow-left"></div>
                                            <div class="to to-next icon-arrow-right"></div>
                                        </div>
                                        <!-- 文章文组 -->
                                        <div class="hi-carousel scroll group-wrap article-group"
                                             ms-attr-id="group_{{ group.group_id }}_{{ $index }}"
                                             ms-if="subgroup.detail.type == 3"
                                             ms-visible="group.active_tab == $index">
                                            <div class="overflow-hidden">
                                                <div class="carousel-list">
                                                    <div class="carousel-item article-item"
                                                         ms-repeat-article="subgroup.detail.refs">
                                                        <?php include(dirname(__FILE__) . '/particals/article_products.php'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="to to-prev icon-arrow-left"></div>
                                            <div class="to to-next icon-arrow-right"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="more-row" ms-visible="group.city_link_url">
                                <a class="go-city-link" ms-href="group.city_link_url">{{ group.city_cn_name }}<span> 更多精彩 ></span></a>
                            </div>
                        </div>
                        <!-- 广告条 -->
                        <a class="banner-group" ms-href="group.link_url" ms-if="group.type == 6">
                            <div class="banner-wrap"
                                 ms-css-background-image="url({{ group.cover_image_url }}?imageView/5/w/1390/h/400)">
                            </div>
                            <div class="image-cover">
                                <div class="banner-title">{{ group.name }}</div>
                                <div class="banner-text">{{ group.summary }}</div>
                                <div class="banner-btn">
                                    <div class="btn-text">去看看</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
