<!--stylesheet-->
<link rel="stylesheet" href="themes/public/stylesheets/ccb-sale.css">

<!--template-->
<div class="ccb-sale main-content" ms-controller="ccbSale">

    <!--header image-->
    <div class="activity-top">
        <!--static tabs-->
        <div class="activity-tab">
            <img ms-src="themes/public/images/activities/ccbsale/{{tab_image}}.png">
        </div>
    </div>

    <div class="container">
        <div class="activity-content">
            <!--flash sale-->
            <div class="flash-sale">
                <div class="flash-cover">
                    <img ms-src="{{flash_sale_data.products[0].cover_image_url}}?imageView/1/w/680/h/555">
                    <div class="price-zone">
                        <span>秒杀价</span>
                        <span class="RMB">￥</span>
                        <span class="new-price">{{ flash_sale_data.products[0].show_price.price }}</span>
                        <span>市场价</span>
                        <span class="RMB">￥</span>
                        <span class="old-price">{{ flash_sale_data.products[0].show_price.orig_price }}</span>
                    </div>
                    <div class="summary">
                        <div class="summary-table">
                            <div class="summary-content">{{ flash_sale_data.products[0].description }}</div>
                        </div>
                    </div>
                </div>
                <div class="flash-info">
                    <div class="timer-zone" ms-if="activity_phase==2">
                        <img class="flash-slogan" src="themes/public/images/activities/ccbsale/flash_slogan.png">
                        <div class="flash-time">
                            <span><i class="i icon-calendar"></i></span>
                            <span>秒杀时间：</span>
                            <span>{{ flash_date }}</span>
                            <span>{{ flash_time }}</span>
                        </div>
                        <div style="font-size: 28px;margin-bottom: 10px;">{{timer_label}}</div>
                        <div class="timer">
                            <span class="hours">{{ timerDisplay.hours }}</span>
                            <span>:</span>
                            <span class="minutes">{{ timerDisplay.minutes }}</span>
                            <span>:</span>
                            <span class="seconds">{{ timerDisplay.seconds }}</span>
                        </div>
                    </div>
                    <div class="timer-zone" ms-if="activity_phase==1">
                        <img class="flash-slogan" src="themes/public/images/activities/ccbsale/flash_slogan.png">
                        <div class="flash-time">
                            <img class="loading" src="themes/public/images/activities/ccbsale/loading.png">
                            <div class="time">
                                <div>{{ flash_date }}</div>
                                <div>{{ flash_time }}</div>
                            </div>
                            <div class="intro">秒杀即将开始...</div>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">{{ flash_sale_data.products[0].name }}</div>
                        <div class="progress">
                            <div class="right">限量{{ flash_sale_data.products[0].stock_info.all_stock_num }}张</div>
                            <div class="progress-bar">
                                <!--blue left, grey sold-->
                                <div class="left-bar" ms-class="all-radius:flash_sale_data.products[0].left_bar==280" ms-css-width="{{flash_sale_data.products[0].left_bar}}">
                                    <span ms-if="flash_sale_data.products[0].white_bar != 280" class="left-appendix">{{ flash_sale_data.products[0].stock_info.current_stock_num }}</span>
                                </div>
                                <div class="sold-bar" ms-class="all-radius:flash_sale_data.products[0].sold_bar==280" ms-css-width="{{flash_sale_data.products[0].sold_bar}}"></div>
                            </div>
                        </div>
                        <a class="buy-btn" ms-href="{{flash_sale_data.products[0].link_url}}">{{ flash_sale_data.products[0].button_label }}</a>
                    </div>
                </div>
            </div>
            <!--ccb credit card-->
            <div class="ccb-card">
                <img src="themes/public/images/activities/ccbsale/ccb_credit_card.png">
                <a class="get-cards" href="http://creditcard.ccb.com/index.html?id=20150113_1421114013&orgCode=D999001013&flag=standard_form" target="_blank">在线办卡 ></a>
                <a class="more-activities" href="http://creditcard.ccb.com/creditCard/20150113_1421114013.html" target="_blank">更多活动 ></a>
            </div>
            <!--discount sale-->
            <div class="discount-sale">
                <img class="slogan" src="themes/public/images/activities/ccbsale/discount_header_image.png">
                <div class="title clearfix">
