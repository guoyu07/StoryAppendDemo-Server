<!--stylesheet-->
<link rel="stylesheet" href="themes/public/stylesheets/friday-sale.css">
<style>
    .header-image {
        background-image: url(themes/public/images/activities/fridaysale/activity_header_sougou.jpg) !important;
    }
</style>

<!--template-->
<div class="friday-sale main-content" ms-controller="fridaySale">

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
                    <div class="timer-title"><i></i>{{flash_sale_data.timer_label}}</div>
                    <div class="timer-zone">
                        <span class="days" ms-if="timer.days > 0">{{timerDisplay.days}}</span>
                        <span class="mark" ms-if="timer.days > 0"> 天 </span>
                        <span class="hours">{{timerDisplay.hours}}</span>
                        <span class="mark"> 时 </span>
                        <span class="minutes">{{timerDisplay.minutes}}</span>
                        <span class="mark"> 分 </span>
                        <span class="seconds">{{timerDisplay.seconds}}</span>
                        <span class="mark"> 秒 </span>
                    </div>
                </div>
                <div class="header-block flash-right">
                    <img src="themes/public/images/activities/newyearsale/qr_link_url.jpg">
                </div>
                <div class="header-block header-desc">
                    <p>1.现在扫码关注【玩途】微信</p>
                    <p>2.回复【125】获得5折入场券</p>
                    <p>3.凭券参加周5下午5点，5折秒杀</p>
                </div>
            </div>
        </div>
    </div>
    <div class="color-bg">
        <div class="container one-flash-zone">
            <!--Flash Sale Template-->
            <div ms-class-1="limited-product" ms-repeat-el="flash_sale_data.products">
                <a target="_blank" class="flash-image product-image" ms-href="{{el.link_url}}">
                    <img ms-src="{{el.cover_image_url}}?imageView/1/w/300/h/270">
                    <img class="off-discount" src="themes/public/images/activities/fridaysale/off5.png?imageView/1/w/300/h/270">
                    <div class="price-zone">
                        <span>秒杀价</span>
                        <span class="RMB">￥</span>
                        <span class="new-price">{{ Math.floor(el.show_price.price / 2) }}</span>
                        <span>玩途价</span>
                        <span class="RMB">￥</span>
                        <span class="old-price">{{ el.show_price.price }}</span>
                    </div>
                </a>
                <div class="product-info">
                    <div style="height:62px;" class="product-name">{{el.name}}</div>
                    <div class="left-zone">
                        <!--                        <span class="total">限量{{el.stock_info.all_stock_num}}张</span>-->
                        <div class="left">限量{{el.stock_info.all_stock_num}}张</div>
                        <div class="progress-bar">
                            <!--red left, white sold-->
                            <div class="red-bar" ms-class="all-radius:el.red_bar==280"
                                 ms-css-width="{{el.red_bar}}">
                                <span class="red-appendix">{{el.stock_info.current_stock_num}}</span>
                            </div>
                            <div class="white-bar" ms-class="all-radius:el.white_bar==280"
                                 ms-css-width="{{el.white_bar}}"></div>
                        </div>
                    </div>
                    <a id="limited_btn" class="limited-btn" target="_blank" ms-href="{{el.link_url}}"
                       ms-class="disabled-btn:flash_sale_data.status==3 && el.stock_info.current_stock_num == 0">
                        {{el.button_label}}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!--discount sale-->
<div class="discount-sale">
    <div class="discount-title"><span>更多欧洲折扣单品低至6折</span>
        <div class="line-through"></div>
    </div>
    <div class="container">
        <div class="one-city" ms-repeat-city="discount_data.groups" ms-class="{{city.location.code}}">
            <div class="title">
                <img ms-src="{{city.location.pc_nav_image_url}}">
                <a target="_blank" ms-href="{{city.location.link_url}}" ms-if="city.location.code != 'HTL'">更多折扣商品 > </a>
                <a target="_blank" href="/activity/fridaysaledetail" ms-if="city.location.code == 'HTL'">更多折扣商品 > </a>
                <!--Hack for Thailand Hotel-->
            </div>
            <div class="discount-product" ms-repeat-el="city.products">
                <a target="_blank" class="product-image" ms-href="{{el.link_url}}">
                    <img ms-src="{{el.cover_image_url}}?imageView/1/w/300/h/270">
                    <div class="price-zone">
                        <span>抢购价</span>
                        <span class="RMB">￥</span>
                        <span class="new-price">{{el.show_price.price}}</span>
                        <span class="RMB">￥</span>
                        <span class="old-price">{{el.show_price.orig_price}}</span>
                    </div>
                    <div class="discount-mask"></div>
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
        <img src="themes/public/images/activities/fridaysale/activity_rule.jpg">
    </div>
