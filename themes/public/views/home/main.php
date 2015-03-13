
<div class="main-wrap">

<!--    <div id="carousel-example-generic" class=" carousel slide" data-ride="carousel" data-interval="false"-->
<!--         ms-controller="carouselList">-->
<!---->
<!--        <div class="carousel-inner">-->
<!--            <a class="item set-bg" ms-class="active:$first" ms-repeat-carousel="carousel_images"-->
<!--               ms-css-background-image="url({{carousel.image_url}})" ms-attr-href="{{carousel.link_url}}" ></a>-->
<!--        </div>-->
<!---->
<!--        <a class="left-icon-position carousel-control" href="#carousel-example-generic" data-slide="prev">-->
<!--            <i class="icon-arrow-left"></i>-->
<!--        </a>-->
<!--        <a class="right-icon-position carousel-control" href="#carousel-example-generic" data-slide="next">-->
<!--            <i class="icon-arrow-right"></i>-->
<!--        </a>-->
<!--    </div>-->
<div id="carousel-example-generic" class=" carousel slide"
     ms-controller="carouselList">

    <div class="carousel-inner">
        <a class="item set-bg" target="_blank" ms-class="active:$first" ms-repeat-carousel="carousel_images"
           ms-data-index="$index"
           ms-css-background-image="url({{carousel.image_url}})" ms-attr-href="{{carousel.link_url}}" ></a>
    </div>

    <button class="left-icon-position carousel-control go-left">
        <i class="icon-arrow-left"></i>
    </button>
    <button class="right-icon-position carousel-control go-right">
        <i class="icon-arrow-right"></i>
    </button>
</div>

    <!--
    <div class="top-section">
        <div class="top-img-ctn">

        </div>
        <div class="slogan">
            <img src="themes/public/images/home/slogan.png" alt=""/>
        </div>
        <div class="search-ctn">
            <div class="search-box">
                <input type="text" id="search_box" placeholder="输入您想去的地方" class="search-input"/>
                <span class="glyphicon glyphicon-search i-search"></span>
            </div>
        </div>
    </div>
    头部块 -->
    <section class="hitour-feature">
        <div class="container">
            <div class="section-title st-1">
              你好！要开启一段新旅程？
              <p>发现世界新玩法</p>
            </div>

            <div class="section-content row">
                <div class="feature-block col-md-4">
                    <div class="block-icon"><i class="ticket-icon"></i></div>
                    <div class="block-content">
                        <p class="block-title">景点门票</p>
                        <p class="block-body">超过50个海外城市，<br>400多个景点的门票、通票预定</p>
                    </div>
                </div>

                <div class="feature-block col-md-4">
                    <div class="block-icon"><i class="map-icon"></i></div>
                    <div class="block-content">
                        <p class="block-title">当地行程</p>

                        <p class="block-body">全球30个目的地超过300条<br>当地精选行程预订</p>
                    </div>
                </div>

                <div class="feature-block col-md-4">
                    <div class="block-icon"><i class="gift-icon"></i></div>
                    <div class="block-content">
                        <p class="block-title">新奇体验</p>

                        <p class="block-body">热气球、跳伞、摇滚音乐节，<br>观鲸……不同凡响的极致体验</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--分类推荐-->
    <section class="classify-recommend">
        <div class="container pr">
            <div class="top-wrap">
                <div class="wrap-title st-2">
                    <h1>一起来！跟世界说Hi！</h1>
                </div>
                <div  ms-controller="recommendList" class="nav">
                    <ul ms-each-el="list">
                        <li ms-class-1="actived:el.actived" ms-class-2="bg-orange:el.type==3" ms-data-index="$index" ms-click="switchRecommend($index)">{{el.name}}
                            <img ms-if="el.type==3" class="tab-tag" src="themes/public/images/activities/summer-sale/sale_icon.png">
                        </li>
