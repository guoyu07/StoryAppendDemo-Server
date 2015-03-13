<div class="city-content" ms-controller="cityInfo">
    <div class="city-header">
        <h1 class="container"><span>{{ cn_name }}&nbsp;<span>{{ en_name }}</span></span></h1>
    </div>
    <div class="nav" ms-controller="allNav">
        <div class="tab-head container">
            <div class="tab-head-ctn clearfix">
                <a ms-href="tab.link_url" class="tab-item" ms-repeat-tab="nav_tabs"
                   ms-class-1="active: $index == 0" ms-class-2="rec: tab.type == '2'">
                    <span class="gp-name">{{ tab.name }}</span>
                    <i class="gp-count">{{ tab.products_count }}</i>
                </a>
            </div>
        </div>
    </div>
    <div class="main-content" ms-controller="cityNav">
        <div class="container" ms-repeat-tab="nav_tabs">
            <div class="product-list tab-content clearfix" ms-if="tab.type < 100" ms-class="none: $index != 0">
                <div class="product-item" ms-repeat-pd="tab.products">
                    <a ms-href="pd.link_url" target="_blank" class="item-link">
                        <div class="cover-img" ms-if="$index % 10 == 0 || $index % 10 == 6" ms-css-background-image="url({{pd.cover_image.image_url}}?imageView/1/w/655/h/310)"></div>
                        <div class="cover-img" ms-if="$index % 10 != 0 && $index % 10 != 6" ms-css-background-image="url({{pd.cover_image.image_url}}?imageView/1/w/310/h/310)"></div>
                        <div class="price">&yen;<em>{{ pd.show_prices.price }}</em></div>
                        <div ms-class="activity-tag-{{pd.activity_info.activity_id}}" ms-if="pd.activity_info && pd.activity_info.length != 0 && pd.activity_info.show_activity_tag==1">
                            <img ms-src="themes/public/images/activities/activity_tag_{{pd.activity_info.activity_id}}.png">

                        </div>
                        <div class="special-tag" ms-if="(!pd.activity_info || pd.activity_info.length == 0 || pd.activity_info.show_activity_tag != 1) && pd.show_prices.special_info">
                            <span>{{ pd.show_prices.special_info.reseller }}</span><br>
                            <span>{{ pd.show_prices.special_info.slogan }}</span><br>
                            <span>{{ (1 - pd.show_prices.price / pd.show_prices.orig_price) * 100 | number(0) }}% OFF</span>
                        </div>
                        <div class="item-hover">
                            <h3>{{pd.description.name}}</h3>
                            {{pd.description.service_include | html}}
                        </div>
                    </a>
                    <div class="item-info">
                        <h2><a ms-href="pd.link_url">{{ pd.description.name }}</a></h2>
                        <div class="benefit-tag" ms-if="pd.description.benefit"><span>{{ pd.description.benefit }}</span></div>
                        <div class="favorite-ctn">
                            <div ms-class-1="icon-heart-empty:pd.is_favorite == 0" ms-class-2="icon-heart-filled:pd.is_favorite == 1" ms-click="toggleFavor(pd,$index)"></div>
                        </div>
                    </div>
                    <div class="favor-tip"></div>
                </div>
            </div>
            <div class="group-list tab-content clearfix" ms-if="tab.type >= 100" ms-class="none: $index != 0">
                <div class="group-item" ms-repeat-gp="tab.groups">
                    <a ms-href="gp.link_url" class="item-img"
                       ms-css-background-image="url({{gp.cover_image_url}}?imageView/1/w/310/h/310)">
                        <h2>{{ gp.name }}</h2>
                        <div class="service-count">{{ gp.products.length }}个体验</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php include_once( '_city-more.php' ); ?>
</div>