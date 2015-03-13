<div class="page-product" ms-controller="productInfo">
    <div class="main-wrap">
        <section class="product-main">
            <div class="nav-ctn">
                <nav>
                    <a href="<?= $this->createUrl('home/index'); ?>">首页</a><span class="sep">></span>
                    <a ms-attr-href="{{country_url}}" ms-text="country_name"></a><span class="sep">></span>
                    <a ms-attr-href="{{city_url}}" ms-text="city_name"></a>
                    <div class="tel-me">您有任何问题，请拨打：<em>400-010-1900</em></div>
                </nav>
            </div>
            <div class="product-title">
                <h1 ms-html="description.name"></h1>
                <div class="favorite-ctn">
                     <span ms-class-1="icon-heart-empty:is_favorite == 0"
                           ms-class-2="icon-heart-filled:is_favorite == 1"
                           ms-click="toggleFavor(this.$vmodel, 0)">{{ is_favorite == 0?'加入收藏':'已收藏' }}</span>
                </div>
                <div class="user-mark"></div>
            </div>
            <?php include(dirname(__FILE__) . '/modules/buy_section/buysection.php'); ?>
            <div class="ad-ctn" ms-if="ad_info && ad_info.length != 0">
                <a id="summer_link" ms-attr-href="ad_info.link_url" target="_blank">
                    <img class="qr-code" ms-if="ad_info.qr_code_link" ms-src="{{ad_info.qr_code_link}}">
                    <img ms-src="ad_info.image_url">
                </a>
            </div>
            <div class="tab-ctn">
                <div class="fix-wrap">
                    <div class="container">
                        <div class="hi-nav left-ctn" data-bind="nav-content" ms-controller="buyNotice">
                            <div class="nav-item active" data-target="tab0">商品信息</div>
                            <div class="nav-item" data-target="tab_service_include" ms-controller="productNoticeCtrl"
                                 ms-if="data.please_read">服务包含
                            </div>
                            <div class="nav-item" data-target="tab_please_read">购买须知</div>
                            <div class="nav-item" data-target="tab_redeem_usage" ms-controller="productNoticeCtrl"
                                 ms-if="data.please_read">
                                兑换及使用
                            </div>
                            <div class="nav-item" data-target="tab_comments" ms-controller="productCommentsCtrl"
                                 ms-if="data.comments.length > 0">
                                用户点评<span>（{{ data.score.avg_hitour_service_level }}分）</span>
                            </div>
                        </div>
                        <div class="right-ctn">
                            <div class="price-ctn">
                                <div>
                                    <span ms-text="show_prices.title"></span>
                                    <span class="price-wrap">
                                        <strong ms-html="'&yen;'+show_prices.price"></strong>
                                    </span>
                                </div>
                            </div>
                            <button class="fixed-buy-btn" ms-click="openBuy">{{ buy_label }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 服务包含和地图 -->
        <section class="service_include-map" ms-if="type == 3 || type == 4 || type == 5">
            <div class="clearfix">
                <div class="map-img" ms-css-background-image="url({{gmap_url}})">
                    <div class="map-point">游玩项目</div>
                </div>
                <div class="service_include" ms-controller="buyNotice">
                    <div ms-repeat-el="service_include">
                        <div ms-if="$index == 0">
                            <div class="service-title">{{ el.key|html }}</div>
                            {{ el.val|html }}
                            <div class="in-cover"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="hi-tab-content" id="nav-content">
            <!-- multiday_tour -->
            <div ms-if="type == 9 && multi_day_general.recommendation" class="content-item" id="tab0" ms-controller="productTourCtrl">
                <?php include(dirname(__FILE__) . '/modules/product_tour/product_tour.php'); ?>
            </div>

            <!-- 商品详情 -->
            <section class="content-item product-scenes" ms-if="type != 9 || !multi_day_general.recommendation"
                     ms-controller="productScenes" id="tab0">
                <div class="section-title" ms-if="type == 3 || type == 4">
                    <h2>商品详情</h2>
                    <div class="back-line"></div>
                </div>
                <div class="desc-ctn"><span>{{description.description}}</span></div>

                <!--  通票包含景点  -->
                <div class="section-title" ms-if="landinfo_groups.length > 0 && type == 3">
                    <h2>{{album_info.album_name}}</h2>
                    <div class="back-line"></div>
                </div>
                <div class="scenes-ctn" ms-if="type == 3">
                    <div class="group-wrap" ms-repeat-gp="landinfo_groups">
                        <div class="scene" ms-repeat-el="gp">
                            <img ms-src="{{el.image_url}}?imageView2/1/w/458/h/205" alt="" />
                            <div class="scene-info">
                                <div class="scene-name-ctn">
                                    <h3>{{el.name}}</h3>
                                    <h4>{{el.en_name}}</h4>
                                </div>
                                <div class="seq">{{$outer.$index|seq($outer.gp.length)}}</div>
                                <p class="scene-summary">{{el.pass_benefit}}</p>
                                <p class="scene-desc">{{el.reason}}</p>
                            </div>
                        </div>
                        <div class="linked-circle" ms-if="gp.length>1">2选1</div>
                    </div>
                </div>

                <!--  行程计划  -->
                <div class="section-title" ms-if="tour_plan.length > 0 && tour_plan_type == 1">
                    <h2>{{description.schedule}}</h2>
                    <div class="back-line"></div>
                </div>
                <div class="tour-ctn" ms-if="tour_plan.length > 0 && tour_plan_type == 1">
                    <div class="brief" ms-if="tour_plan.length>1&&patch">
                        本旅行将包含<em>{{tour_plan.length}}</em>天的精彩内容，下面就让玩途小编详细的为大家介绍行程安排吧！
                    </div>
                    <div class="day-list" ms-if="tour_plan.length>1">
                        <ul>
                            <li ms-repeat-el="tour_plan"><i
                                    class="seq"><span>D{{$index+1}}</span></i><span>{{el.title}}</span></li>
                        </ul>
                    </div>
                    <div class="day-nav-ctn" ms-if="tour_plan.length>1" ms-mouseleave="mouseleave()">
                        <ul>
                            <li ms-repeat-el="tour_plan" ms-mouseover="mousein($index,el.title,$event)"
                                ms-mouseout="mouseout()"
                                ms-class-1="active:el.active" ms-click="switchDay(el)"
                                ms-class-3="no-line:$index%4==3||$last">
                                <i class="x-circle"></i><span>D{{ $index+1 }}</span>
                                <div class="dline"></div>
                            </li>
                            <div class="day-prompt" ms-controller="dayPrompt" ms-css-left="x" ms-css-top="y"
                                 ms-visible="show">
                                <span>{{ prompt }}</span><span class="v-mock">a</span>
                                <div class="arrow"></div>
                            </div>
                        </ul>
                    </div>
                    <div class="day-tour-ctn">
                        <div class="one-time" ms-repeat-el="dayTour">
                            <i class="x-clock"></i>
                            <div class="time-time" ms-visible="el.time">{{ el.time }}</div>
                            <div class="time-head">{{ el.title }}</div>
                            <div class="time-content">
                                <div class="time-content-wrap" ms-repeat-ct="el.items">
                                    <img ms-src="{{ct.image_url}}" alt=""
                                         onerror="this.parentNode.removeChild(this);" />
                                    <h4>{{ ct.title }}</h4>
                                    <p>{{ ct.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="day-nav-ctn" ms-if="tour_plan.length>1" ms-mouseleave="mouseleave()">
                        <ul>
                            <li ms-repeat-el="tour_plan"
                                ms-mouseover="mousein($index,el.title,$event)"
                                ms-mouseout="mouseout()"
                                ms-class-1="active:el.active" ms-click="switchDay(el)"
                                ms-class-3="no-line:$index%4==3||$last">
                                <i class="x-circle"></i><span>D{{ $index+1 }}</span>
                                <div class="dline"></div>
                            </li>
                            <div class="day-prompt" ms-controller="dayPrompt" ms-css-left="x" ms-css-top="y"
                                 ms-visible="show">
                                <span>{{ prompt }}</span><span class="v-mock">a</span>
                                <div class="arrow"></div>
                            </div>
                        </ul>
                    </div>
                </div>
                <div class="simple-tour-ctn" ms-if="tour_plan.length > 0 && tour_plan_type == 2">
                    <div class="tour-content" ms-repeat-el="tour_plan">
                        <div class="tour-content-ctn" ms-repeat-ct="el.items">
                            <div class="tour-content-wrap">
                                <h4 ms-visible="ct.title">{{ ct.title }}</h4>
                                <img ms-src="{{ct.image_url}}" alt="" onerror="this.parentNode.removeChild(this);" />
                                <p ms-if="ct.description">{{ ct.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-title" ms-if="communications.length>0||all_landinfo.length>0">
                    <h2>{{album_info.landinfo_md_title}}</h2>
                    <div class="back-line"></div>
                </div>
                <div class="one-communication" ms-visible="communications.length > 0" ms-repeat-item="communications">
                    <i class="icon icon-bus-circle-bg"></i>
                    <div class="communication-text">
                        <h3 class="title">{{ item.title }}</h3>
                        <p class="desc">{{ item.description | html }}</p>
                    </div>
                </div>
                <div class="all-land-ctn" ms-if="all_landinfo.length>0">
                    <div class="one-all-land" ms-class="first-all-land:$first" ms-repeat-el="all_landinfo">
                        <div class="all-land-name">
                            <h3>{{el.title}}</h3>
                        </div>
                        <div class="all-land-list">
                            {{el.list|html}}
                        </div>
                    </div>
                </div>
            </section>

            <!-- 买前必看 + 兑换及使用 老 -->
            <div class="content-item" ms-controller="productNoticeCtrl" id="tab_please_read" ms-if="!data.please_read">
                <section class="buy-notice tab-content" ms-controller="buyNotice">
                    <div id="buy_notice">
                        <div class="section-title">
                            <h2>服务包含/购买须知</h2>
                            <div class="back-line"></div>
                        </div>
                        <div class="product-rule col-ctn">
                            <ul>
                                <li class="r-redeem">
                                    <h3>兑换规则</h3>
                                    <p>{{rules.redeem_desc|html}}</p>
                                </li>
                                <li class="r-buy">
                                    <h3>购买时间</h3>
                                    <p>{{rules.sale_desc}}</p>
                                </li>
                                <li class="r-return">
                                    <h3>退款限制</h3>
                                    <p>{{rules.return_desc}}</p>
                                </li>
                                <li class="r-shipping">
                                    <h3>发货限制</h3>
                                    <p>{{rules.shipping_desc}}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="service-include col-ctn" ms-repeat-el="service_include" ms-class="no-border:$first">
                            <div class="left-column">{{el.key|html}}</div>
                            <div class="content-list plain-list">{{el.val|html}}</div>
                        </div>
                        <div class="how-it-works col-ctn" ms-repeat="how_it_works">
                            <div class="left-column">{{$key|html}}</div>
                            <div class="content-list plain-list">{{$val|html}}</div>
                        </div>
                    </div>
                    <div class="exchange-places" id="exchange-places">
                        <div class="section-title" ms-if="pick_landinfo_groups.length>0">
                            <h2>兑换地点</h2>
                            <div class="back-line"></div>
                        </div>
                        <div class="gmap-ctn" ms-if="pick_landinfo_groups.length>0">
                            <img ms-src="{{gmap_url}}" alt="" />
                        </div>
                        <div class="redeem-places col-ctn" ms-if="pick_landinfo_groups.length>0">
                            <div ms-repeat-el="pick_landinfo_groups">
                                <div class="left-column">{{el.title}}</div>
                                <div class="content-list plain-list">
                                    <div class="redeem-place" ms-repeat-pl="el.landinfos">
                                        <h3>{{pl.seq}}{{pl.name}}</h3>
                                        <p ms-if="pl.address">详细地址：{{pl.address}}</p>
                                        <p ms-if="pl.communication">到达方式：{{pl.communication}}</p>
                                        <p ms-if="pl.phone">联系电话：{{pl.phone}}</p>
                                        <p ms-if="pl.open_time">开放时间：{{pl.open_time}}</p>
                                        <p ms-if="pl.close_time">关闭时间：{{pl.close_time}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- 买前必看 + 兑换及使用 新 -->
            <?php include(dirname(__FILE__) . '/modules/product_notice/product_notice.php'); ?>

            <!-- 商品评论 -->
            <?php include(dirname(__FILE__) . '/modules/product_comments/product_comments.php'); ?>
        </div>

        <section class="hitour-guarantee" id="guarantee">
            <div class="container">
                <div class="section-title st-4">
                    <h2>出发！让你后顾无忧！</h2>
                </div>
                <div class="section-content row">
                    <div class="col-md-4">
                        <div class="mark">2</div>
                        <div class="detail">
                            <div class="guarantee-title">2倍赔偿</div>
                            <p>因玩途原因无法兑换服务<br>，可获全额退款，并获差<br>价双倍赔偿</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mark">10</div>
                        <div class="detail">
                            <div class="guarantee-title">10天保额</div>
                            <p>赠送为期10天，保额15万<br>的太平洋境外旅游意外险，<br>助您安心出发、平安归来</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mark">24</div>
                        <div class="detail">
                            <div class="guarantee-title">24小时</div>
                            <p>客服代表随时待命，海外<br>合作伙伴提供线下服务和<br>支援</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="hot-recommend" ms-controller="recommend" ms-if="products.length>0">
            <div class="container pr">
                <div class="section-title">
                    <h2>热门推荐</h2>
                    <div class="back-line"></div>
                </div>
                <div class="recommend-pro-list">
                    <div class="row"
                         ms-css-width="{{Math.ceil(products.length/4)*1020}}"
                         ms-css-left="-{{showIndex*1020}}"
                         msx-css-transform="translateX(-{{showIndex*1020}}px)">
                        <div class="product-block col-md-3" ms-repeat-el="products">
                            <a class="product" ms-attr-href="{{el.link_url}}">
                                <div class="cover-ctn">
                                    <img ms-attr-src="{{el.image_url}}?imageView/1/w/235/h/178" alt="" />
                                </div>
                                <div class="product-info">
                                    <div class="product-name">{{el.name|html}}</div>
                                </div>
                                <div class="price-ctn">
                                    <span class="price-wrap">￥{{el.show_prices.price}}</span>
                                    <span class="orig-price-wrap">￥{{el.show_prices.orig_price}}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="arrow-left" ms-if="products.length>4&&showIndex>0" ms-click="turnLeft"></div>
                <div class="arrow-right" ms-if="products.length>4&&showIndex<Math.ceil(products.length/4)-1" ms-click="turnRight"></div>
            </div>
        </section>
    </div>

    <!-- 购买 弹层 -->
    <div class="buy-mask"></div>

    <!-- 回到顶部 收藏 微信 浮块 -->
    <?php include(dirname(__FILE__) . '/../common/modules/back_to_top/back_to_top.php'); ?>

    <!-- _活动商品 活动规则提醒弹层 -->
    <div class="hi-modal" id="activity_tips_modal">
        <div class="full-overlay" data-close="activity_tips_modal"></div>
        <div class="modal-wrap" style="width: 720px; height: 407px; margin-left: -360px; margin-top: 100px;">
            <img data-close="activity_tips_modal" src="http://hitour.qiniudn.com/af0e13f52440120d2647fc04149aaf84.png">
            <div class="go-btn" data-close="activity_tips_modal"></div>
            <div class="close-btn" data-close="activity_tips_modal"></div>
        </div>
    </div>

    <!-- _多日行程商品 详细行程侧拉层 -->
    <div class="product-tour-detail" ms-controller="productTourDetailCtrl" ms-if="type == 9 && data.multi_day_general">
        <?php include(dirname(__FILE__) . '/modules/product_tour_detail/product_tour_detail.php'); ?>
    </div>
</div>


<script src="http://maps.google.cn/maps/api/js?libraries=geometry&sensor=false&key=AIzaSyAg8uuaMnzdPH46wxk9YRYC-eV277neBEQ"></script>
<script src="themes/public/lib/GoogleMap/hi.Gmap.js"></script>

