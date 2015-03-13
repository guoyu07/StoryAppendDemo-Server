<div class="main-wrap product-package" ms-controller="productPackage">
<div class="package-breadcrumb">
    <div class="container clearfix">
        <div class="breadcrumb-wrap">
            <span class="breadcrumb-item"><a href="<?= $this->createUrl('home/index'); ?>">首页</a></span>
            <span class="breadcrumb-item">> <a ms-href="productData.city.country.link_url">{{ productData.city.country_cn_name }}</a></span>
            <span class="breadcrumb-item">> <a ms-href="productData.city.link_url">{{ productData.city.cn_name }}</a></span>
            <span class="breadcrumb-item">- <span>酒店套餐详情</span></span>
        </div>
        <div class="phone-col">您有任何问题，请拨打：<em>400-010-1900</em></div>
    </div>
</div>
<div class="white-container">
    <div class="container">
        <!--Product Basic Information-->
        <section class="product-main">
            <div class="product-name">{{ basic_info.name }}</div>
            <!--Product Rate-->
            <div class="product-score title-score">
                <span>玩家评分</span>
                <span class="score">
                    <i class="star-back icon-rating-star"></i>
                    <i class="star-front icon-rating-star"
                       ms-css-width="calculatePercent(basic_info.avg_hitour_service_level, 5) + '%'"></i>
                </span>
                <span class="total">({{ basic_info.total }})</span>
            </div>
            <div class="hi-carousel" id="base_carousel">
                <div class="carousel-list">
                    <div class="carousel-item" ms-repeat-item="productData.images.sliders"
                         ms-class="active : $index == 0"
                         ms-css-background-image="url('{{ item.image_url }}?imageView/5/w/700/h/470')"
                         data-repeat-rendered="initSlider">
                        <div class="item-above">
                            <div class="text-wrap">
                                <h3 class="item-name">{{ item.name }}</h3>
                                <p class="item-desc">{{ item.short_desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="to to-prev"><i class="icon-arrow-left"></i></div>
                <div class="to to-next"><i class="icon-arrow-right"></i></div>
                <div class="index-list">
                    <i class="to to-index" ms-repeat-index="productData.images.sliders"
                       ms-class="active : $index == 0"></i>
                </div>
                <img ms-class="activity-tag-flashsale"
                     ms-if="basic_info.activity_info && basic_info.activity_info.length != 0 && basic_info.activity_info.show_activity_tag == 1
                 && (basic_info.activity_info.name == 'FlashSale' || basic_info.activity_info.name == 'FridaySale')"
                     src="themes/public/images/activities/activity_tag_flashsale.png">
                <img ms-class="activity-tag-{{activity_info.activity_id}}"
                     ms-if="basic_info.activity_info && basic_info.activity_info.length != 0 && basic_info.activity_info.show_activity_tag == 1
                 && !(basic_info.activity_info.name == 'FlashSale' || basic_info.activity_info.name == 'FridaySale')"
                     ms-src="themes/public/images/activities/activity_tag_{{basic_info.activity_info.activity_id}}.png">

            </div>
            <div class="product-basic">
                <div class="price-zone">
                        <span class="price-holder">
                          <span class="RMB">&yen;</span>
                          <span id="product_price" class="price">{{ basic_price.price }}</span>
                          <span class="appendix"> / 套起</span>
                        </span>
                    <span class="orig-price">市场价<br><span>&yen;{{ basic_price.orig_price }}</span></span>
                </div>
                <p class="ps-dropdown" ms-visible="hotel_info.length > 1">您可以点击查看不同酒店套餐选项</p>
                <div id="hitour_dropmenu" class="dropmenu-container" ms-if="hotel_info.length != 1">
                    <div class="dropmenu-init">{{ basic_price.hotel_name }}<i class="i icon-arrow-down"></i></div>
                    <div class="menu">
                        <div class="item" ms-repeat-hotel="hotel_info" ms-attr-index="$index"
                             ms-click="priceChange($index)">{{ hotel.cn_name }}
                        </div>
                    </div>
                </div>
                <div id="basic_service" class="service-included">
                    <div class="service-group">
                        <h2>
                            <i ms-class-2="icon-gift"></i>
                            <div>套餐包含</div>

                        </h2>
                        <ol>
                            <li ms-repeat-item="productData.description.package_service">{{ item }}</li>
                            <li ms-repeat-item="productData.description.package_gift">{{ item }}</li>
                        </ol>
                    </div>
                    <div class="service-group add-top-line">
                        <h2>
                            <i ms-class-2="icon-attach"></i>
                            <div>独享特惠</div>

                        </h2>
                        <ol>
                            <li ms-repeat-item="productData.description.package_recommend">{{ item }}</li>
                        </ol>
                    </div>
                </div>
                <div class="btn-zone">
                    <div class="favorite-ctn">
                        <span ms-class-1="icon-heart-empty:basic_info.is_favorite == 0"
                              ms-class-2="icon-heart-filled:basic_info.is_favorite == 1"
                              ms-click="toggleFavor(this.$vmodel.basic_info, 0)"></span>
                        加入收藏
                    </div>
                    <button id="buy_btn" class="buy-btn" ms-click="showBuyDialog()">{{ basic_info.buy_label }}</button>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="white-container">
    <!--Hitour Tab Component-->
    <div class="bundle-tab">
        <div class="nav-content">
            <div class="product-tab hi-nav" data-bind="nav-content">
                <a class="nav-item vertical-divider active" data-target="tab-package">套餐包含</a>
                <a class="nav-item vertical-divider" data-target="tab-optional">独享特惠</a>
                <a class="nav-item vertical-divider" data-target="tab_please_read">购买须知</a>
                <a class="nav-item" data-target="tab_redeem_usage" ms-controller="productNoticeCtrl"
                   ms-if="data.please_read">兑换及使用</a>

                <div id="fixed_fav" class="fixed-fav-btn" ms-class-1="icon-heart-empty:basic_info.is_favorite == 0"
                     ms-class-2="icon-heart-filled:basic_info.is_favorite == 1"
                     ms-click="toggleFavor(this.$vmodel.basic_info, 0)">
                </div>
                <button id="fixed_tab_button" class="fixed-tab-button" ms-click="showBuyDialog()">{{ basic_info.buy_label }}
                </button>
            </div>
        </div>
    </div>
    <div class="tab-content hi-tab-content" id="nav-content">
        <!-- tab-item 套餐包含块 -->
        <div class="package tab-item content-item container" id="tab-package">
            <div class="tab-title">
                <h2>套餐包含 {{ bundle.complimentary.products.length + 1 }} 项服务，共为您节省 {{ basic_price.orig_price - basic_price.price }}元</h2>
            </div>
            <!-- 可选酒店 -->
            <div class="package-item hotel">
                <h3>
                    <em>1.</em>
                    <span class="title">{{ bundle.hotel.title }}</span>
                    <span class="desc">{{ bundle.hotel.desc }}</span>
                </h3>
                <div class="hotel-list">
                    <div class="hotel-item" ms-repeat-hotel="bundle.hotel.products" data-repeat-rendered="initTabComponents">
                        <div class="hotel-item-box clearfix"
                             ms-click="bundleModel.showBundleModal(hotel.product_id, hotel.type, productData.product_id)">
                            <div class="img-col">
                                <img ms-src="{{ hotel.cover_image_url }}?imageView/1/w/150/h/100">
                            </div>
                            <div class="info-col">
                                <h4>
                                    <span class="cn-name">{{ hotel.name }}</span>
                                    <span class="comment-star">
                                         <i class="star-back icon-rating-star"></i>
                                         <i class="star-front icon-rating-star"
                                            ms-css-width="{{ hotel.hotel.star_level * 20 }}%"></i>
                                    </span>
                                    <div class="en-name">{{ hotel.en_name }}</div>
                                </h4>
                                <div class="base">
                                    <p class="desc">{{ hotel.summary }}</p>
                                </div>
                            </div>
                            <div class="more-col">
                                <span>了解更多</span>
                            </div>
                            <i class="icon-arrow-right"></i>
                        </div>
                        <div class="hotel-room-item clearfix" ms-repeat-room="hotel.hotel.room_types">
                            <div class="room-name">{{ room.name }}</div>
                            <div class="room-service-item icon-person">{{ room.capacity }}人</div>
                            <div class="room-service-item" ms-repeat-service="room.services"
                                 ms-class="{{ service.class_name }}">{{ service.name }}
                            </div>
                            <div class="room-price">市场价<span>&yen;<em>{{ room.show_prices.orig_price}}</em></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 套餐包含其他 -->
            <div class="package-item bonus contain-product" ms-repeat-pd="bundle.complimentary.products"
                 ms-click="bundleModel.showBundleModal(pd.product_id, pd.type, productData.product_id)"
                 data-repeat-rendered="renderCallback">
                <h3>
                    <em>{{ $index + 2 }}.</em>
                    <span class="title">{{ pd.name }}</span>
                </h3>
                <div class="product-info clearfix">
                    <div class="img-col">
                        <img ms-src="{{ pd.cover_image_url }}?imageView/1/w/120/h/80">
                    </div>
                    <div class="intro-col">
                        <div class="comment-row">
                            <span>玩家评分</span>
                            <span class="comment-star">
                                 <i class="star-back icon-rating-star"></i>
                                 <i class="star-front icon-rating-star" ms-css-width="{{ '80' }}%"></i>
                            </span>
                        </div>
                        <div class="go-more">{{ pd.summary }}</div>
                    </div>
                    <div class="price-col">
                        <span>市场价<em>{{ '&yen;' + pd.show_prices.orig_price }}</em></span>
                    </div>
                </div>
            </div>
            <!-- 套餐公式 -->
            <div class="formula">
                <ul>
                    <li class="orig-total"><span>市场总价</span><em>&yen;{{ basic_price.orig_price }}</em></li>
                    <li class="hi-total"><span>玩途套餐价</span><em>&yen;{{ basic_price.price }}</em></li>
                    <li class="save-total"><span>节省(每套)</span><em>&yen;{{ basic_price.orig_price - basic_price.price }}</em>
                    </li>
                </ul>
                <i>*玩途套餐价按基础价计算</i>
            </div>
        </div>
        <!-- tab-item 优惠可选 -->
        <div class="optional tab-item content-item" id="tab-optional">
            <div class="container">
                <div class="tab-title">
                    <h2>独享特惠</h2>
                    <p>您还可以优惠购买如下服务</p>
                </div>
                <div class="product-list">
                    <div class="product-item clearfix" ms-repeat-pd="bundle.optional.products"
                         ms-click="bundleModel.showBundleModal(pd.product_id, pd.type, productData.product_id)">
                        <div class="img-col">
                            <img ms-src="{{ pd.cover_image_url }}?imageView/1/w/120/h/80">
                        </div>
                        <div class="intro-col">
                            <h4>{{ pd.name }}</h4>

                            <div class="comment-row">
                                <span>玩家评分</span>
                                <span class="comment-star">
                                    <i class="star-back icon-rating-star"></i>
                                    <i class="star-front icon-rating-star" ms-css-width="{{ '80' }}%"></i>
                                </span>
                            </div>
                        </div>
                        <div class="price-col">
                            <span class="orig-price">市场价<em>&nbsp;{{ '&yen;' + pd.show_prices.orig_price }}&nbsp;</em></span>
                            <span class="hi-price">玩途价<em>{{ '&yen;' + pd.show_prices.price }}</em></span><br>
                            <span class="package-price">独享特惠
                                 <span class="dis-more">再减</span>
                                 <em><span ms-if="pd.bundle_info.discount_type == 'F'">&yen;</span>{{ pd.bundle_info.discount_amount }}</em>
                                 <span ms-if="pd.bundle_info.discount_type == 'P'">%</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 购买须知 旧 -->
        <div class="buy-notice tab-item content-item" ms-controller="productNoticeCtrl" id="tab_please_read" ms-if="!data.please_read">
            <div class="container">
                <div class="tab-title">
                    <h2>服务包含/购买须知</h2>
                </div>
                <div class="notice-content">
                    <div class="product-rule">
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
                    <div class="service-include" ms-repeat-el="service_include" ms-class="no-border:$first">
                        <div class="left-column">{{el.key|html}}</div>
                        <div class="content-list plain-list">{{el.val|html}}</div>
                    </div>
                    <div class="how-it-works" ms-repeat="how_it_works">
                        <div class="left-column">{{$key|html}}</div>
                        <div class="content-list plain-list">{{$val|html}}</div>
                    </div>
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
                                <h3>{{pl.seq}}. {{pl.name}}</h3>

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
        </div>
        <!-- 购买须知 新 -->
        <?php include(dirname(__FILE__) . '/modules/product_notice/product_notice.php'); ?>
    </div>
</div>


<div class="buy-dialog-mask">
    <div class="buy-dialog">
        <a class="close-btn" role="button" ms-click="closeBuyDialog()">×</a>

        <div class="buy-title">
            <h1>{{basic_info.name}}</h1>

            <div class="price-ctn" ms-if="selected_hotel.total_price">&yen;<em>{{selected_hotel.total_price}}</em></div>
        </div>
        <div class="left-column">
            <img ms-src="{{selected_hotel.hotel_item.cover_image_url}}?imageView/1/w/463/h/320"
                 alt="" />

            <div class="hotel-basic">
                <div class="product-room each-line">
                    <span class="title">房型：</span>
                    <span class="hotel-service room-type">{{selected_hotel.hotel_item.room_type.name}}</span>
                    <span>{{selected_hotel.hotel_item.room_type.area}}平方米</span>
                </div>
                <div class="product-facility each-line">
                    <span class="title left">服务设施：</span>
                        <span class="right">
                            <span class="hotel-service"
                                  ms-repeat-facility="selected_hotel.hotel_item.room_type.services">
                                <i class="i" ms-class="{{ facility.class_name }}"></i>{{facility.name}}
                            </span>
                        </span>
                </div>
                <div class="product-capacity each-line">
                    <span class="title">可住人数：</span>
                    <span class="capacity">{{selected_hotel.hotel_item.room_type.capacity}}人</span>
                    <!-- <span ms-if="selected_hotel.hotel_item.hotel.hotel_base.description">({{selected_hotel.hotel_item.hotel.hotel_base.description}})</span>-->
                </div>
                <div class="product-policy each-line" ms-if="selected_hotel.hotel_item.room_type.bed_policy_md != ''">
                    <div class="title">加床政策:</div>
                    <div class="md" ms-html="selected_hotel.hotel_item.room_type.bed_policy_md"></div>
                </div>
                <div class="product-policy each-line"
                     ms-if="selected_hotel.hotel_item.room_type.policies.length > 0 && selected_hotel.hotel_item.room_type.bed_policy_md == ''">
                    <div class="title">加床政策:</div>
                    <table class="policy-table">
                        <tr ms-repeat-policy="selected_hotel.hotel_item.room_type.policies">
                            <td width="35%" ms-if="policy.age_range != ''">{{ policy.age_range }}岁</td>
                            <td ms-attr-colspan="policy.age_range != ''?2:3">{{ policy.policy }}</td>
                        </tr>
                    </table>
                </div>
                <div class="product-breakfast each-line" ms-if="selected_hotel.hotel_item.room_type.breakfast_md != ''">
                    <div class="title">早餐费用:</div>
                    <div class="md" ms-html="selected_hotel.hotel_item.room_type.breakfast_md"></div>
                </div>
            </div>
        </div>
        <div class="right-content">
            <div class="field-ctn">
                <span class="label">请选择您的出行日期</span>

                <div id="tour_date" class="tour-date" ms-click="toggleCalendar()">
                    <i class="i-calendar"></i>
                    <div id='date_text' class="date-text">{{selected_hotel.date_str}}</div>

                    <i class="i-arrow-down"></i>
                </div>
            </div>
            <div class="field-ctn plan-selection">
                <!--<label for="">选择想要入住的酒店：</label>-->
                <ul ms-attr-a="hotel_list.length">
                    <li ms-repeat-htl="hotel_list" ms-class-1="active:htl.active" ms-class-2="disable:htl.disable">
                            <span ms-attr-index="$index" ms-click="changeSelectedIndex($index)">
                                <i ms-attr-index="$index" class="radio"><i ms-attr-index="$index"
                                                                           class="radio-inner"></i></i>{{htl.cn_name}}

                            </span>
                        <em ms-if="htl.unit_price">&yen;{{htl.unit_price}}</em>
                    </li>
                </ul>
            </div>


            <div class="field-ctn">
                <span class="label">选择您的房间数</span>
                    <div class="select-holder">
                        <div class="select"  ms-click="selector.show('plan',$event)">
                            {{selected_hotel.plan}}
                        </div>
                        <div class="select-box" ms-visible="selector.plan">
                            <div class="option" ms-repeat-plan="selected_hotel.plans_range" ms-click="selector.select('plan',selected_hotel,plan,selectPlan,$event,null)">{{plan}}间</div>
                        </div>
                        <i class="i-arrow-down ie-hide" ms-click="selector.show('plan',$event)"></i>
                    </div>
            </div>
            <div class="field-ctn ">
                <span class="label">出行成人数</span>
                <div class="select-holder" id="adult_num">
                    <div class="select"  ms-click="selector.show('adult_num',$event)" >
                        {{adult_num}}
                    </div>
                    <div class="select-box" ms-visible="selector.adult_num">
                        <div class="option" ms-repeat-op="max_num" ms-click="selector.select('adult_num',null,op,selectQuantity,$event,null)">{{op}}人</div>
                    </div>
                    <i class="i-arrow-down ie-hide" ms-click="selector.show('adult_num',$event)"></i>
                </div>

            </div>
            <div class="field-ctn ">
                <span class="label">出行儿童数 ({{minAge}}-{{maxAge}}岁)</span>

                <div class="select-holder" id="child_num">
                    <div class="select"  ms-click="selector.show('child_num',$event)" >
                        {{child_num}}
                    </div>
                    <div class="select-box" ms-visible="selector.child_num">
                        <div class="option" ms-repeat-op="max_num" ms-click="selector.select('child_num',null,op,selectQuantity,$event,null)">{{op}}人</div>
                    </div>
                    <i class="i-arrow-down ie-hide" ms-click="selector.show('child_num',$event)"></i>
                </div>
                <div class="field-error" ms-if="quantity_error">{{quantity_error}}</div>
            </div>
            <div class="field-ctn" ms-visible="child_num>0&&!quantity_error">
                <span class="label">入住儿童年龄</span>
                <div class="child-age-wrap">
                    <div class="select-holder shortly" ms-repeat-child="child_list">
                        <div class="select"  ms-click="selector.show(child,$event)" >
                            {{child.age}}
                        </div>
                        <div class="select-box shortly" ms-visible="child.show">
                            <div class="option" ms-repeat-op="age_list" ms-click="selector.select('age',child,op,checkComplete,$event,child)">{{op}}岁</div>
                        </div>
                        <i class="i-arrow-down ie-hide" ms-click="selector.show(child,$event)"></i>
                    </div>

                </div>


            </div>
            <div class="field-tips" ms-if="quantity_tips">{{quantity_tips}}</div>
            <button id="confirm_button" ms-disabled="!selected_hotel.available"
                    ms-class="btn-active:selected_hotel.available" class="confirm-btn" ms-click="goBuy()">
                {{selected_hotel.button_label}}
            </button>
        </div>
    </div>
</div>


<!--  绑定商品 详情弹窗  -->
<?php include(dirname(__FILE__) . '/../module/bundle/bundle.html'); ?>

</div>
<script type="text/javascript"
        src="http://maps.google.cn/maps/api/js?libraries=geometry&key=AIzaSyB062x7b2UUvRIMLRIHJ8rFaZXGSkca89c&sensor=false"></script>
<script type="text/javascript" src="themes/public/lib/GoogleMap/hi.Gmap.js"></script>
