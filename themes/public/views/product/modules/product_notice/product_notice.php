<div class="content-item product-service-include" id="tab_service_include" ms-controller="productNoticeCtrl"
     ms-if="data.please_read && local.type != 8">
    <div class="container">
        <div class="part-title-row">
            <div class="part-title">服务包含</div>
        </div>
        <div class="services clearfix" ms-repeat-service="data.service_include" ms-class="border-top : $index != 0">
            <div class="title-col">
                <div class="title-text" ms-html="service.title"></div>
            </div>
            <div class="content-col">
                <div class="service-include-md" ms-html="service.detail"></div>
            </div>
        </div>
    </div>
</div>

<div class="content-item product-please-read" id="tab_please_read" ms-controller="productNoticeCtrl"
     ms-if="data.please_read">
    <div class="container">
        <div class="part-title-row">
            <div class="part-title">购买须知</div>
        </div>
        <div class="notes clearfix">
            <div class="title-col">
                <div class="title-text">购买须知</div>
            </div>
            <div class="content-col">
                <div class="markdown-text" ms-visible="data.please_read.rules.sale_desc != ''">
                    <div>
                        <h4>购买时间</h4>
                        <ul>
                            <li ms-html="data.please_read.rules.sale_desc"></li>
                        </ul>
                    </div>
                </div>
                <div class="markdown-text" ms-visible="data.please_read.rules.redeem_desc">
                    <div>
                        <h4>兑换时间</h4>
                        <ul>
                            <li ms-html="data.please_read.rules.redeem_desc"></li>
                        </ul>
                    </div>
                </div>
                <div class="markdown-text" ms-visible="data.please_read.rules.shipping_desc">
                    <div>
                        <h4>发货限制</h4>
                        <ul>
                            <li ms-html="data.please_read.rules.shipping_desc"></li>
                        </ul>
                    </div>
                </div>
                <div class="markdown-text" ms-visible="data.please_read.rules.return_desc">
                    <div>
                        <h4>变更政策</h4>
                        <ul>
                            <li ms-html="data.please_read.rules.return_desc"></li>
                        </ul>
                    </div>
                </div>
                <div class="markdown-text clearfix" ms-html="data.please_read.buy_note"></div>
            </div>
        </div>
    </div>
</div>

<div class="content-item product-redeem-usage" id="tab_redeem_usage" ms-controller="productNoticeCtrl"
     ms-if="data.please_read">
    <div class="container">
        <div class="part-title-row">
            <div class="part-title">兑换及使用</div>
        </div>
        <div class="usage clearfix">
            <div class="title-col">
                <div class="title-text">兑换使用</div>
            </div>
            <div class="content-col">
                <div class="markdown-text clearfix" ms-html="data.redeem_usage.usage"></div>
            </div>
        </div>
        <div class="redeem-map border-top" ms-if="data.redeem_usage.pick_landinfo_groups.length > 0">
            <div id="redeem_map"></div>
        </div>
        <div class="redeem clearfix" ms-if="data.redeem_usage.pick_landinfo_groups.length > 0">
            <div class="title-col">
                <div class="title-text">兑换地点</div>
            </div>
            <div class="content-col">
                <div class="location-group" ms-repeat-group="data.redeem_usage.pick_landinfo_groups">
                    <div class="location-group-title" ms-class="first : $index == 0"
                         ms-visible="data.redeem_usage.pick_landinfo_groups.length > 1">{{ group.title }}:
                    </div>
                    <div class="markdown-text">
                        <div ms-repeat-location="group.landinfos">
                            <h4>兑换地点<i class="location-icon"
                                       ms-css-background-position="{{'0 ' + ($index + group.location_count) * -32 + 'px'}}"></i>
                            </h4>
                            <ul>
                                <li>{{ location.name }}{{ location.en_name }}</li>
                                <li>详细地址：{{ location.address }}</li>
                                <li ms-visible="location.phone">联系电话：{{ location.phone }}</li>
                                <li ms-visible="location.open_time || location.close_time">
                                    <span ms-visible="location.open_time">开放时间：{{ location.open_time }}</span><br>
                                    <span ms-visible="location.close_time" style="padding-left: 13px">关闭时间：{{ location.close_time }}</span>
                                </li>
                                <li ms-visible="location.communication">到达方式：{{ location.communication }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>