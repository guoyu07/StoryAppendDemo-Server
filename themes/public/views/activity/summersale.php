<style>
    .activity-top {
        width: 100%;
        height: 470px;
        /*background-image: url("themes/public/images/activities/summer-sale/top_header.png");*/
        background-image: url("http://hitour.qiniudn.com/8ac086d367754f3f0b723aac4132635b.png");
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
    }
    .activity-top button {
        background-color: rgb(16,119,138);
        width: 100px;
        height: 30px;
        position: absolute;
        left: 50%;
        margin-left: -50px;
        bottom: 33px;
        color: white;
        outline: 0;
        border: 0;
    }
    .activity-top button img {
        margin-left: 5px;
    }

    .rule-desc {
        height: 206px;
        background-image: url("themes/public/images/activities/summer-sale/rule_description.png");
        background-position: center;
        background-repeat: no-repeat;
    }

    .tab-bar {
        background-image: url("themes/public/images/activities/summer-sale/bar_bg3.png");
        background-position: center;
        background-repeat: no-repeat;
        height: 90px;
    }

    .tab-bar .catalog-list {
        list-style: none;
        padding-left: 0;
        margin: 0 auto;
        width: 1001px;
        position: relative;
        height: 90px;
    }
    .tab-bar .catalog-list li {
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
        display: inline-block;
        width: 200px;
        /*height: 90px;*/
        position: relative;
    }

    .tab-bar .catalog-list li .tab-date {
        position: absolute;
        bottom: 15px;
        right: 30px;
        font-size: 18px;
    }

    .tab-image {
        vertical-align: bottom;
    }

    .activity-content {
        width: 100%;
        text-align: center;
        padding-bottom: 40px;
    }

    .city-zone {
        width: 1001px;
        margin: 0 auto;
        padding-top: 23px;
        padding-bottom: 40px;
        color: white;
    }

    .city-top {
        position: relative;
        height: 121px;
    }

    .city-top .city-recommend {
        position: absolute;
        font-size: 40px;
        bottom: -4px;
        left: 260px;
        line-height: 40px;
        font-style: italic;
    }
    .city-top .city-more {
        position: absolute;
        cursor: pointer;
        text-decoration: none;
        font-size: 18px;
        color: #ffffff;
        right: 20px;
        line-height: 18px;
        bottom: 20px;
    }
    .city-top .city-more:hover {
        text-decoration: none;
        color: #ffffff;
    }

    .product-block {
        display: inline-block;
        margin: 0px 20px 20px 20px;
    }

    .product-lg {
        color: white;
        height: 425px;
        overflow: hidden;
        background-color: white;
        display: block;
        position: relative;
        text-decoration: none;
    }
    .product-lg:hover {
        color: inherit;
        text-decoration: none;
    }
    .product-lg:hover .product-mask{
        display: block;
        opacity: 1;
    }

    .product-lg .cover-ctn-lg {
        overflow: hidden;
        height: 304px;
    }
    .product-lg .cover-ctn-lg .cover-ctn-image1 {
        width: 460px;
        height: 304px;
    }
    .product-lg .cover-ctn-lg .cover-ctn-image2 {
        width: 960px;
        height: 304px;
    }

    .product-lg .product-info {
        padding: 0 18px 0 18px;
        position: relative;
        height: 70px;
        text-align: left;
    }

    .product-lg .product-info .product-name {
        display: inline-block;
        text-align: left;
        font-size: 18px;
        width: 415px;
        vertical-align: middle;
        color: #333333;
    }

    .product-lg .product-info .product-name-lg {
        display: inline-block;
        text-align: left;
        font-size: 24px;
        width: 850px;
        vertical-align: middle;
        color: #333333;
    }
    .product-lg .product-info .product-tmp {
        display: inline-block;
        vertical-align: middle;
        height: 70px;
    }

    .product-lg .price-ctn {
        color: #ff6600;
        font-weight: bold;
        font-family: "helvetica_neue_thin";
        margin-left: 18px;
        text-align: right;
        padding-right: 30px;
        font-size: 34px;
        line-height: 34px;
    }

    .product-lg .price-ctn .orig-mark {
        font-size: 14px;
    }

    .product-lg .price-ctn .price-btn {
        color: #ffffff;
        background-color: #ff6600;
        border: 1px solid #d95700;
        font-size: 20px;
        width: 200px;
        text-align: center;
        font-weight: normal;
        vertical-align: middle;
    }

    .product-lg .price-ctn .rmb:before{
        font-family: "Hitour";
        content: "\e617";
        font-style: normal;
        font-size: 70%;
        position: relative;
        top: -1px;
    }

    .product-mask {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        height: 304px;
        opacity: 0;
        background-color: rgba(0,0,0,0.8);
        color: #fff;
        overflow: hidden;
        line-height: 26px;
        font-weight: bold;
        transition: 0.6s;
        -moz-transition: 0.6s; /* Firefox 4 */
        -webkit-transition: 0.6s; /* Safari 和 Chrome */
        -o-transition: 0.6s;
    }

    .product-mask .cell-wrap-lg {
        position: relative;
        display: table-cell;
        vertical-align: middle;
        height: 304px;
        text-align: center;
        width: 460px;
        transition: 0.6s;
        -moz-transition: 0.6s; /* Firefox 4 */
        -webkit-transition: 0.6s; /* Safari 和 Chrome */
        -o-transition: 0.6s;
    }

    .product-mask .cell-wrap-lg .product-desc-lg {
        display: inline-block;
        padding: 26px 22px 26px 26px;
        width: 250px;
        font-size: 18px;
        position: relative;
        text-align: left;
    }

    .product-mask .cell-wrap-lg .product-desc-lg:before {
        font-family: "Hitour";
        content: '\e60b';
        position: absolute;
        top: 2px;
        left: 2px;
    }
    .product-mask .cell-wrap-lg .product-desc-lg:after {
        font-family: "Hitour";
        content: '\e60c';
        position: absolute;
        bottom: 2px;
        right: 2px;
    }


    .special-tag {
        background: url(themes/public/images/activities/summer-sale/tag_large.png);
        width: 107px;
        height: 84px;
        position: absolute;
        left: 14px;
        top: 0px;
        z-index:20;
        display: none;
    }

    .unionPay-logo {
        margin-top: 25px;
        margin-bottom: 45px;
        color: white;
        position: relative;
        text-align: left;
    }

    .unionPay-icon {
        display: inline-block;
        margin-left: 20px;
        margin-bottom: 12px;
    }

    .unionPay-title {
        display: inline-block;
        font-size: 30px;
        line-height: 30px;
    }

    .unionPay-more {
        position: absolute;
        cursor: pointer;
        text-decoration: none;
        font-size: 18px;
        color: #ffffff;
        right: 20px;
        line-height: 18px;
        bottom: 15px;
    }

    .unionPay-more:hover {
        text-decoration: none;
        color: inherit;
    }

    .unionPay-products {
        text-align: left;
    }

    .unionPay-block {
        width: 460px;
        height: 140px;
        display: inline-block;
        cursor: default;
        text-align: left;
        margin: 0px 20px 50px 20px;
    }

    .unionPay-block .upBlock-left {
        display: inline-block;
        cursor: pointer;
        width: 200px;
        height: 140px;
        border-radius: 10px;
        box-shadow: 8px 8px 8px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    .unionPay-block .upBlock-left:hover .upBlock-mask{
        opacity: 1;
        display: block;
    }

    .unionPay-block .upBlock-left .cover-image {
        position: absolute;
        right: 0px;
        bottom: 0px;
        top: 0px;
        left: 0px;
        width: 200px;
        height: 140px;
        border-radius: 10px;
    }

    .unionPay-block .upBlock-left .unionPay_smIcon{
        position: absolute;
        right: 10px;
        bottom: 10px;
    }

    .upBlock-mask {
        border-radius: 10px;
        width: 200px;
        height: 140px;
        padding: 10px;
        position: absolute;
        background-color: rgba(0, 0, 0, 0.8);
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        opacity: 0;
        transition: 0.6s;
        -moz-transition: 0.6s; /* Firefox 4 */
        -webkit-transition: 0.6s; /* Safari 和 Chrome */
        -o-transition: 0.6s;
    }

    .upBlock-mask p{
        color: white;
        font-size: 14px;
        width: 180px;
        margin-bottom: 10px;
        line-height: 14px;
    }

    .unionPay-block .upBlock-right {
        margin-left: 20px;
        margin-right: 20px;
        display: inline-block;
        width: 200px;
        height: 140px;
        vertical-align: top;
    }

    .unionPay-block .upBlock-right .upProduct-name{
        font-size: 22px;
        line-height: 22px;
        margin-top: 10px;
    }

    .unionPay-block .upBlock-right .upProduct-desc{
        font-size: 14px;
        margin-top: 25px;
    }


    .hitour-guarantee {
        background: #f6f6f6; }
    .hitour-guarantee .section-title {
        text-align: center;
        font-family: STFangSong, FangSong;
        font-weight: normal; }
    .hitour-guarantee .section-title h1 {
        color: #525252;
        font-size: 34px;
        line-height: 40px;
        margin-top: 62px;
        margin-bottom: 0;
        font-weight: normal; }
    .hitour-guarantee .section-title h2 {
        color: #525252;
        font-size: 22px;
        line-height: 24px;
        margin-top: 28px;
        font-weight: normal;
        margin-bottom: 54px; }
    .hitour-guarantee .section-content {
        font-size: 0;
        margin-bottom: 60px; }
    .hitour-guarantee .section-content .mark {
        color: #737373;
        margin: 0 12px 0 0;
        font-size: 90px;
        line-height: 100%;
        display: inline-block;
        *display: inline;
        *zoom: 1;
        vertical-align: top; }
    .hitour-guarantee .section-content .detail {
        color: gray;
        font-size: 16px;
        line-height: 24px;
        display: inline-block;
        *display: inline;
        *zoom: 1;
        vertical-align: top; }
    .hitour-guarantee .section-content .detail .guarantee-title {
        color: #6db381; }

</style>

<div class="activity-top" ms-controller="activity">
    <button id="rule_button">
        活动规则<img id="toggle_control" src="themes/public/images/activities/summer-sale/toggle_up.png">
    </button>
</div>
<div class="rule-desc">
</div>
<div class="tab-bar" ms-controller="tabGroup">
    <ul class="catalog-list">
        <li class= "tab" ms-repeat-group="phases" ms-data-index="group.phase_id"
            ms-click="switchTab()" ms-class="active:group.active">
<!--            <p class="tab-date" ms-css-color="group.text_color">{{group.date_range}}</p>-->
            <img class="tab-image" ms-src="themes/public/images/activities/summer-sale/{{group.image}}.png" ms-if="group.active == 0">
            <img class="tab-image" ms-src="themes/public/images/activities/summer-sale/{{group.image}}-1.png" ms-if="group.active == 1">
        </li>
    </ul>
</div>
<section class="activity-content" ms-controller="productGroup" ms-css-background-color="{{tabInfo.bg_color}}">
    <div class="city-zone" ms-repeat-city="cities">
        <div class="city-top" ms-css-background-image="url({{city.city.nav_image_url}})">
<!--            <div class="city-recommend">玩乐推荐</div>-->
            <a class="city-more" target="_blank" ms-href="{{city.city.link_url}}">更多玩乐>></a>
        </div>
        <div class="city-content" ms-css-background-color="{{city.city.bg_color}}">
            <div class="city-products">
                <div class="product-block" ms-repeat-el="city.products" ms-css-width="{{el.blockWidth}}">
                    <a class="product-lg" target="_blank" ms-attr-href="{{el.link_url}}">
                        <div class="cover-ctn-lg">
                            <img ms-class-1="cover-ctn-image1:el.blockType==1"
                                 ms-class-2="cover-ctn-image2:el.blockType==2"
                                 ms-attr-src="{{el.cover_image_url}}?imageView/1/w/{{el.blockWidth}}/h/304" alt="{{el.name}}">
                        </div>
                        <div class="special-tag">
                        </div>
                        <div class="product-info">
                            <div ms-class-1="product-name:el.blockType==1"
                                 ms-class-2="product-name-lg:el.blockType==2">{{el.name}}</div>
                            <div class="product-tmp"></div>
                        </div>
                        <div class="price-ctn">
                            <span class="orig-price-lg"><span class="orig-mark">玩途价</span><span class="rmb"></span>{{el.show_price}}</span>
                            <button class="price-btn">银联支付 再减￥150</button>
                        </div>
                        <div class="product-mask">
                            <div class="cell-wrap-lg" ms-css-width="{{el.blockWidth}}">
                                <div class="product-desc-lg">{{el.summary}}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="unionPay-logo" ms-data-index="city.campaign" ms-if="city.campaign">
                <img class="unionPay-icon" src="themes/public/images/activities/summer-sale/unionPay_md.png">
                <div class="unionPay-title">{{city.city.cn_name}}银联刷卡优惠</div>
                <a class="unionPay-more" target="_blank" ms-attr-href="{{city.campaign_link}}">更多优惠>></a>
            </div>
            <div class="unionPay-products">
                <div class="unionPay-block" ms-repeat-promotion="city.campaign">
                    <div class="upBlock-left">
                        <a ms-attr-href="{{city.campaign_link}}" target="_blank">
                            <img class="cover-image" ms-src="{{promotion.image_url}}?imageView/1/w/200/h/140">
                            <img class="unionPay_smIcon"
                                 src="themes/public/images/activities/summer-sale/unionPay_sm.png">
                            <div class="upBlock-mask">
                                <p>优惠时间：</p>
                                <p>{{promotion.discount_date}}</p>
                                <p>活动范围：{{promotion.scope}}</p>
                            </div>
                        </a>
                    </div>
                    <div class="upBlock-right">
                        <div class="upProduct-name">{{promotion.name}}</div>
                        <div class="upProduct-desc">{{promotion.title}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hitour-notice">
        <img src="themes/public/images/activities/summer-sale/hitour_notice.png">
    </div>
</section>

<section class="hitour-guarantee">
    <div class="container">
        <div class="section-title st-4">
            <h1>出发！让你后顾无忧！</h1>
            <h2>无论你想去世界的任何一个角落，玩途都会为您提供最贴心的保障</h2>
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

<script>

    //add rule button action listener
    var toggleFlag = false;
    $("#rule_button").click(function(){
        toggleFlag = !toggleFlag;
        if(toggleFlag) {
            $("#toggle_control").attr("src","themes/public/images/activities/summer-sale/toggle_down.png");
            $(".rule-desc").slideToggle();
        }
        else {
            $("#toggle_control").attr("src","themes/public/images/activities/summer-sale/toggle_up.png");
            $(".rule-desc").slideToggle();
        }
    });

    //main controller
    var ctrl= avalon.define("activity",function(vm){
        vm.data='';

    });

    //tab controller
    var tabGroup = avalon.define("tabGroup", function(vm) {
        vm.phases = [];

        //init tab

        //add tab button listener
        vm.switchTab = function() {
            var list = this;
            var idIndex = list.getAttribute("data-index");
            productGroup.tabInfo = vm.phases[idIndex - 1];
            productGroup.cities = vm.phases[idIndex - 1].groups;
            productGroup.availibility = vm.phases[idIndex - 1].status;
            console.log(productGroup.cities);
            for(var i = 0;i < vm.phases.length;i++) {
                vm.phases[i].active = 0;
            }
            vm.phases[idIndex - 1].active = 1;
        }
    });

    //products controller
    var productGroup = avalon.define("productGroup", function(vm) {
        vm.tabInfo = {};
        vm.cities = [];
        vm.availibility = "";
    });

    //get data
    $.ajax({
        url : "activity/summersaledata",
        dataType : "json",
        success : function(res) {
            if(res.code == 200) {
                var data = res.data;
                var endFlag = false;
                ctrl.data = data;

                //initialize the status of each tab
                for(var i = 0;i < data.phases.length;i++) {
                    data.phases[i].active = 0;
                    data.phases[i].image = "00" + (i + 1);
                }

                //make the product whose index is odd, and which is last one has the odd index occupy two blocks
                for(var i = 0;i < data.phases.length;i++) {
                    for(var j = 0;j< data.phases[i].groups.length;j++) {
                        for(var k = 0;k < data.phases[i].groups[j].products.length;k++) {
                            data.phases[i].groups[j].products[k].blockWidth = 460;
                            data.phases[i].groups[j].products[k].blockType = 1;
                        }
                        if(data.phases[i].groups[j].products.length % 2 == 1) {
                            data.phases[i].groups[j].products[data.phases[i].groups[j].products.length - 1].blockWidth = 960;
                            data.phases[i].groups[j].products[data.phases[i].groups[j].products.length - 1].blockType = 2;
                        }
                    }
                }
                tabGroup.phases = data.phases;

                //initialize the first tab status
                for(var i = 0;i < data.phases.length;i++) {
                    if(data.phases[i].status == 3) {
                        productGroup.tabInfo = data.phases[i];
                        productGroup.cities = data.phases[i].groups;
                        productGroup.availibility = data.phases[i].status;
                        tabGroup.phases[i].active = 1;
                        endFlag = true;
                    }
                }
                if(!endFlag) {
                    tabGroup.phases[4].active = 1;
                    productGroup.tabInfo = data.phases[4];
                    productGroup.cities = data.phases[4].groups;
                }
            }
            else
                alert("非常抱歉，后台服务异常，请稍后重试。");
        }
    });

    $(document).ready(function(){
        $(".rule-desc").hide();
        $(".loading-mask").css("display","none");
    });

//    //statistic of the stayed time.
//    var second = 0;
//    var minute = 0;
//    var hour = 0;
//    var dwellTime = 0;
//    var timeStatistic = function() {
//        second++;
//        if(second == 60) {
//            second = 0;
//            minute++;
//        }
//        if(minute == 60) {
//            hour++;
//            minute = 0;
//        }
//        dwellTime = hour + ":" + minute + ":" + second;
//        console.log(dwellTime);
//        window.setTimeout("timeStatistic();", 1000);
//    };
//    var timeControl = window.setTimeout("timeStatistic();", 1000);
</script>