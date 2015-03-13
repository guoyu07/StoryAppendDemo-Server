<!--
/*
 * Created by PhpStorm.
 * User: JasonLee
 * Date: 15-3-4
 * Time: 上午10:16
 */
 -->
<!--stylesheet-->
<link rel="stylesheet" href="themes/public/stylesheets/friday-sale.css">

<style>
    .header-image {
        background-image: url(themes/public/images/activities/fridaysale/detail_header.jpg) !important;
    }
    .header-block .title{
        color: #525252;
        font-size: 18px;
        margin-top: 5px;;
    }
    .header-block .phone-number {
        color: #ff6600;
        font-size: 36px;
    }
    .main-sale {
        background-color: #00b7ee;
    }
    .sub-sale {

    }
    .discount-mask {
        background-image: url() !important;
        background-color: rgba(0,0,0,0.7) !important;
        color: white;
        font-size: 24px;
        text-align: center;
        padding: 100px 0px;
    }
    .discount-mask span {
        font-size: 38px;
    }
    .price-zone {
        background-color: rgba(0,183,255,0.8) !important;
    }
    .limited-btn {
        background-color: #00b7ee !important;
    }
</style>

<!--template-->
<div class="friday-sale main-content" ms-controller="fridaySaleDetail">

<!--flash sale-->
<div class="flash-sale">
    <div class="header-image">
        <div class="temp-link">
            <div class="container" style="text-align: right">
            </div>
        </div>
    </div>
    <div class="notification">
        <div class="container">
            <div class="header-margin">
                <div class="header-block flash-left">
                    <div class="title">住宿一晚 预定以下酒店 请致电</div>
                    <div class="phone-number">
                        400-010-1900
                    </div>
                </div>
                <div class="header-block flash-right">
                    <img src="themes/public/images/activities/newyearsale/qr_link_url.jpg">
                </div>
                <div class="header-block header-desc">
                    <p>1.现在扫码关注<sup>&lceil;</sup>玩途<sub>&rfloor;</sub>微信</p>
                    <p>2.回复<sup>&lceil;</sup>125<sub>&rfloor;</sub>获得5折入场券</p>
                    <p>3.凭券参加周5下午5点，5折秒杀</p>
                </div>
            </div>
        </div>
    </div>
</div>


<!--discount sale-->
<div class="discount-sale" ms-repeat-block="blocks_data" ms-class-1="main-sale:$first" ms-class-2="sub-sale:$index!=0">
    <div class="container">
        <div class="one-city">
            <div class="title">
                <img ms-src="{{block.nav_image}}">
            </div>
            <div class="discount-product" ms-repeat-el="block.products">
                <a target="_blank" class="product-image" ms-href="{{el.link_url}}">
                    <img ms-src="{{el.cover_image_url}}?imageView/1/w/300/h/270">
                    <div class="price-zone">
                        <span>抢购价</span>
                        <span class="RMB">￥</span>
                        <span class="new-price">{{el.show_price.price}}</span>
                        <span class="RMB">￥</span>
                        <span class="old-price">{{el.show_price.orig_price}}</span>
                    </div>
                    <div class="discount-mask">平均￥<span>{{ el.mean_price }}</span> /晚</div>
                </a>
                <div class="product-info">
                    <div class="product-line">
                        <div class="product-benefit" ms-if="el.benefit"><i class="i icon-tag2"></i>{{el.benefit}}
                        </div>
                        <div class="product-city" ms-if="el.city_name"><i class="i icon-location"></i>{{el.city_name}}
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="product-name">{{el.name}}</div>
                    <a id="limited_btn" class="limited-btn" target="_blank" ms-href="{{el.link_url}}">立即抢购</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="activity-rule">
    <div class="container">
        <img src="themes/public/images/activities/fridaysale/detail_rule.jpg">
    </div>
</div>

<!--script-->
<script type="text/javascript">


//  main controller
var fridaySaleDetailCtrl = avalon.define( "fridaySaleDetail", function( vm ) {

    vm.blocks_data = [];

    vm.moveZone = function( part ) {
        var element = "." + part;
        $( document.body ).animate( {scrollTop : $( element ).offset().top}, 500 );
    };
} );

var productPriceMap = [
    { product_id : 3535, mean_price : 217 },
    { product_id : 3192, mean_price : 175 },
    { product_id : 3511, mean_price : 323 },
    { product_id : 3658, mean_price : 246 },
    { product_id : 3704, mean_price : 460 },
    { product_id : 3507, mean_price : 453 },
    { product_id : 3534, mean_price : 1033 },
    { product_id : 3826, mean_price : 526 },
    { product_id : 3853, mean_price : 559 },
    { product_id : 3532, mean_price : 4329 },
    { product_id : 3512, mean_price : 993 },
    { product_id : 3513, mean_price : 960 }
];

var initData = function() {

    for(var k = 0;k < fridaySaleDetailCtrl.blocks_data.length;k++) {
        fridaySaleDetailCtrl.blocks_data[k].nav_image = 'themes/public/images/activities/fridaysale/detail_nav_' + (k + 1) + '.png';
        for(var i = 0;i < fridaySaleDetailCtrl.blocks_data[k].products.length;i++) {
            for(var j = 0;j < productPriceMap.length;j++) {
                if(fridaySaleDetailCtrl.blocks_data[k].products[i].product_id == productPriceMap[j].product_id) {
                    fridaySaleDetailCtrl.blocks_data[k].products[i].mean_price = productPriceMap[j].mean_price;
                    break;
                }
            }
        }
    }

    //Set Fixed Nav
    window.onscroll = function() {
        var st = document.body.scrollTop || document.documentElement.scrollTop;

        if( st > $( ".flash-sale" ).offset().top - 1 ) {
            $( "#nav_content" ).addClass( "show-nav" );
        } else {
            $( "#nav_content" ).removeClass( "show-nav" );
        }
        if( st > 100 && st < (document.body.scrollHeight - window.innerHeight - 100) ) {
            $( '.bottom-fixed-bar' ).css( 'height', '186px' );
        } else {
            $( '.bottom-fixed-bar' ).css( 'height', '0' );
        }
    }
};
$(function(){
    $.ajax( {
                url      : $request_urls.getFridaySaleDetailData,
                dataType : "json",
                success  : function( res ) {
                    if( res.code == 200 ) {

                        //for IE8 , data must be initialized for the first time
                        for( var i = 0; i < res.data.groups.length; i++ ) {
                            res.data.groups[i].nav_image = '';
                            for( var j = 0;j < res.data.groups[i].products.length;j++ ) {
                                res.data.groups[i].products[j].mean_price = 0;
                            }
                        }

                        for( var i = 0; i < res.data.groups.length; i++ ) {
                            fridaySaleDetailCtrl.blocks_data.push(res.data.groups[i]);
                        }

                        initData();
                        $( ".loading-mask" ).css( "display", "none" );
                    } else
                        alert( res.msg );
                }
    })
});

</script>