</div>
<div class="bottom-fixed-bar">
    <div class="inner-box">
        <div class="left-msg">
            绝不错过下期精彩<br>订阅送20元优惠券
        </div>
        <div class="email-ctn">
            <em></em>
            <ol>
                <li>留下您的邮箱，获取更多优惠信息！</li>
                <li> 20元优惠券，除活动商品外全站抵扣现金！</li>
            </ol>
            <div class="email-holder">
                <input id="email" type="text" placeholder="邮箱地址" />
                <button id="submitEmail">提交</button>
            </div>
            <div class="lcd">订阅成功，以后优惠信息及优惠券会发送到您的邮箱里！</div>
            <a class="close-btn" href="javascript:;">x</a>
        </div>
    </div>
</div>
</div>

<!--script-->
<script type="text/javascript">


//  main controller
var fridaySaleCtrl = avalon.define( "fridaySale", function( vm ) {

    vm.discount_header_image = "";
    vm.flash_sale_data = {};
    vm.discount_data = {};

    vm.timer = {
        days    : 0,
        hours   : 0,
        minutes : 0,
        seconds : 0
    };

    vm.timerDisplay = {
        days    : 0,
        hours   : 0,
        minutes : 0,
        seconds : 0
    };

    vm.moveZone = function( part ) {
        var element = "." + part;
        $( document.body ).animate( {scrollTop : $( element ).offset().top}, 500 );
    };
} );

var soldOutProducts = [];

var changeDisplayOrder = function() {
    var fridaySaleNode = $( ".friday-sale" );
    var flashSaleNode = fridaySaleNode.children( ".flash-sale" );
    fridaySaleNode.remove( ".flash-sale" );
    $( ".discount-sale" ).after( flashSaleNode );
};

var initFlashData = function() {
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
    //Set Timer Clock
    if( fridaySaleCtrl.flash_sale_data.status == 4 )
        setTimerZone( fridaySaleCtrl.discount_data ); else
        setTimerZone( fridaySaleCtrl.flash_sale_data );

    //Set Timer Label
    if( fridaySaleCtrl.flash_sale_data.status == 2 )
        fridaySaleCtrl.flash_sale_data.timer_label = "距开始还有"; else if( fridaySaleCtrl.flash_sale_data.status == 3 )
        fridaySaleCtrl.flash_sale_data.timer_label = "离结束仅剩"; else
        fridaySaleCtrl.flash_sale_data.timer_label = "秒杀已结束 优惠继续抢";

    //Set Product Attributes
    for( var i = 0; i < fridaySaleCtrl.flash_sale_data.products.length; i++ ) {

        var product = fridaySaleCtrl.flash_sale_data.products[i];

        //Set Progress Bar
        fridaySaleCtrl.flash_sale_data.products[i].red_bar = product.stock_info.current_stock_num * 280 /
                                                             product.stock_info.all_stock_num;
        fridaySaleCtrl.flash_sale_data.products[i].white_bar = (product.stock_info.all_stock_num -
                                                                product.stock_info.current_stock_num) * 280 /
                                                               product.stock_info.all_stock_num;

        //Set Activity Button Label
        if( fridaySaleCtrl.flash_sale_data.status == 2 )
            fridaySaleCtrl.flash_sale_data.products[i].button_label = "准备开抢"; else if( fridaySaleCtrl.flash_sale_data.status ==
                                                                                       3 ) {
            if( product.stock_info.current_stock_num == 0 ) {
                fridaySaleCtrl.flash_sale_data.products[i].button_label = "已售罄";
                soldOutProducts.push( i );
            } else {
                fridaySaleCtrl.flash_sale_data.products[i].button_label = "立即秒杀";
            }
        } else {
            fridaySaleCtrl.flash_sale_data.products[i].button_label = "秒杀结束";
            soldOutProducts.push( i );
        }
    }

    window.setTimeout( function() {
        setButtonLabel();
    }, 100 );

};

var setButtonLabel = function() {
    for( var i = 0; i < soldOutProducts.length; i++ ) {
        $( ".limited-btn" ).eq( soldOutProducts[i] ).removeAttr( "href" );
        $( ".flash-image" ).eq( soldOutProducts[i] ).removeAttr( "href" );
    }
};

