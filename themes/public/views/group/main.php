<div class="city-content" ms-controller="cityInfo">
    <div class="city-header">
        <h1 class="container">{{ cn_name }}&nbsp;<span>{{ en_name }}</span></h1>
    </div>
    <div class="nav" ms-controller="cityNav">
        <div class="tab-head">
            <div class="container">
                <div class="tab-head-ctn clearfix">
                    <div ms-repeat-tab="nav_tabs">
                        <div class="tab-unit" ms-if="tab.type <= 99">
                            <a ms-href="{{ city.link_url }}#0">{{ tab.name }}</a>
                        </div>
                        <div class="tab-unit" ms-if="tab.type > 99">
                            <a ms-href="{{ city.link_url }}#1">{{ tab.name }}</a>
                            <i>/</i>
                            <a class="tab-item groups-dropdown with-dropdown active">{{ current_name }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-body">
            <div class="container" ms-repeat-tab="nav_tabs">
                <div class="groups-dropdown" ms-if="$index == 1" ms-class="{{ tab.class_name }}">
                    <ul class="group-list clearfix">
                        <li class="clearfix" ms-repeat-gp="tab.groups"
                            ms-class-1="active: gp.group_id == current_group_id" ms-class-2="rec: gp.type == '2'">
                            <a ms-href="gp.link_url" class="gp-name">{{ gp.name }}</a>
                            <i class="gp-count">{{ gp.products_count }}</i>
                        </li>
                    </ul>
                    <div class="main-rec">
                        <a href="#"><img
                                src="http://hitour.qiniudn.com/ad77546a10852f0532c9555acb7dccae.jpg?imageView/5/w/235/h/288"></a>
                        <a href="#"><img
                                src="http://hitour.qiniudn.com/ad77546a10852f0532c9555acb7dccae.jpg?imageView/5/w/235/h/288"></a>
                        <a href="#"><img
                                src="http://hitour.qiniudn.com/ad77546a10852f0532c9555acb7dccae.jpg?imageView/5/w/235/h/288"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="group-intro" ms-if="group_type != 1 && group_type != 2">
        <div class="container">
            <h3>{{ group_name }}</h3>
            <p>{{ group_desc }}</p>
        </div>
    </div>
    <div class="main-content" ms-controller="cityNav">
        <div class="container">
            <div class="product-list tab-content clearfix">
                <div class="product-item" ms-repeat-pd="current_products">
                    <a ms-href="pd.link_url" target="_blank" class="item-link">
                        <div class="cover-img" ms-if="$index % 10 == 0 || $index % 10 == 6" ms-css-background-image="url({{pd.cover_image.image_url}}?imageView/1/w/655/h/310)"></div>
                        <div class="cover-img" ms-if="$index % 10 != 0 && $index % 10 != 6" ms-css-background-image="url({{pd.cover_image.image_url}}?imageView/1/w/310/h/310)"></div>
                        <div class="price">&yen;<em>{{ pd.show_prices.price }}</em></div>
                        <div ms-class="activity-tag-{{pd.activity_info.activity_id}}" ms-if="pd.activity_info && pd.activity_info.length != 0 && pd.activity_info.show_activity_tag == 1">
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
                        <div class="favor-tip"></div>
                    </a>

                    <div class="item-info">
                        <h2><a ms-href="pd.link_url">{{ pd.description.name }}</a></h2>

                        <div class="benefit-tag" ms-if="pd.description.benefit"><span>{{ pd.description.benefit }}</span></div>
                        <div class="favorite-ctn">
                            <div ms-class-1="icon-heart-empty:pd.is_favorite == 0" ms-class-2="icon-heart-filled:pd.is_favorite == 1" ms-click="toggleFavor(pd,$index)"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include( dirname(__FILE__).'/../city/_city-more.php' ); ?>
</div>