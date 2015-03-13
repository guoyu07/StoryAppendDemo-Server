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
<style type="text/css">
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
        $("header").css("display","none");
        $("footer").css("display","none");
    });

    //statistic of the stayed time.
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
<script type="text/javascript" src="http://s1.qhimg.com/!12e1a56b/monitor_go_zt.js"></script>