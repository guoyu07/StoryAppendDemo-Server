<!--stylesheet-->
<link rel="stylesheet" href="themes/public/stylesheets/friday-sale.css">
<!--360-->
<style type="text/css">
    /*reset*/
    html{-webkit-text-size-adjust:none}
    body,h1,h2,h3,h4,h5,h6,hr,p,blockquote,dl,dt,dd,ul,ol,li,pre,form,fieldset,legend,button,input,textarea,th,td,div{margin:0;padding:0}
    body,button,input,select,textarea{font:12px/1.5 simsun}h1,h2,h3,h4,h5,h6{font-size:100%}address,cite,dfn,em,var{font-style:normal}
    code,kbd,pre,samp{font-family:courier new,courier,monospace}ul,ol{list-style:none}a,a:hover{text-decoration:none}
    a:focus{outline:none;-moz-outline:none}sup{vertical-align:text-top}sub{vertical-align:text-bottom}
    legend{color:#000}fieldset,img,a{border:0 none;outline:0 none}input,textarea{outline:none}
    button,input,select,textarea{font-size:100%}table{border-collapse:collapse;border-spacing:0}
    abbr,article,aside,bb,datagrid,datalist,details,dialog,eventsource,figure,figcaption,footer,header,mark,menu,meter,nav,
    output,progress,section,time{display:block;height:auto}textarea{resize:none}
    .clearfix:after{clear:both;content:"\20";display:block;font-size:0;height:0;line-height:0;visibility:hidden}
    .clearfix{display:block;zoom:1;clear:none !important;}
    /*头部样式*/
    #hd .container{width:1000px;height:50px;margin:0 auto;position: relative;z-index: 1000}
    #hd {height:50px;background:#fff;}
    #hd .logo {height:50px;width:145px;float:left;}
    #hd .le-nav{float:left;margin-left:50px;_display:inline;}
    #hd .le-nav li{float:left;height:50px;border-right:1px solid #e5e5e5;position:relative;}
    #hd .le-nav li:hover .sub-nav{display:block;}
    #hd .le-nav li.last{border-right:none;}
    #hd .le-nav li a{float:left; display:block;padding-right:12px;line-height:50px;height:47px;font-size:14px;color:#666;font-family:Microsoft Yahei;overflow:hidden;}
    #hd .le-nav li a:hover,#hd .le-nav li a.hover{text-decoration:none;background:#f8f3f0;}
    #hd .le-nav li a:hover{color:#09c;}
    #hd .le-nav li i{margin:10px 8px 0 12px;float:left;height:30px;width:20px;background:url(http://p0.qhimg.com/t01dcaa87d742d04de9.png) no-repeat;}
    #hd .le-nav .home:hover{border-bottom:3px solid #DC1B50;}
    #hd .le-nav .sub-nav{display:none;}
    #hd .le-nav .hover{display:block;}

    #hd .le-nav .holiday:hover,#hd .le-nav .hover{border-bottom:3px solid #f8f3f0;}
    #hd .le-nav .sub-nav-holiday{border-bottom:3px solid #C6F;}

    #hd .le-nav .flight:hover{border-bottom:3px solid #f8f3f0;}
    #hd .le-nav .sub-nav-flight{border-bottom:3px solid #4BB4EB;}

    #hd .le-nav .hotel:hover{border-bottom:3px solid #f8f3f0;}
    #hd .le-nav .sub-nav-hotel{border-bottom:3px solid #EC7579;}

    #hd .le-nav .train:hover{border-bottom:3px solid #f8f3f0;}
    #hd .le-nav .sub-nav-train{border-bottom:3px solid #FFC000;}

    #hd .le-nav .tuan:hover{border-bottom:3px solid #4FBDB0;}
    #hd .le-nav .tripmap:hover{border-bottom:3px solid #FD604D;}
    #hd .le-nav .website:hover{border-bottom:3px solid #C8B897;}
    #hd .le-nav .home i{background-position:0 0;}
    #hd .le-nav .flight i{background-position:-30px 0;}
    #hd .le-nav .hotel i{background-position:-60px 0;}
    #hd .le-nav .train i{background-position:-120px 0;}
    #hd .le-nav .tuan i{background-position:-90px 0;}
    #hd .le-nav .holiday i{background-position:-150px 0;}
    #hd .le-nav .tripmap i{background-position:-180px 0;}
    #hd .le-nav .website i{background-position:-210px 0;}
    #hd .sub-nav{position:absolute;top:50px;left:0;background:#f8f3f0;width:100%;padding:0;text-align:center;}
    #hd .sub-nav dt{width:100%;text-align:center;}
    #hd .sub-nav dt a{float:none;padding:0;}
    /*底部样式*/
    #ft {background: #fff;height: 87px;text-align: center;color: #999;}
    #ft .channel{width:1000px;margin:10px auto;}
    #ft .channel h3{height:30px;margin:10px 0;background:url(http://p8.qhimg.com/t01b3478d1015e652f5.png) center repeat-x;width:100%;}
    #ft .channel h3 span{display:inline-block;padding:0 20px;background:#fff;color:#333;font-size:18px;font-family:Microsoft Yahei;font-weight:500;}
    #ft .channel p{height:24px;line-height:24px;overflow:hidden;}
    #ft .channel p a{float:left;background:url(http://p0.qhimg.com/t01cab8bc124bb43a36.png) no-repeat;margin-left:54px;padding-left:30px;color:#069;line-height:24px;_display:inline;}
    #ft .channel p a:hover{color:#f60;text-decoration:none;}
    #ft .channel p .flight{background-position:0 0;}
    #ft .channel p .inter-flight{background-position:0 -24px;}
    #ft .channel p .hotel{background-position:0 -48px;}
    #ft .channel p .inter-hotel{background-position:0 -72px;}
    #ft .channel p .holiday{background-position:0 -96px;}
    #ft .channel p .tickets{background-position:0 -120px;}
    #ft .channel p .tuan{background-position:0 -144px;}
    #ft .channel p .flight:hover{background-position:-100px 0;}
    #ft .channel p .inter-flight:hover{background-position:-100px -24px;}
    #ft .channel p .hotel:hover{background-position:-100px -48px;}
    #ft .channel p .inter-hotel:hover{background-position:-100px -72px;}
    #ft .channel p .holiday:hover{background-position:-100px -96px;}
    #ft .channel p .tickets:hover{background-position:-100px -120px;}
    #ft .channel p .tuan:hover{background-position:-100px -144px;}
    #ft p {height: 20px;line-height: 20px;}
    #ft .link {padding: 19px 0 4px;border-top: 6px solid #de3c12;}
    #ft a {color: #999;}
    #ft a:hover{text-decoration:underline;}
    #ft .link a {margin: 0 5px;}
</style>
<div id="hd">
    <div class="container">
        <div class="logo"><a href="http://go.360.cn/" title="360旅游" target="_blank"><img src="http://p0.qhimg.com/t01b831aecc51b5ca05.png" alt="360旅游" /></a></div>
        <ul class="le-nav clearfix">
            <li><a href="http://go.360.cn/" target="_blank" class="home"><i></i>首页</a></li>
            <li>
                <a href="http://go.360.cn/holiday" target="_blank" class="holiday"><i></i>旅游度假</a>
                <dl class="sub-nav sub-nav-holiday">
                    <dt><a href="http://go.360.cn/holiday/visa" target="_blank" class="visa">签证</a></dt>
                </dl>
            </li>
            <li>
                <a href="http://go.360.cn/flight" target="_blank" class="flight"><i></i>机票</a>

            </li>
            <li>
                <a href="http://go.360.cn/hotel" target="_blank" class="hotel"><i></i>酒店</a>
                <dl class="sub-nav sub-nav-hotel">
                    <dt><a href="http://www.booking.com/?aid=364336" target="_blank" class="visa">海外酒店</a></dt>
                    <dt><a href="http://go.360.cn/hotel/apartment" target="_blank" class="visa">度假公寓</a></dt>
                </dl>
            </li>
            <li>
                <a href="http://go.360.cn/train" target="_blank" class="train"><i></i>火车票</a>
                <dl class="sub-nav sub-nav-train">
                    <dt><a href="http://go.360.cn/bus" target="_blank" class="visa">长途汽车</a></dt>
                </dl>
            </li>
            <li><a href="http://go.360.cn/top10" target="_blank" class="tuan"><i></i>限时特价</a></li>
            <li><a href="http://go.360.cn/tripmap" target="_blank" class="tripmap"><i></i>景点大全</a></li>
            <li class="last"><a href="http://go.360.cn/website" target="_blank" class="website"><i></i>旅游网址</a></li>
        </ul>
    </div>
</div>

<!--template-->
<div class="friday-sale main-content" ms-controller="fridaySale">
    <!--side bar-->
    <div class="activity-nav">
        <img style="margin-top: 20px;cursor:pointer;" ms-click="moveZone('flash-sale')" src="themes/public/images/activities/fridaysale/flash-zone.png">
        <img src="themes/public/images/activities/fridaysale/discount-zone.png">
        <div class="nav-location">
            <div class="each-nav" ms-click="moveZone(nav.location.code)" ms-repeat-nav="discount_data.groups">{{nav.location.cn_name}}</div>
        </div>
    </div>

    <!--top image-->
    <div class="activity-top" ms-css-background-image="url({{header_image}})">
    </div>
    <!--flash sale-->
    <div class="flash-sale">
        <div class="container">
            <div class="flash-title">
                <img class="title-img" src="themes/public/images/activities/fridaysale/flash-sale.png">
                <div class="timer-zone">
                    <span class="timer-title">{{flash_sale_data.timer_label}}</span>
                    <span class="timer-bg hour-hundred" ms-if="timerDisplay.hour_hundred != 0">{{timerDisplay.hour_hundred}}</span>
                    <span class="timer-bg hour-ten">{{timerDisplay.hour_ten}}</span>
                    <span class="timer-bg hour-one">{{timerDisplay.hour_one}}</span>
                    <span>时</span>
                    <span class="timer-bg minute-ten">{{timerDisplay.minute_ten}}</span>
                    <span class="timer-bg minute-one">{{timerDisplay.minute_one}}</span>
                    <span>分</span>
                    <span class="timer-bg second-ten">{{timerDisplay.second_ten}}</span>
                    <span class="timer-bg second-one">{{timerDisplay.second_one}}</span>
                    <span>秒</span>
                </div>
            </div>
            <div class="limited-product" ms-repeat-el="flash_sale_data.products">
                <a class="flash-image product-image" ms-href="{{el.link_url}}">
                    <img ms-src="{{el.cover_image_url}}?imageView/1/w/310/h/310">
                    <img class="off-discount" src="themes/public/images/activities/fridaysale/off5.png?imageView/1/w/310/h/310">
                    <div class="price-zone">
                        <span>秒杀价</span>
                        <span class="RMB">￥</span>
                        <span class="new-price">{{el.show_price.price}}</span>
                        <span>玩途价</span>
                        <span class="RMB">￥</span>
                        <span class="old-price">{{el.show_price.orig_price}}</span>
                    </div>
                </a>
                <div class="product-info">
                    <div style="height:52px;" class="product-name">{{el.name}}</div>
                    <div class="left-zone">
                        <span class="total">限量{{el.stock_info.all_stock_num}}张</span>
                        <span class="left">还剩{{el.stock_info.current_stock_num}}张</span>
                        <div class="progress-bar">
                            <div class="white-bar" ms-class="all-radius:el.white_bar==280" ms-attr-style="width:{{el.white_bar}}px"></div>
                            <div class="red-bar" ms-class="all-radius:el.red_bar==280" ms-attr-style="width:{{el.red_bar}}px"></div>
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

    <!--discount sale-->
    <div class="discount-sale">
        <div class="container">
            <div class="one-city" ms-repeat-city="discount_data.groups" ms-class="{{city.location.code}}">
                <div class="title">
                    <a ms-href="{{city.location.link_url}}"><img ms-src="{{city.location.pc_nav_image_url}}"></a>
                </div>
                <div class="discount-product" ms-repeat-el="city.products">
                    <a class="product-image" ms-href="{{el.link_url}}">
                        <img ms-src="{{el.cover_image_url}}?imageView/1/w/310/h/310">
                        <div class="price-zone">
                            <span>抢购价</span>
                            <span class="RMB">￥</span>
                            <span class="new-price">{{el.show_price.price}}</span>
                            <span class="RMB">￥</span>
                            <span class="old-price">{{el.show_price.orig_price}}</span>
                        </div>
                    </a>
                    <div class="product-info">
                        <div class="product-benefit" ms-if="el.benefit"><i class="i icon-tag2"></i>{{el.benefit}}</div>
                        <div class="product-name">{{el.name}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="activity-rule">
        <div class="container">
            <img src="themes/public/images/activities/fridaysale/activity-desc.png">
        </div>
    </div>
</div>

<div id="ft">
    <div class="channel">
        <h3><span>热门搜索</span></h3>
        <p>
            <a href="http://go.360.cn/flight" class="flight" target="_blank">特价机票</a>
            <a href="http://go.360.cn/flight#inter" class="inter-flight" target="_blank">国际机票</a>
            <a href="http://go.360.cn/hotel" class="hotel" target="_blank">超值酒店</a>
            <a href="http://www.booking.com/?aid=364336" class="inter-hotel" target="_blank">海外酒店</a>
            <a href="http://go.360.cn/holiday" class="holiday" target="_blank">旅游度假</a>
            <a href="http://go.360.cn/ticket/search" class="tickets" target="_blank">景点门票</a>
            <a href="http://go.360.cn/top10" class="tuan" target="_blank">限时特价</a>
        </p>
    </div>
    <div class="activity">
        <iframe src="http://go.360.cn/zt/activity.html" frameborder="0" width="1000" height="220" scrolling="no"></iframe>
    </div>
    <div class="link">
        <a href="http://feedback.hao.360.cn/" target="_blank">意见反馈</a>
        |
        <a href="http://e.weibo.com/360lvyou" target="_blank">官方微博</a>
        |
        <a href="javascript:" onclick="try{ window.external.AddFavorite('http://go.360.cn/','360旅游'); return false;} catch(e){ (window.sidebar)?window.sidebar.addPanel('360旅游','http://go.360.cn/',''):alert('请使用按键 Ctrl+d，收藏360旅游'); }finally{return false;}">加入收藏</a>
        |
        <a href="http://hao.360.cn/about.html" target="_blank">关于我们</a>
        |
        <a href="http://www.360.cn/about/contactus.html" target="_blank">联系方式</a>
    </div>
    <div class="copyright">
        Copyright © 360网址导航. All Rights Reserved.
        <a href="http://www.miibeian.gov.cn/" target="_blank">京ICP证080047号</a>
    </div>
</div>

<!--script-->
<script type="text/javascript">

    //  main controller
    var fridaySaleCtrl = avalon.define("fridaySale", function (vm) {

        vm.header_image = "";
        vm.flash_sale_data = {};
        vm.discount_data = {};
        vm.timer = {
            hours: 0,
            minutes: 0,
            seconds: 0
        };

        vm.timerDisplay = {
            hour_one: 0,
            hour_ten: 0,
            hour_hundred : 0,
            minute_one: 0,
            minute_ten: 0,
            second_one: 0,
            second_ten: 0
        };

        vm.moveZone = function(part) {
            var element = "." + part;
            $(document.body).animate({scrollTop: $(element).offset().top},500);
        };
    });

    var soldOutProducts = [];

    var initFlashData = function () {
        //Set Timer Clock
        setTimerZone();

        //set Header Image
        if(fridaySaleCtrl.flash_sale_data.status == 4)
            fridaySaleCtrl.header_image = "themes/public/images/activities/fridaysale/activity-header-discount.png";
        else
            fridaySaleCtrl.header_image = "themes/public/images/activities/fridaysale/activity-header-flash.png";

        //Set Timer Label
        if (fridaySaleCtrl.flash_sale_data.status == 2)
            fridaySaleCtrl.flash_sale_data.timer_label = "距开始还有";
        else if (fridaySaleCtrl.flash_sale_data.status == 3)
            fridaySaleCtrl.flash_sale_data.timer_label = "离结束仅剩";
        else
            fridaySaleCtrl.flash_sale_data.timer_label = "秒杀已结束 优惠继续抢";

        //Set Product Attributes
        for (var i = 0; i < fridaySaleCtrl.flash_sale_data.products.length; i++) {

            var product = fridaySaleCtrl.flash_sale_data.products[i];

            //Set Progress Bar
            fridaySaleCtrl.flash_sale_data.products[i].white_bar = (product.stock_info.all_stock_num - product.stock_info.current_stock_num) * 280 / product.stock_info.all_stock_num;
            fridaySaleCtrl.flash_sale_data.products[i].red_bar = product.stock_info.current_stock_num * 280 / product.stock_info.all_stock_num;

            //Set Activity Button Label
            if (fridaySaleCtrl.flash_sale_data.status == 2)
                fridaySaleCtrl.flash_sale_data.products[i].button_label = "准备开抢";
            else if (fridaySaleCtrl.flash_sale_data.status == 3) {
                if (product.stock_info.current_stock_num == 0) {
                    fridaySaleCtrl.flash_sale_data.products[i].button_label = "已售罄";
                    soldOutProducts.push(i);
                }
                else {
                    fridaySaleCtrl.flash_sale_data.products[i].button_label = "立即秒杀";
                }
            }
            else {
                fridaySaleCtrl.flash_sale_data.products[i].button_label = "活动结束";
                soldOutProducts.push(i);
            }
        }

        window.setTimeout(function(){
            setButtonLabel();
        },100);

    };

    var setButtonLabel = function() {
        for(var i = 0 ; i < soldOutProducts.length;i++) {
            $(".limited-btn").eq(soldOutProducts[i]).removeAttr("href");
            $(".flash-image" ).eq(soldOutProducts[i]).removeAttr("href");
        }
    };

    var updateTimerDisplay = function () {
        fridaySaleCtrl.timerDisplay.hour_one = fridaySaleCtrl.timer.hours % 10;
        fridaySaleCtrl.timerDisplay.hour_ten = Math.floor(fridaySaleCtrl.timer.hours % 100 / 10);
        fridaySaleCtrl.timerDisplay.hour_hundred = Math.floor(fridaySaleCtrl.timer.hours / 100);
        fridaySaleCtrl.timerDisplay.minute_one = fridaySaleCtrl.timer.minutes % 10;
        fridaySaleCtrl.timerDisplay.minute_ten = Math.floor(fridaySaleCtrl.timer.minutes / 10);
        fridaySaleCtrl.timerDisplay.second_one = fridaySaleCtrl.timer.seconds % 10;
        fridaySaleCtrl.timerDisplay.second_ten = Math.floor(fridaySaleCtrl.timer.seconds / 10);
    };

    var setTimerZone = function () {
        fridaySaleCtrl.timer.hours = Math.floor(fridaySaleCtrl.flash_sale_data.countdown / 3600);
        fridaySaleCtrl.timer.minutes = Math.floor(fridaySaleCtrl.flash_sale_data.countdown % 3600 / 60);
        fridaySaleCtrl.timer.seconds = fridaySaleCtrl.flash_sale_data.countdown % 60;
        if(fridaySaleCtrl.timer.hours == 0 && fridaySaleCtrl.timer.minutes == 0 && fridaySaleCtrl.timer.seconds == 0)
            return;
        else
            setTimerClock();
    };

    var setTimerClock = function () {

        fridaySaleCtrl.timer.seconds--;

        if (fridaySaleCtrl.timer.seconds == -1) {
            fridaySaleCtrl.timer.minutes--;
            fridaySaleCtrl.timer.seconds = 59;
        }
        if (fridaySaleCtrl.timer.minutes == -1) {
            fridaySaleCtrl.timer.hours--;
            fridaySaleCtrl.timer.seconds = 59;
            fridaySaleCtrl.timer.minutes = 59;
        }
        if (fridaySaleCtrl.timer.hours == -1) {
            window.location.reload();
            return;
        }
        updateTimerDisplay();
        window.setTimeout(setTimerClock, 1000);
    };

    var getAllData = function () {
        $.ajax({
                   url: "activity/fridaysaledata",
                   dataType: "json",
                   success: function (res) {
                       if (res.code == 200) {
                           fridaySaleCtrl.flash_sale_data = res.data.flash_sale_data;
                           fridaySaleCtrl.discount_data = res.data.friday_sale_data;
                           initFlashData();

                           $(".loading-mask").css("display", "none");
                           $("header").css("display","none");
                           $("footer").css("display","none");
                       }
                       else
                           alert(res.msg);
                   }
               });
    };

    getAllData();
</script>
<script type="text/javascript" src="http://s1.qhimg.com/!12e1a56b/monitor_go_zt.js"></script>