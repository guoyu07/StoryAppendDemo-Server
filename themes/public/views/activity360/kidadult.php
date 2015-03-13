<!--
    This is a mixed page about kidadult and 11.11 activities.
-->

<!--stylesheet-->
<link rel="stylesheet" href="themes/public/stylesheets/kid-adult.css">

<!--template-->
<div class="kid-adult main-content" ms-controller="actCtrl">
    <!--activity header-->
    <!--    <div class="activity-header" ms-class-1="kidadult-image:pageType == 1" ms-class-2="carnival-image:pageType == 2 " ms-class-3="shopping-image:pageType == 3 "></div>-->
    <img src="themes/public/images/activities/carnival-11.11/activity360-header.jpg" width="100%" ms-if="pageType == 2">
    <img src="themes/public/images/activities/shopping/activity360-header.jpg" width="100%" ms-if="pageType == 3">
    <div class="theme-park" ms-repeat-group="allData.groups" ms-attr-id="'park_' + group.group_id"
         ms-class-1="bg-grey:pageType == 1 && $index % 2 == 0" ms-class-2="bg-red:pageType == 1 && $index % 2 == 1"
         ms-class-3="bg-linear-gradient:pageType == 2  && $index == 0" ms-class-31="bg-linear-gradient-shopping:pageType == 3  && $index == 0" ms-class-4="bg-white:(pageType == 2 || pageType == 3) && $index % 2 == 1" ms-class-5="bg-light-grey:(pageType == 2 || pageType == 3) && $index % 2 == 0">
        <div class="container">
            <div class="park-name">{{group.name}}<span ms-class="color-white:pageType == 1 && $index % 2 == 1">{{group.en_name}}</span></div>
            <div class="one-product" ms-repeat-product="group.products">

                <!--kidadult product block-->
                <a class="product-block" ms-if="pageType == 1">
                    <img class="product-cover" ms-src="{{product.cover_image_url}}?imageView/1/w/240/h/210">
                    <div class="product-info">{{product.name}}</div>
                    <div class="product-price">
                        <span>&yen;{{product.show_price.price}}购成人票</span>
                    </div>
                    <div class="product-mask">
                        <div class="mask-top">
                            <img class="qr-code" ms-src="{{product.qr_code_url}}?imageView/1/w/140/h/140">
                            <p class="saved-price">省&yen;{{product.show_price.orig_price - product.show_price.price}}</p>
                        </div>
                        <div class="bg-intro kidadult-intro"></div>
                    </div>
                </a>

                <!--11.11 carnival product block-->
                <a class="product-block" ms-href="{{product.link_url}}" ms-if="(pageType == 2 || pageType == 3)">
                    <img class="product-cover" ms-src="{{product.cover_image_url}}?imageView/1/w/240/h/210">
                    <div class="product-info">{{product.name}}</div>
                    <div class="product-price">
                        <span class="original-price">&yen;{{product.show_price.orig_price}}</span>
                        <span>&yen;{{product.show_price.price}}起</span>
                    </div>
                    <div class="product-mask">
                        <div class="mask-top">
                            <img class="qr-code" ms-src="{{product.qr_code_url}}?imageView/1/w/140/h/140">
                            <img class="unionPay-logo" src="themes/public/images/activities/carnival-11.11/unionpay-long-logo.png">
                        </div>
                        <div class="bg-intro carnival-intro"></div>
                    </div>
                </a>
                <div class="product-location">
                    <i class="icon-location"></i>
                    {{product.city_name}}
                </div>
            </div>
        </div>
    </div>

    <!--navigation-->
    <div class="sidebar" ms-if="pageType == 2">
        <div class="side-top"></div>
        <div class="nav-content">
            <img ms-src="themes/public/images/activities/carnival-11.11/nav_{{nav.group_id}}.png"
                 ms-repeat-nav="allData.groups" ms-click="scrollTo(nav.group_id)">
        </div>
    </div>

    <div class="sidebar" ms-if="pageType == 3">
        <!--        <div class="side-top"></div>-->
        <div class="nav-content-shopping">
            <img ms-src="themes/public/images/activities/shopping/nav_{{nav.group_id}}.png"
                 ms-repeat-nav="allData.groups" ms-click="scrollTo(nav.group_id)">
        </div>
    </div>

    <!--hitour guarantee-->
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
</div>

<!--script-->
<script type="text/javascript">

    //  main controller
    var unionPayCtrl = avalon.define("actCtrl", function (vm) {
        vm.allData = {};

        vm.pageType = 1;// "1" refers to kidadult, "2" refers to 11.11 hitour carnival, "3"-Shopping

        vm.scrollTo = function(index) {
            var elem_id = "#park_" + index;
            var offsetTop = $(elem_id ).offset().top;
            $("body" ).animate({scrollTop:offsetTop},600);
            console.log(offsetTop);
        }
    });


    (function() {
        $.ajax( {
            url      : $request_urls.getKidAdultData,
            dataType : "json",
            success  : function( res ) {
                if( res.code == 200 ) {

                    unionPayCtrl.allData = res.data;

                    if(unionPayCtrl.allData.activity_id == 118)
                        unionPayCtrl.pageType = 1;
                    else if (unionPayCtrl.allData.activity_id == 119)
                        unionPayCtrl.pageType = 2;
                    else if (unionPayCtrl.allData.activity_id == 126)
                        unionPayCtrl.pageType = 3;

                    $( function() {
                        $( ".loading-mask" ).css( "display", "none" );
                    } );

                } else
                    alert( res.msg );
            }
        } );
    })();

</script>

<script src="http://stat.union.360.cn/js/qunion.js"></script>
<script>
    $('body').delegate('a', 'click', function() {
        QUnion
            .use('go', 'AD4Bl1ORwfxu3ZNEy7kcPLhorKJYHp9MS8aI_ged-mWQjb2qFX5vnGU0Csiz6tVT')
            .sendLog({
                to: 69
            });
    });
</script>

<script src="http://s1.qhimg.com/!12e1a56b/monitor_go_zt.js"></script>