<!--                        <li ms-if="$index<list.length-1">/</li>-->
                    </ul>
                </div>
            </div>
            <div class="product-list">
                <div ms-controller="recommend1" ms-class="slice-wrap {{status}}">
                    <div class="row">
                        <div ms-class="col-md-{{el.size}}" ms-repeat-el="first">
                            <div class="recommend-block">
                                <a ms-attr-href="{{el.link_url}}" class="product">
                                    <img class="special-tag" ms-if="el.type==3" src="themes/public/images/activities/summer-sale/tag_small.png">
                                    <div ms-css-background-image="url(themes/public/images/home/{{el.image}}.png)" class="mask-group" ms-class-1="none-bg:(el.type==2 || el.type==3)"
                                         ms-class-2="color-red:(el.image==1 || el.image==6) && el.type==2" ms-class-3="color-yellow:(el.image==2 || el.image==7)&& el.type==2" ms-class-4="color-blue:(el.image==3 || el.image==8)&& el.type==2" ms-class-5="color-green:el.image==4 && el.type==2"
                                         >
                                        <div class="product-name" ms-class-1="font-size20:el.type==1" ms-class-2="text-center:el.type==2" ms-class-3="activity-name:el.type==3">{{el.headline|html}}</div>
                                        <div ms-if="el.type!=3" ms-class-1="product-desc:el.type==2" ms-class-2="product-city:el.type==1">{{el.sub}}</div>
                                        <div class="price-zone" ms-if="el.type==3">
                                            <div class="price-new">{{el.price_new}}</div>
<!--                                            <div class="price-old">{{el.price_old}}</div>-->
                                            <button class="activity-btn">银联支付 再减￥50</button>
                                        </div>
                                    </div>
                                    <div ms-if="el.type==3" class="mask-group activity-group"></div>
<!--                                  <div class="mask-group" ms-if="el.type==1"></div>-->
<!--                                  <img class="mask-cities" ms-if="el.type==2" src="themes/public/images/home/circle_shadow_large.png">-->
<!--                                  <div ms-class-1="product-name:el.type==1" ms-class-2="product-city:el.type==2">{{el.headline|html|html}}</div>-->
<!--                                  <div ms-class-1="product-desc:el.type==1" ms-class-2="product-city-desc:el.type==2">{{el.sub}}</div>-->

<!--                                    <div class="product-name">{{el.headline|html|html}}</div>-->
<!--                                    <div class="product-desc">{{el.sub}}</div>-->

                                    <div class="cover-ctn"><img ms-attr-src="{{el.cover_url}}?imageView/5/w/{{el.width}}/h/{{el.height}}"
                                                                alt=""/></div>

                                    <div class="more-mask" ms-if="el.type==1 || el.type==3">
                                        <div class="cell-wrap">
                                            <div class="more-desc">
                                                <p class="desc-w">{{el.product_desc}}</p>

                                                <p class="price -ctn" ms-if="el.price">￥{{el.show_prices.price}}</p>
                                            </div>
                                        </div>

                                    </div>

                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div ms-class="col-md-{{el.size}}" ms-repeat-el="second">
                            <div class="recommend-block">
                                <a ms-attr-href="{{el.link_url}}" class="product">
                                    <img class="special-tag" ms-if="el.type==3" src="themes/public/images/activities/summer-sale/tag_small.png">
                                    <div ms-css-background-image="url(themes/public/images/home/{{el.image}}.png)" class="mask-group" ms-class-1="none-bg:(el.type==2 || el.type==3)"
                                         ms-class-2="color-red:(el.image==1 || el.image==6) && el.type==2" ms-class-3="color-yellow:(el.image==2 || el.image==7)&& el.type==2" ms-class-4="color-blue:(el.image==3 || el.image==8)&& el.type==2" ms-class-5="color-green:el.image==4 && el.type==2"
                                         >
                                        <div class="product-name" ms-class-1="font-size20:el.type==1" ms-class-2="text-center:el.type==2" ms-class-3="activity-name:el.type==3">{{el.headline|html}}</div>
                                        <div ms-if="el.type!=3" ms-class-1="product-desc:el.type==2" ms-class-2="product-city:el.type==1">{{el.sub}}</div>
                                        <div class="price-zone" ms-if="el.type==3">
                                            <div class="price-new">{{el.price_new}}</div>
<!--                                            <div class="price-old">{{el.price_old}}</div>-->
                                            <button class="activity-btn">银联支付 再减￥50</button>
                                        </div>
                                    </div>
                                    <div ms-if="el.type==3" class="mask-group activity-group"></div>