var updateTimerDisplay = function() {
    fridaySaleCtrl.timerDisplay.days = fridaySaleCtrl.timer.days;
    fridaySaleCtrl.timerDisplay.hours = fridaySaleCtrl.timer.hours;
    fridaySaleCtrl.timerDisplay.minutes = fridaySaleCtrl.timer.minutes;
    fridaySaleCtrl.timerDisplay.seconds = fridaySaleCtrl.timer.seconds;
//    var temp = "00" + fridaySaleCtrl.timer.hours;
//    if( fridaySaleCtrl.timer.hours >= 100 )
//        fridaySaleCtrl.timerDisplay.hours = temp.substring( temp.length - 3, temp.length ); else
//        fridaySaleCtrl.timerDisplay.hours = temp.substring( temp.length - 2, temp.length );
//    temp = "00" + fridaySaleCtrl.timer.minutes;
//    fridaySaleCtrl.timerDisplay.minutes = temp.substring( temp.length - 2, temp.length );
//    temp = "00" + fridaySaleCtrl.timer.seconds;
//    fridaySaleCtrl.timerDisplay.seconds = temp.substring( temp.length - 2, temp.length );
};

var setTimerZone = function( sale_data ) {
    fridaySaleCtrl.timer.days = Math.floor( sale_data.countdown / 86400 );
    fridaySaleCtrl.timer.hours = Math.floor( sale_data.countdown % 86400 / 3600 );
    fridaySaleCtrl.timer.minutes = Math.floor( sale_data.countdown % 3600 / 60 );
    fridaySaleCtrl.timer.seconds = sale_data.countdown % 60;
    if( fridaySaleCtrl.timer.seconds == 0 && fridaySaleCtrl.timer.minutes == 0 && fridaySaleCtrl.timer.hours == 0 && fridaySaleCtrl.timer.days == 0)
        return; else
        setTimerClock();
};

var setTimerClock = function() {

    fridaySaleCtrl.timer.seconds--;

    if( fridaySaleCtrl.timer.seconds == -1 ) {
        fridaySaleCtrl.timer.minutes--;
        fridaySaleCtrl.timer.seconds = 59;
    }
    if( fridaySaleCtrl.timer.minutes == -1 ) {
        fridaySaleCtrl.timer.hours--;
        fridaySaleCtrl.timer.seconds = 59;
        fridaySaleCtrl.timer.minutes = 59;
    }
    if( fridaySaleCtrl.timer.hours == -1 ) {
        fridaySaleCtrl.timer.days--;
        fridaySaleCtrl.timer.seconds = 59;
        fridaySaleCtrl.timer.minutes = 59;
        fridaySaleCtrl.timer.hours = 23;
    }
    if( fridaySaleCtrl.timer.days == -1 ) {
        window.location.reload();
        return;
    }
    updateTimerDisplay();
    window.setTimeout( setTimerClock, 1000 );
};

var getAllData = function() {
    $.ajax( {
                url      : "activity/fridaysaledata",
                dataType : "json",
                success  : function( res ) {
                    if( res.code == 200 ) {

                        //for IE8 , data must be initialized for the first time
                        res.data.flash_sale_data.timer_label = "";
                        for( var i = 0; i < res.data.flash_sale_data.products.length; i++ ) {
                            res.data.flash_sale_data.products[i].white_bar = 0;
                            res.data.flash_sale_data.products[i].red_bar = 0;
                            res.data.flash_sale_data.products[i].button_label = "";
                        }

                        fridaySaleCtrl.flash_sale_data = res.data.flash_sale_data;
                        fridaySaleCtrl.discount_data = res.data.friday_sale_data;
                        initFlashData();
                        $( function() {
                            $( ".loading-mask" ).css( "display", "none" );
                        } );

                    } else
                        alert( res.msg );
                }
            } );
};

getAllData();
$( function() {
    $( '.close-btn' ).click( function() {
        $( '.bottom-fixed-bar' ).hide();
    } )
    $( '#submitEmail' ).on( 'click', function() {
        var email = $( '#email' ).val();
        if( email ) {
            if( /^([\w\d]+[-\w\d.]*@[-\w\d.]+\.[a-zA-Z]{2,10}|1[358]\d{9})$/.test( email ) ) {
                $.ajax( {
                            url     : 'activity/subscribe/email/' + email,
                            success : function() {
                                $( '.email-holder' ).hide();
                                $( '.lcd' ).show();
                            }
                        } );
            } else {
                alert( '邮箱格式不对' );
            }
        }
    } );
} );

</script>
