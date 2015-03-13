<div class="page-hotel-plus" ms-controller="hotelPlusCtrl">

    <?php include(dirname(__FILE__) . '/modules/city_common_header/city_common_header.php'); ?>

    <div class="our-highlights"></div>
    <div class="hotel-tabs">
        <div class="hi-nav plus-nav clearfix" data-bind="nav-content">
            <div class="plus-text">全部区域:</div>
            <div class="nav-item" ms-repeat-item="data.hotel_plus.promotion_group" ms-class="active: $index == 0"
                 ms-attr-data-target="tab{{ $index }}"
                 ms-css-background-color="item.tab_bg_color"
                 ms-css-border-color="item.tab_decorator_color">{{ item.name }}
            </div>
        </div>
    </div>

    <div class="hi-tab-content hotel-plus-groups" id="nav-content">
        <div class="content-item hotel-group" ms-repeat-group="data.hotel_plus.promotion_group"
             data-repeat-rendered="renderCallback" ms-attr-id="tab{{ $index }}" ms-class="even : $index % 2 == 1">
            <div class="group-wrap wrap">
                <div class="group-intro">
                    <h2 class="group-name">{{ group.name }}</h2>
                    <p class="group-desc">{{ group.description }}</p>
                </div>
                <div class="hotel-item" ms-repeat-hotel="group.promotion_product">
                    <div class="hotel-main">
                        <a ms-href="hotel.link_url" target="_blank">
                            <div class="hotel-name">{{ hotel.description.name }}</div>
                        </a>
                        <div class="hotel-level">
                            <div class="level-text">星级:</div>
                            <div class="level-star icon-rating-star"
                                 ms-css-width="calculatePercent(hotel.hotel.star_level, 5) + 'px'"></div>
                        </div>
                        <p class="hotel-en-name">{{ hotel.description.en_name }}</p>
                        <div class="image-rooms-row clearfix">
                            <img class="hotel-image"
                                 ms-src="{{ hotel.cover_image.image_url }}?imageView2/5/w/180/h/120">
                            <table class="room-list">
                                <tr>
                                    <th>房型</th>
                                    <th>最多入住人数</th>
                                    <th>早餐</th>
                                    <th>WIFI</th>
                                </tr>
                                <tr class="room-item" ms-repeat-room="hotel.hotel.room_types">
                                    <td>{{ room.name }}</td>
                                    <td>{{ room.max_capacity }}人</td>
                                    <td>双早</td>
                                    <td>免费</td>
                                </tr>
                            </table>
                            <div class="hotel-price">
                                <div class="price-row"><span>&yen;<em>{{ hotel.show_prices.price }}</em></span>起</div>
                                <a ms-href="hotel.link_url" target="_blank" class="go-hotel-btn">立即预定</a>
                            </div>
                        </div>
                        <div class="hotel-area">
                            <i class="icon-location"></i><span>{{ hotel.hotel.location }}</span>
                        </div>
                    </div>
                    <div class="hotel-bottom">
                        <div class="gift-title clearfix">
                            <i class="icon-gift"></i>免费赠送
                        </div>
                        <div class="gift-content">
                            <div class="gift-item" ms-repeat-gift="hotel.complimentary">
                                <em>{{ $index + 1 }}. </em>{{ gift.descriptions.name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