<!--                    <div class="products-title">{{ discount_data.groups[0].location.cn_name }}</div>-->
                    <a class="products-more" ms-href="{{discount_data.groups[0].location.link_url}}" target="_blank">更多韩国商品 ></a>
                </div>
                <div class="each-block" ms-repeat-product="discount_data.groups[0].products"
                     ms-class-1="double:$index==0 || $index==9">
                    <div class="product-cover">
                        <img class="cover-image" ms-src="{{product.cover_image_url}}?imageView/1/w/308/h/310" ms-if="$index!=0 && $index!=9">
                        <img class="cover-image" ms-src="{{product.cover_image_url}}?imageView/1/w/638/h/310" ms-if="$index==0 || $index==9">
                        <img class="product-tag" src="themes/public/images/activities/ccbsale/discount_20.png" ms-if="$index!=0">
                        <img class="product-tag" src="themes/public/images/activities/ccbsale/discount_50.png" ms-if="$index==0">
                        <div class="price-zone">
                            <span>玩途价</span>
                            <span class="RMB">￥</span>
                            <span class="new-price">{{ product.show_price.price }}</span>
                            <span>市场价</span>
                            <span class="RMB">￥</span>
                            <span class="old-price">{{ product.show_price.orig_price }}</span>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-name">{{ product.name }}</div>
                        <a class="product-buy" ms-href="{{product.link_url}}">立即抢购</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="activity-rule">
        <div class="container">
            <img src="themes/public/images/activities/ccbsale/activity_rule.png">
        </div>
    </div>

</div>