<!--                                    <div class="mask-group" ms-if="el.type==1"></div>-->
<!--                                    <img class="mask-cities" ms-if="el.type==2" src="themes/public/images/home/circle_shadow_large.png">-->
<!--                                    <div ms-class-1="product-name:el.type==1" ms-class-2="product-city:el.type==2">{{el.headline|html|html}}</div>-->
<!--                                    <div ms-class-1="product-desc:el.type==1" ms-class-2="product-city-desc:el.type==2">{{el.sub}}</div>-->
                                    <div class="cover-ctn"><img ms-attr-src="{{el.cover_url}}?imageView/5/w/{{el.width}}/h/{{el.height}}"
                                                                alt=""/></div>


                                    <div class="more-mask" ms-if="el.type==1 || el.type==3">
                                        <div class="cell-wrap">
                                            <div class="more-desc">
                                                <p class="desc-w">{{el.product_desc}}</p>

                                                <p class="price-ctn" ms-if="el.price">￥{{el.show_prices.price}}</p>
                                            </div>
                                        </div>

                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                <div ms-controller="recommend2" ms-class="slice-wrap {{status}}">
                    <div class="row">
                        <div ms-class="col-md-{{el.size}}" ms-repeat-el="first">
                            <div class="recommend-block">
                                <a ms-attr-href="{{el.link_url}}" class="product">
                                    <img class="special-tag" ms-if="el.type==3" src="themes/public/images/activities/summer-sale/tag_small.png">
                                    <div ms-css-background-image="url(themes/public/images/home/{{el.image}}.png)" class="mask-group" ms-class-1="none-bg:(el.type==2 || el.type==3)"
                                         ms-class-2="color-red:(el.image==1 || el.image==6) && el.type==2" ms-class-3="color-yellow:(el.image==2 || el.image==7)&& el.type==2" ms-class-4="color-blue:(el.image==3 || el.image==8)&& el.type==2" ms-class-5="color-green:el.image==4 && el.type==2"
                                         >
                                        <div class="product-name" ms-class-1="font-size20:el.type==1" ms-class-2="text-center:el.type==2" ms-class-3="activity-name:el.type==3">{{el.headline|html}}</div>
                                        <div ms-if="el.type!=3" ms-class-1="product-desc:el.type==2" ms-class-2="product-city:el.type==1">{{el.sub}}</div>
                                        <div class="price-zone" ms-if="el.type==3">
                                            <div class="price-new">{{el.price_new}}</div>
<!--                                            <div class="price-old">{{el.price_old}}</div>-->
                                            <button class="activity-btn">银联支付 再减￥50</button>
                                        </div>
                                    </div>
                                    <div ms-if="el.type==3" class="mask-group activity-group"></div>
<!--                                    <div class="mask-group" ms-if="el.type==1"></div>-->
<!--                                    <img class="mask-cities" ms-if="el.type==2" src="themes/public/images/home/circle_shadow_large.png">-->
<!--                                    <div ms-class-1="product-name:el.type==1" ms-class-2="product-city:el.type==2">{{el.headline|html|html}}</div>-->
<!--                                    <div ms-class-1="product-desc:el.type==1" ms-class-2="product-city-desc:el.type==2">{{el.sub}}</div>-->
                                    <div class="cover-ctn"><img ms-attr-src="{{el.cover_url}}?imageView/5/w/{{el.width}}/h/{{el.height}}"
                                                                alt=""/></div>


                                    <div class="more-mask" ms-if="el.type==1 || el.type==3">
                                        <div class="cell-wrap">
                                            <div class="more-desc">
                                                <p class="desc-w">{{el.product_desc}}</p>

                                                <p class="price-ctn" ms-if="el.price">￥{{el.show_prices.price}}</p>
                                            </div>
                                        </div>

                                    </div>

                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div ms-class="col-md-{{el.size}}" ms-repeat-el="second">
                            <div class="recommend-block">
                                <a ms-attr-href="{{el.link_url}}" class="product">
                                    <img class="special-tag" ms-if="el.type==3" src="themes/public/images/activities/summer-sale/tag_small.png">
                                    <div ms-css-background-image="url(themes/public/images/home/{{el.image}}.png)" class="mask-group" ms-class-1="none-bg:(el.type==2 || el.type==3)"
                                         ms-class-2="color-red:(el.image==1 || el.image==6) && el.type==2" ms-class-3="color-yellow:(el.image==2 || el.image==7)&& el.type==2" ms-class-4="color-blue:(el.image==3 || el.image==8)&& el.type==2" ms-class-5="color-green:el.image==4 && el.type==2"
                                         >
                                        <div class="product-name" ms-class-1="font-size20:el.type==1" ms-class-2="text-center:el.type==2" ms-class-3="activity-name:el.type==3">{{el.headline|html}}</div>
                                        <div ms-if="el.type!=3" ms-class-1="product-desc:el.type==2" ms-class-2="product-city:el.type==1">{{el.sub}}</div>
                                        <div class="price-zone" ms-if="el.type==3">
                                            <div class="price-new">{{el.price_new}}</div>
