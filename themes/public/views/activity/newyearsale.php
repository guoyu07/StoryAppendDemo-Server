<!--stylesheet-->
<link rel="stylesheet" href="themes/public/stylesheets/new-year-sale.css">

<!--template-->
<div class="new-year-sale main-content" ms-controller="newYearSale">

<!--flash sale-->
<div class="flash-sale">
    <div class="header-image"></div>
    <div class="notification">
        <div class="container">
            <div class="header-margin">
                <div class="header-block flash-left">
                    <div class="timer-title"><i></i>{{flash_sale_data.timer_label}}</div>
                    <div class="timer-zone">
                        <span class="hours">{{timerDisplay.hours}}</span>
                        <span>:</span>
                        <span class="minutes">{{timerDisplay.minutes}}</span>
                        <span>:</span>
                        <span class="seconds">{{timerDisplay.seconds}}</span>
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

    <div class="color-bg">
        <div class="container one-flash-zone">
            <!--Flash Sale Template-->
            <div ms-class-1="limited-product" ms-repeat-el="flash_sale_data.products">
                <a target="_blank" class="flash-image product-image" ms-href="{{el.link_url}}">
                    <img ms-src="{{el.cover_image_url}}?imageView/1/w/300/h/270">
                    <img class="off-discount"
                         src="themes/public/images/activities/newyearsale/off5.png?imageView/1/w/300/h/270">
                    <div class="price-zone">
                        <span>秒杀价</span>
                        <span class="RMB">￥</span>
                        <span class="new-price">{{ Math.floor(el.show_price.price / 2)}}</span>
                        <span>玩途价</span>
                        <span class="RMB">￥</span>
                        <span class="old-price">{{ el.show_price.price }}</span>
                    </div>
                    <div class="product-tag">
                        <img src="themes/public/images/activities/newyearsale/tag_bg.png">
                        <p>{{ el.city_name }}</p>
                    </div>
                </a>
                <div class="product-info">
                    <div style="height:62px;" class="product-name">{{el.name}}</div>
                    <div class="left-zone">
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
                    <a id="limited_btn" class="limited-btn" ms-href="{{el.link_url}}"
                       ms-class="disabled-btn:flash_sale_data.status==3 && el.stock_info.current_stock_num == 0">
                        {{el.button_label}}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="activity-rule">
    <div class="container">
        <img src="themes/public/images/activities/newyearsale/activity_rule.jpg">
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
var newYearSaleCtrl = avalon.define( "newYearSale", function( vm ) {

    vm.flash_sale_data = {};

    vm.timer = {
        hours   : 0,
        minutes : 0,
        seconds : 0
    };

    vm.timerDisplay = {
        hours   : 0,
        minutes : 0,
        seconds : 0
        //            hour_hundred : 0,
        //            hour_one: 0,
        //            hour_ten: 0,
        //            minute_one: 0,
        //            minute_ten: 0,
        //            second_one: 0,
        //            second_ten: 0
    };

    vm.moveZone = function( part ) {
        var element = "." + part;
        $( document.body ).animate( {scrollTop : $( element ).offset().top}, 500 );
    };
} );

var soldOutProducts = [];

