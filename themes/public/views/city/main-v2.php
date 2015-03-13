<div class="city-content">

    <!-- city header -->
    <div class="city-header" ms-css-background-image="url({{city_image.banner_image_url}})"
         ms-controller="cityInfo">
        <h1 class="city-name container">{{cn_name}}</h1>
    </div>

    <!--  city-recommend  -->
    <div class="city-recommend" ms-controller="cityRecommend" ms-if="rec_item.type == '2'">
        <div class="rec-container">
            <div class="rec-title">
                <h2>{{rec_item.name}}</h2>

<!--                <span>从黎明到黄昏再到黎明，引领潮流的伦敦是一座永不停歇的不夜城</span>-->
            </div>
            <div class="rec-content">
                <div class="rec-slider-ctn">
                    <a class="rec-item" target="_blank" ms-href="{{pd.link_url}}" ms-repeat-pd="rec_item.products"
                       ms-css-background-image="url({{pd.cover_image.image_url}}?imageView/1/w/550/h/300)">
                        <div class="title-ctn">
                            <h3>{{pd.description.name}}</h3>
                            <div class="activity-tag" ms-if="pd.activity_info && pd.activity_info.length != 0">
                                <img ms-src="{{pd.activity_info.tag_large_url}}">
                            </div>
                            <span class="price-font">&yen;&nbsp;<em>{{pd.show_prices.price}}</em></span>
                        </div>
                        <div class="rec-hover">
                            <h3>{{pd.description.name}}</h3>
                            {{pd.description.service_include | html}}
                            <div class="go_more">了解更多 ></div>
                        </div>
                    </a>
                </div>
                <button class="to_left"></button>
                <button class="to_right"></button>
            </div>
        </div>
    </div>

    <!--  city-category  -->
    <div class="city-main">
        <div class="city-category" ms-class="active:$first" ms-controller="cityCategory" ms-repeat-gp="groups">
            <div class="cate-title">
                <div class="container">
                    <h2>{{gp.name}}</h2>
                    <span>{{gp.description}}</span>
                </div>
            </div>
            <div class="cate-content container">
                <!--      slider      -->
                <div class="city-slider ">
                    <div class="slider-ctn">
                        <!--slider-panel 1-->
                        <div class="slider-unit clearfix active">
                            <a ms-href="pd.link_url" target="_blank" class="cate-item" ms-repeat-pd="gp.products">
                                <div class="item-img">
                                    <img ms-if="($index % 10) != 0 && ($index % 10) != 6"
                                        ms-src="{{pd.cover_image.image_url}}?imageView/1/w/320/h/345">
                                    <img ms-if="($index % 10) == 0 || ($index % 10) == 6"
                                        ms-src="{{pd.cover_image.image_url}}?imageView/1/w/660/h/350">
                                    <div class="item-hover">
                                        <h3>{{pd.description.name}}</h3>
                                        {{pd.description.service_include | html}}
                                    </div>
                                    <div class="go_more">了解更多 ></div>
                                </div>
                                <div class="item-title">
                                    <h3>{{pd.description.name}}</h3>
                                    <div class="special-info" ms-if="pd.show_prices.special_info">{{pd.show_prices.special_info.reseller}}&nbsp;{{pd.show_prices.special_info.slogan}}</div>
                                    <div class="activity-tag" ms-if="pd.activity_info && pd.activity_info.length != 0">
                                        <img ms-src="{{pd.activity_info.tag_large_url}}">
                                    </div>
                                </div>
                                <div class="price-ctn clearfix">
                                    <span class="price_now price-font">&yen;<em>{{pd.show_prices.price}}</em></span>
                                    <div class="price_m">
                                        <label class="discount">{{(pd.show_prices.price / pd.show_prices.orig_price) * 10 | number(1)}}折</label>
                                        <span class="price_orig">&yen;{{pd.show_prices.orig_price}}&nbsp;</span>
                                    </div>
                                    <div class="spec_tag" ms-if="pd.description.benefit">{{pd.description.benefit}}</div>

                                </div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
<!--        <h2>&#21253;&#21547;&#26381;&#21153;</h2>&#10;&#10;<ol>&#10;<li>&#20840;&#31243;&#39640;&#26723;&#31354;&#35843;&#22823;&#24052;</li>&#10;<li>1&#23567;&#26102;45&#20998;&#38047;&#24052;&#22763;&#22478;&#24066;&#29615;&#28216;&#65292;&#32477;&#22909;&#26426;&#20250;&#35266;&#36175;&#24052;&#40654;&#21382;&#21490;&#21644;&#23439;&#20255;&#21476;&#36857;&#24314;&#31569;&#12290;</li>&#10;<li>&#27599;&#20301;&#28216;&#23458;&#37197;&#21457;&#19968;&#21482;&#32819;&#26426;&#65288;&#25968;&#30721;&#35821;&#38899;&#65289;&#36827;&#34892;&#21363;&#26102;&#35762;&#35299;&#28216;&#35272;&#12290;</li>&#10;<li>1&#23567;&#26102;&#22622;&#32435;&#27827;&#28216;&#33337;&#65292;&#21547;&#20013;&#25991;&#30005;&#23376;&#35821;&#38899;&#35762;&#35299;&#12290;</li>&#10;<li>&#22467;&#33778;&#23572;&#38081;&#22612;&#31532;&#20108;&#23618;&#38376;&#31080;&#65292;&#20056;&#30005;&#26799;&#21069;&#24448;&#35266;&#36175;&#25972;&#20010;&#24052;&#40654;&#20840;&#26223;&#65306;&#22307;&#24515;&#25945;&#22530;&#12289;&#33945;&#39532;&#29305;&#39640;&#22320;&#31561;&#23613;&#25910;&#30524;&#24213;&#12290;</li>&#10;<li>&#20813;&#25490;&#38431;&#36827;&#20837;&#33410;&#30465;&#26053;&#34892;&#26102;&#38388;&#12290;</li>&#10;<li>&#36192;30&#20803;&#29609;&#36884;&#29616;&#37329;&#25269;&#29992;&#21048;</li>&#10;</ol>",-->
        <div class="tab_bar-fixed" ms-controller="cityCategory">
            <ul class="container clearfix">
                <li ms-class="active: $first" ms-repeat-gp="groups">{{gp.name}}</li>
            </ul>
        </div>
    </div>

</div>

<div class="back-top">
</div>