<!--                                            <div class="price-old">{{el.price_old}}</div>-->
                                            <button class="activity-btn">银联支付 再减￥50</button>
                                        </div>
                                    </div>
                                    <div ms-if="el.type==3" class="mask-group activity-group"></div>
<!--                                    <div class="mask-group" ms-if="el.type==1"></div>-->
<!--                                    <img class="mask-cities" ms-if="el.type==2" src="themes/public/images/home/circle_shadow_large.png">-->
<!--                                    <div ms-class-1="product-name:el.type==1" ms-class-2="product-city:el.type==2">{{el.headline|html|html}}</div>-->
<!--                                    <div ms-class-1="product-desc:el.type==1" ms-class-2="product-city-desc:el.type==2">{{el.sub}}</div>-->
                                    <div class="cover-ctn"><img ms-attr-src="{{el.cover_url}}?imageView/5/w/{{el.width}}/h/{{el.height}}"
                                                                alt=""/></div>


                                    <div class="more-mask" ms-if="el.type==1 || el.type==3">
                                        <div class="cell-wrap">
                                            <div class="more-desc">
                                                <p class="desc-w">{{el.product_desc}}</p>
                                                <p class="price-ctn" ms-if="el.price">{{el.show_prices.price}}</p>
                                            </div>
                                        </div>

                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                <div ms-controller="recommend3" ms-class="slice-wrap {{status}}">
                    <div class="row">
                        <div ms-class="col-md-{{el.size}}" ms-repeat-el="first">
                            <div class="recommend-block">
                                <a ms-attr-href="{{el.link_url}}" class="product">
                                    <img class="special-tag" ms-if="el.type==3" src="themes/public/images/activities/summer-sale/tag_small.png">
                                    <div ms-css-background-image="url(themes/public/images/home/{{el.image}}.png)" class="mask-group" ms-class-1="none-bg:(el.type==2 || el.type==3)"
                                         ms-class-2="color-red:(el.image==1 || el.image==6) && el.type==2" ms-class-3="color-yellow:(el.image==2 || el.image==7)&& el.type==2" ms-class-4="color-blue:(el.image==3 || el.image==8)&& el.type==2" ms-class-5="color-green:el.image==4 && el.type==2"
                                         >
                                        <div class="product-name" ms-class-1="font-size20:el.type==1" ms-class-2="text-center:el.type==2" ms-class-3="activity-name:el.type==3">{{el.headline|html}}</div>
                                        <div ms-if="el.type!=3" ms-class-1="product-desc:el.type==2" ms-class-2="product-city:el.type==1">{{el.sub}}</div>
                                        <div class="price-zone" ms-if="el.type==3">
                                            <div class="price-new">{{el.price_new}}</div>
<!--                                            <div class="price-old">{{el.price_old}}</div>-->
                                            <button class="activity-btn">银联支付 再减￥50</button>
                                        </div>
                                    </div>
                                    <div ms-if="el.type==3" class="mask-group activity-group"></div>