var changeDisplayOrder = function() {
    var newYearSaleNode = $( ".friday-sale" );
    var flashSaleNode = newYearSaleNode.children( ".flash-sale" );
    newYearSaleNode.remove( ".flash-sale" );
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
    if( newYearSaleCtrl.flash_sale_data.status == 4 ) {
        newYearSaleCtrl.timer.hours = 0;
        newYearSaleCtrl.timer.minutes = 0;
        newYearSaleCtrl.timer.seconds = 0;
    } else
        setTimerZone( newYearSaleCtrl.flash_sale_data );

    //Set Timer Label
    if( newYearSaleCtrl.flash_sale_data.status == 2 )
        newYearSaleCtrl.flash_sale_data.timer_label = "距开始还有";
    else if( newYearSaleCtrl.flash_sale_data.status == 3 )
        newYearSaleCtrl.flash_sale_data.timer_label = "离结束仅剩";
    else
        newYearSaleCtrl.flash_sale_data.timer_label = "秒杀已结束 优惠继续抢";

    //Set Product Attributes
    for( var i = 0; i < newYearSaleCtrl.flash_sale_data.products.length; i++ ) {

        var product = newYearSaleCtrl.flash_sale_data.products[i];

        //Set Progress Bar
        newYearSaleCtrl.flash_sale_data.products[i].red_bar = product.stock_info.current_stock_num * 280 /
                                                             product.stock_info.all_stock_num;
        newYearSaleCtrl.flash_sale_data.products[i].white_bar = (product.stock_info.all_stock_num -
                                                                product.stock_info.current_stock_num) * 280 /
                                                               product.stock_info.all_stock_num;

        //Set Activity Button Label
        if( newYearSaleCtrl.flash_sale_data.status == 2 )
            newYearSaleCtrl.flash_sale_data.products[i].button_label = "准备开抢";
        else if( newYearSaleCtrl.flash_sale_data.status == 3 ) {
            if( product.stock_info.current_stock_num == 0 ) {
                newYearSaleCtrl.flash_sale_data.products[i].button_label = "已售罄";
                soldOutProducts.push( i );
            } else {
                newYearSaleCtrl.flash_sale_data.products[i].button_label = "立即秒杀";
            }
        } else {
            newYearSaleCtrl.flash_sale_data.products[i].button_label = "秒杀结束";
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
    var temp = "00" + newYearSaleCtrl.timer.hours;
    if( newYearSaleCtrl.timer.hours >= 100 )
        newYearSaleCtrl.timerDisplay.hours = temp.substring( temp.length - 3, temp.length ); else
        newYearSaleCtrl.timerDisplay.hours = temp.substring( temp.length - 2, temp.length );
    temp = "00" + newYearSaleCtrl.timer.minutes;
    newYearSaleCtrl.timerDisplay.minutes = temp.substring( temp.length - 2, temp.length );
    temp = "00" + newYearSaleCtrl.timer.seconds;
    newYearSaleCtrl.timerDisplay.seconds = temp.substring( temp.length - 2, temp.length );
    //        newYearSaleCtrl.timerDisplay.hour_one = newYearSaleCtrl.timer.hours % 10;
    //        newYearSaleCtrl.timerDisplay.hour_ten = Math.floor(newYearSaleCtrl.timer.hours % 100 / 10);
    //        newYearSaleCtrl.timerDisplay.hour_hundred = Math.floor(newYearSaleCtrl.timer.hours / 100);
    //        newYearSaleCtrl.timerDisplay.minute_one = newYearSaleCtrl.timer.minutes % 10;
    //        newYearSaleCtrl.timerDisplay.minute_ten = Math.floor(newYearSaleCtrl.timer.minutes / 10);
    //        newYearSaleCtrl.timerDisplay.second_one = newYearSaleCtrl.timer.seconds % 10;
    //        newYearSaleCtrl.timerDisplay.second_ten = Math.floor(newYearSaleCtrl.timer.seconds / 10);
};

var setTimerZone = function( sale_data ) {
    newYearSaleCtrl.timer.hours = Math.floor( sale_data.countdown / 3600 );
    newYearSaleCtrl.timer.minutes = Math.floor( sale_data.countdown % 3600 / 60 );
    newYearSaleCtrl.timer.seconds = sale_data.countdown % 60;
    if( newYearSaleCtrl.timer.hours == 0 && newYearSaleCtrl.timer.minutes == 0 && newYearSaleCtrl.timer.seconds == 0 )
        return; else
        setTimerClock();
};

var setTimerClock = function() {

    newYearSaleCtrl.timer.seconds--;

    if( newYearSaleCtrl.timer.seconds == -1 ) {
        newYearSaleCtrl.timer.minutes--;
        newYearSaleCtrl.timer.seconds = 59;
    }
    if( newYearSaleCtrl.timer.minutes == -1 ) {
        newYearSaleCtrl.timer.hours--;
        newYearSaleCtrl.timer.seconds = 59;
        newYearSaleCtrl.timer.minutes = 59;
    }
    if( newYearSaleCtrl.timer.hours == -1 ) {
        window.location.reload();
        return;
    }
    updateTimerDisplay();
    window.setTimeout( setTimerClock, 1000 );
};

var getAllData = function() {
    $.ajax( {
                url      : $request_urls.getFlashSaleData,
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

                        newYearSaleCtrl.flash_sale_data = res.data.flash_sale_data;
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