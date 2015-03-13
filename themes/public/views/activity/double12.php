<!--stylesheet-->
<link rel="stylesheet" href="themes/public/lib/Tab/tab.css" />
<link rel="stylesheet" href="themes/public/stylesheets/activity.double12.css">

<!--template-->
<div class="page activity" ms-controller="double12Ctrl">
    <div class="activity-header">
        <div class="activity-timer">
            <div class="timer-title">{{ local.activity_status }}</div>
            <div class="timer-content">
                <div class="timer-hours">
                    <div class="hours-number">{{ local.timer.hours }}</div>
                    <p>HOURS</p>
                </div>
                <i>:</i>
                <div class="timer-minutes">
                    <div class="hours-number">{{ local.timer.minutes }}</div>
                    <p>MINUTES</p>
                </div>
                <i>:</i>
                <div class="timer-seconds">
                    <div class="hours-number">{{ local.timer.seconds }}</div>
                    <p>SECONDS</p>
                </div>
            </div>
        </div>
        <div class="activity-rule-btn"></div>
        <div class="hi-nav activity-nav clearfix" data-bind="nav-content">
            <div class="nav-item" ms-repeat-item="data.groups" ms-class="active: $index == 0"
                 ms-attr-data-target="tab{{ $index }}"
                 ms-css-background-color="item.tab_bg_color"
                 ms-css-border-color="item.tab_decorator_color">{{ item.name }}
            </div>
        </div>
    </div>
    <div class="hi-tab-content activity-content" id="nav-content">
        <div class="content-item group-wrap" ms-repeat-gp="data.groups" ms-attr-id="tab{{ $index }}"
             data-repeat-rendered="renderCallback"
             ms-css-background-color="gp.tab_bg_color">
            <div class="group-title-row container clearfix">
                <div class="left-col">
                    <h3 class="cn-name">{{ gp.name }}</h3>
                    <h3 class="en-name">{{ gp.en_name }}</h3>
                </div>
                <div class="right-col">
                    <a class="more-btn" ms-href="{{ gp.link_url }}" target="_blank">查看更多<i class="icon-arrow-right"></i></a>
                </div>
            </div>
            <div class="product-list container">
                <div class="products-wrap clearfix">
                    <div class="product-item" ms-repeat-pd="gp.products">
                        <div class="product-image-row">
                            <img class="product-image-cover"
                                 ms-src="{{ pd.cover_image_url }}?imageView/5/w/320/h/220">
                            <div class="product-price">
                                <span>抢购价
                                    <span class="wow-price">&yen;<em>{{ pd.show_price.price }}</em></span>
                                    <span class="orig-price">&nbsp;&yen;<em>{{ pd.show_price.orig_price }}</em></span>
                                </span>
                            </div>
                        </div>
                        <div class="product-name-row">
                            <h2 class="product-name">{{ pd.name }}</h2>
                        </div>
                        <div class="product-blood-row">
                            <div class="blood-above"><span>限量{{ pd.stock_info.all_stock_num }}张</span></div>
                            <div class="blood-line">
                                <div class="blood-left"
                                     ms-css-width="{{ pd.stock_info.current_stock_num / pd.stock_info.all_stock_num * 100 + '%' }}"
                                     ms-css-background-color="{{ gp.tab_bg_color }}"
                                     ms-css-border-right="1px solid {{ gp.tab_decorator_color }}"></div>
                            </div>
                            <div class="blood-under">
                                <span
                                    ms-visible="pd.stock_info.current_stock_num / pd.stock_info.all_stock_num >= 0.25">0</span>
                                <div class="blood-count"
                                     ms-class="low-blood : pd.stock_info.current_stock_num / pd.stock_info.all_stock_num < 0.25"
                                     ms-css-left="{{ pd.stock_info.current_stock_num / pd.stock_info.all_stock_num * 100 + '%' }}">
                                    <span>仅剩{{ pd.stock_info.current_stock_num }}张</span>
                                </div>
                            </div>
                        </div>
                        <div class="product-buy-row">
                            <a class="product-buy-btn" ms-href="pd.link_url" target="_blank" ms-class="{{ pd.class }}"
                               ms-css-background-color="{{ gp.tab_bg_color }}"
                               ms-css-border-bottom-color="{{ gp.tab_decorator_color }}">{{ pd.buy_info }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="activity-footer">
        <div class="activity-rule container"></div>
        <div class="activity-qr-phone container"></div>
        <div class="bottom-fixed-bar">
            <div class="inner-box">
                <div class="left-msg">
                    绝不错过下期精彩<br>订阅送20元优惠券
                </div>
                <div class="email-ctn">
                    <ol>
                        <li>留下您的邮箱，获取更多优惠信息！</li>
                        <li>20元优惠券，除活动商品外全站抵扣现金！</li>
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
</div>

<!--script-->
<script type="text/javascript" src="themes/public/lib/Tab/tab.js"></script>
<script type="text/javascript" src="themes/public/javascripts/activity/double12.js"></script>