<!--script-->
<script type="text/javascript">

    //  main controller
    var ccbSaleCtrl = avalon.define("ccbSale", function (vm) {

        vm.flash_sale_data = {};
        vm.discount_data = {};

        vm.tab_image = '';

        vm.activity_phase = 1;
        vm.timer_label = '';
        vm.flash_date = '';
        vm.flash_time = '';

        vm.timer = {
            hours: 0,
            minutes: 0,
            seconds: 0
        };

        vm.timerDisplay = {
            hours: 0,
            minutes: 0,
            seconds: 0
            //            hour_hundred : 0,
            //            hour_one: 0,
            //            hour_ten: 0,
            //            minute_one: 0,
            //            minute_ten: 0,
            //            second_one: 0,
            //            second_ten: 0
        };

        vm.moveZone = function(part) {
          var element = "." + part;
          $(document.body).animate({scrollTop: $(element).offset().top}, 500);
        };
    });

    var initFlashData = function () {

        //get flash activity time
        var arr = ccbSaleCtrl.flash_sale_data.start_date.split(" ");
        ccbSaleCtrl.flash_date = arr[0];
        ccbSaleCtrl.flash_date = ccbSaleCtrl.flash_date.replace(/-/g, ".");
        ccbSaleCtrl.flash_time = "10AM";//modify here when the time is changed

        //set timer display controller
        if(ccbSaleCtrl.flash_sale_data.status == 4) {
            setTimerZone(ccbSaleCtrl.flash_sale_data);
            ccbSaleCtrl.activity_phase = 2;// '1' means ready, '2' means 'timer starts'
        }
        else if(ccbSaleCtrl.flash_sale_data.status == 2) {
            if(ccbSaleCtrl.flash_sale_data.countdown <= 259200) {
                setTimerZone(ccbSaleCtrl.flash_sale_data);
                ccbSaleCtrl.activity_phase = 2;
            }
            else
                ccbSaleCtrl.activity_phase = 1;
        }
        else if(ccbSaleCtrl.flash_sale_data.status == 3) {
            setTimerZone(ccbSaleCtrl.flash_sale_data);
            ccbSaleCtrl.activity_phase = 2;
        }

        for(var i = 0;i < ccbSaleCtrl.flash_sale_data.products.length;i++) {

            var product = ccbSaleCtrl.flash_sale_data.products[i];
            //Set Progress Bar
            product.left_bar = product.stock_info.current_stock_num * 280 / product.stock_info.all_stock_num;
            product.sold_bar = (product.stock_info.all_stock_num - product.stock_info.current_stock_num) * 280 / product.stock_info.all_stock_num;

            //Set Activity Button Label
            if (ccbSaleCtrl.flash_sale_data.status == 2) {
                product.button_label = "准备开抢";
                ccbSaleCtrl.timer_label = "开始倒计时：";
            }
            else if (ccbSaleCtrl.flash_sale_data.status == 3) {
                ccbSaleCtrl.timer_label = "秒杀倒计时：";
                if (product.stock_info.current_stock_num == 0) {
                    product.button_label = "已售罄";
                    cancelHref($(".buy-btn"));
                }
                else {
                    product.button_label = "立即秒杀";
                }
            }
            else {
                ccbSaleCtrl.timer_label = "秒杀已结束：";
                product.button_label = "秒杀结束";
                cancelHref($(".buy-btn"));
            }
        }
    };

    var cancelHref = function(elem) {
        window.setTimeout(function() {
            elem.removeAttr("href");
        }, 100);
    };

    var updateTimerDisplay = function () {
        var temp = "00" + ccbSaleCtrl.timer.hours;
        if(ccbSaleCtrl.timer.hours >= 100)
            ccbSaleCtrl.timerDisplay.hours = temp.substring(temp.length - 3, temp.length);
        else
            ccbSaleCtrl.timerDisplay.hours = temp.substring(temp.length - 2, temp.length);
        temp = "00" + ccbSaleCtrl.timer.minutes;
        ccbSaleCtrl.timerDisplay.minutes = temp.substring(temp.length - 2, temp.length);
        temp = "00" + ccbSaleCtrl.timer.seconds;
        ccbSaleCtrl.timerDisplay.seconds = temp.substring(temp.length - 2, temp.length);
        //        ccbSaleCtrl.timerDisplay.hour_one = ccbSaleCtrl.timer.hours % 10;
        //        ccbSaleCtrl.timerDisplay.hour_ten = Math.floor(ccbSaleCtrl.timer.hours % 100 / 10);
        //        ccbSaleCtrl.timerDisplay.hour_hundred = Math.floor(ccbSaleCtrl.timer.hours / 100);
        //        ccbSaleCtrl.timerDisplay.minute_one = ccbSaleCtrl.timer.minutes % 10;
        //        ccbSaleCtrl.timerDisplay.minute_ten = Math.floor(ccbSaleCtrl.timer.minutes / 10);
        //        ccbSaleCtrl.timerDisplay.second_one = ccbSaleCtrl.timer.seconds % 10;
        //        ccbSaleCtrl.timerDisplay.second_ten = Math.floor(ccbSaleCtrl.timer.seconds / 10);
    };

    var setTimerZone = function (sale_data) {
        ccbSaleCtrl.timer.hours = Math.floor(sale_data.countdown / 3600);
        ccbSaleCtrl.timer.minutes = Math.floor(sale_data.countdown % 3600 / 60);
        ccbSaleCtrl.timer.seconds = sale_data.countdown % 60;
        if(ccbSaleCtrl.timer.hours == 0 && ccbSaleCtrl.timer.minutes == 0 && ccbSaleCtrl.timer.seconds == 0)
            return;
        else
            setTimerClock();
    };

    var setTimerClock = function () {

        ccbSaleCtrl.timer.seconds--;

        if (ccbSaleCtrl.timer.seconds == -1) {
            ccbSaleCtrl.timer.minutes--;
            ccbSaleCtrl.timer.seconds = 59;
        }
        if (ccbSaleCtrl.timer.minutes == -1) {
            ccbSaleCtrl.timer.hours--;
            ccbSaleCtrl.timer.seconds = 59;
            ccbSaleCtrl.timer.minutes = 59;
        }
        if (ccbSaleCtrl.timer.hours == -1) {
            window.location.reload();
            return;
        }
        updateTimerDisplay();
        window.setTimeout(setTimerClock, 1000);
    };

    var getAllData = function () {
        $.ajax({
            url: "activity/ccbsaledata",
            dataType: "json",
            success: function (res) {
                if (res.code == 200) {

                    //for IE8 , data must be initialized for the first time
                    for(var i = 0;i < res.data.flash_sale_data.products.length;i++) {
                        res.data.flash_sale_data.products[i].left_bar = 0;
                        res.data.flash_sale_data.products[i].sold_bar = 0;
                        res.data.flash_sale_data.products[i].button_label = "";
                    }

                    ccbSaleCtrl.tab_image = "tab" + res.data.phase_id;
                    ccbSaleCtrl.flash_sale_data = res.data.flash_sale_data;
                    ccbSaleCtrl.discount_data = res.data.discount_sale_data;
                    initFlashData();
                    $(function(){
                      $(".loading-mask").css("display", "none");
                    });

                }
                else
                    alert(res.msg);
            }
        });
    };

    getAllData();

</script>