<!--                                    <div class="mask-group" ms-if="el.type==1"></div>-->
<!--                                    <img class="mask-cities" ms-if="el.type==2" src="themes/public/images/home/circle_shadow_large.png">-->
<!--                                    <div ms-class-1="product-name:el.type==1" ms-class-2="product-city:el.type==2">{{el.headline|html|html}}</div>-->
<!--                                    <div ms-class-1="product-desc:el.type==1" ms-class-2="product-city-desc:el.type==2">{{el.sub}}</div>-->
                                    <div class="cover-ctn"><img ms-attr-src="{{el.cover_url}}?imageView/5/w/{{el.width}}/h/{{el.height}}"
                                                                alt=""/></div>


                                    <div class="more-mask" ms-if="el.type==1 || el.type==3">
                                        <div class="cell-wrap">
                                            <div class="more-desc">
                                                <p class="desc-w">{{el.product_desc}}</p>

                                                <p class="price-ctn" ms-if="el.price">{{el.show_prices.price}}</p>
                                            </div>
                                        </div>

                                    </div>

                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div ms-class="col-md-{{el.size}}" ms-repeat-el="second">
                            <div class="recommend-block">
                                <a ms-attr-href="{{el.link_url}}" class="product">
                                    <img class="special-tag" ms-if="el.type==3" src="themes/public/images/activities/summer-sale/tag_small.png">
                                    <div ms-css-background-image="url(themes/public/images/home/{{el.image}}.png)" class="mask-group" ms-class-1="none-bg:(el.type==2 || el.type==3)"
                                         ms-class-2="color-red:(el.image==1 || el.image==6) && el.type==2" ms-class-3="color-yellow:(el.image==2 || el.image==7)&& el.type==2" ms-class-4="color-blue:(el.image==3 || el.image==8)&& el.type==2" ms-class-5="color-green:el.image==4 && el.type==2"
                                         >
                                        <div class="product-name" ms-class-1="font-size20:el.type==1" ms-class-2="text-center:el.type==2" ms-class-3="activity-name:el.type==3">{{el.headline|html}}</div>
                                        <div ms-if="el.type!=3" ms-class-1="product-desc:el.type==2" ms-class-2="product-city:el.type==1">{{el.sub}}</div>
                                        <div class="price-zone" ms-if="el.type==3">
                                            <div class="price-new">{{el.price_new}}</div>
<!--                                            <div class="price-old">{{el.price_old}}</div>-->
                                            <button class="activity-btn">银联支付 再减￥50</button>
                                        </div>
                                    </div>
                                    <div ms-if="el.type==3" class="mask-group activity-group"></div>
<!--                                    <div class="mask-group" ms-if="el.type==1"></div>-->
<!--                                    <img class="mask-cities" ms-if="el.type==2" src="themes/public/images/home/circle_shadow_large.png">-->
<!--                                    <div ms-class-1="product-name:el.type==1" ms-class-2="product-city:el.type==2">{{el.headline|html|html}}</div>-->
<!--                                    <div ms-class-1="product-desc:el.type==1" ms-class-2="product-city-desc:el.type==2">{{el.sub}}</div>-->
                                    <div class="cover-ctn"><img ms-attr-src="{{el.cover_url}}?imageView/5/w/{{el.width}}/h/{{el.height}}"
                                                                alt=""/></div>


                                    <div class="more-mask" ms-if="el.type==1 || el.type==3">
                                        <div class="cell-wrap">
                                            <div class="more-desc">
                                                <p class="desc-w">{{el.product_desc}}</p>

                                                <p class="price-ctn" ms-if="el.price">{{el.show_prices.price}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
           <!-- <div class="arrow-left glyphicon glyphicon-chevron-left"></div>
            <div class="arrow-right glyphicon glyphicon-chevron-right"></div>-->
        </div>

    </section>

    <!--地图区-->
    <div class="map-zone"></div>
    <!---->
    <div class="motto">
        <p class="motto-content st-3" >“ 旅行即生活 | <span>TO TRAVEL IS TO LIVE</span> ”</p>
        <p class="motto-author">— 安徒生 —</p>
    </div>

	<section class="city-list">
	    <!-- <div class="section-title">城市列表</div> -->
	    <div class="section-content container" ms-controller="cityList">
		    <div class="continent-container" ms-repeat-el="continents">
			    <div class="one-continent">
				    <div class="continent-name">{{el.cn_name}}</div>
				  </div>
			    <div class="countries-container" >
				    <div class="row">
					    <div class="one-country col-md-6" ms-repeat-elg="el.countries">
						    <div class="country-name col-md-3"><a ms-attr-href="{{elg.link_url}}">{{elg.cn_name}}</a></div>
						    <div class="cities-container col-md-9">
							    <a class="one-city" ms-repeat-elc="elg.cities" ms-attr-href="{{elc.link_url}}">{{elc.cn_name}}</a>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>



	    </div>
	</section>

    <section class="hitour-guarantee">
        <div class="container">
        <div class="section-title st-4">
            <h1>出发！让你后顾无忧！</h1>
            <h2>无论你想去世界的任何一个角落，玩途都会为你提供最贴心的保障</h2>
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
                        <p>赠送为期10天，保额15万<br>的太平洋境外旅游意外险，<br>助你安心出发、平安归来</p>
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



