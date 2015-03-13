<div class="order-detail-container" ms-visible="content_type == 'order'" ms-controller="account">

    <div class="info-header">
        <div class="row">
            <div class="order-block order-id" style="width: 148px;">
                <p class="title">订单号</p>
                <p class="numeric-text">{{ current_order.order_id }}</p>
            </div>
            <div class="order-block" style="width: 650px;text-align: left;padding-left: 20px;">
                <p class="title">商品名称</p>
                <p class="product-name" ms-if="current_type != 8 && is_combo != 1 ">{{ current_order_product.name }}</p>
                <p class="product-name" ms-if="current_type == 8 || is_combo == 1 ">{{ current_order_product_group_name }}</p>
            </div>
            <div class="order-block" style="width: 170px;">
                <p class="title">订单日期</p>
                <p class="numeric-text">{{ current_order.order_date }}</p>
            </div>
        </div>
    </div>

    <div class="info-status clearfix">
        <div class="status-desc">
            <p class="title">{{ current_order.status_name }}</p>
        </div>
        <div class="order-action-list">
            <a class="button-confirm order-action" ms-attr-href="current_order.payment_url" target="_blank"
               ms-if="current_order.payment_url">支付订单</a>
            <a class="button-warned order-action" href="javascript:;" ms-click="confirmCancel(current_order.order_id)"
               ms-if="current_order.cancel_url">取消订单</a>
            <a class="button-warned order-action" href="javascript:;" ms-click="confirmRefund(current_order.order_id)"
               ms-if="current_order.return_url">申请退订</a>
            <a class="button-confirm order-action" ms-attr-href="current_order.download_voucher_url"
               ms-if="current_order.download_voucher_url">下载兑换单</a>
        </div>
    </div>
    <!--普通商品-->
    <div class="info-section" ms-if="current_type != 8 && is_combo != 1">
        <div class="section-title">
            <span class="mark"></span>
            商品详细信息
        </div>
        <div class="section-content">
            <h3 class="product-title">{{ current_order_product.name }}</h3>
            <div class="row">
                <p class="item-pair col-md-3" ms-repeat="current_order_product_date">
                    <span class="label">{{ $key }}：</span>
                    <span class="value">{{ $val }}</span>
                </p>
                <p class="item-pair col-md-3" ms-repeat="current_order_product_info">
                    <span class="label">{{ $key }}：</span>
                    <span class="value">{{ $val }}</span>
                </p>
            </div>
        </div>
    </div>
    <!--酒店套餐 & combo -->
    <div class="info-section" ms-if="current_type == 8 || is_combo == 1 ">
        <div class="section-title">
            <span class="mark"></span>
            商品详细信息
        </div>
        <div class="section-content">
            <div class="main-product product-ctn">
                <div class="product-info">
                    <h3 class="product-title">{{ current_order_product_group_name }}</h3>
                    <div class="product-amount pull-right">
                        <span class="passenger-amount">{{ current_order_product_group_passenger.summary | html }}</span>
                        <span class="price-amount">￥{{ current_order_product_group_total }}</span>
                    </div>
                </div>
                <div class="product-free" ms-if="current_type == 8">
                    <div class="free">套餐包含：</div>
                    <ul class="free-item">
                        <li>1、{{ current_order_product_group_1_name }}</li>
                        <li ms-repeat-item="current_order_product_group_2">{{ $index + 2 }}、{{ item.name }}</li>
                    </ul>
                </div>
                <div class="product-inform row">
                    <p class="item-pair col-md-3" ms-repeat="current_order_product_group_0[0].date">
                        <span class="label">{{ $key }}：</span>
                        <span class="value">{{ $val }}</span>
                    </p>
                    <p class="item-pair col-md-3" ms-repeat="current_order_product_group_0[0].info">
                        <span class="label">{{ $key }}：</span>
                        <span class="value">{{ $val }}</span>
                    </p>
                    <p class="item-pair col-md-3" ms-repeat="current_order_product_group_0[0].special">
                        <span class="label">{{ $key }}：</span>
                        <span class="value">{{ $val }}</span>
                    </p>
                </div>
            </div>
            <div class="select-product product-ctn" ms-repeat-product="current_order_product_group_3" ms-if="current_order_product_group_3.length >1 || current_type == 8">
                <div class="product-info">
                    <h3 class="product-title">{{ product.name }}</h3>
                    <div class="product-amount pull-right">
                        <span class="passenger-amount">{{ product.passenger.summary | html }}</span>
                        <span class="price-amount">￥{{ product.total }}</span>
                    </div>
                </div>
                <div class="product-inform row">
                    <p class="item-pair col-md-3" ms-repeat="product.date">
                        <span class="label">{{ $key }}：</span>
                        <span class="value">{{ $val }}</span>
                    </p>
                    <p class="item-pair col-md-3" ms-repeat="product.info">
                        <span class="label">{{ $key }}：</span>
                        <span class="value">{{ $val }}</span>
                    </p>
                    <p class="item-pair col-md-3" ms-repeat="product.special">
                        <span class="label">{{ $key }}：</span>
                        <span class="value">{{ $val }}</span>
                    </p>
                </div>
                <div class="passenger-block">
                    <div class="col-md-2" ms-repeat-el="product.passenger.all">{{ el }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="info-section passenger clearfix">
        <div class="section-title">
            <span class="mark"></span>
            出行人信息
        </div>
        <div class="section-content">
            <p class="intro" ms-if="current_type != 8 && is_combo != 1">{{ current_order_passenger.summary | html }}</p>
            <p class="intro" ms-if="current_type == 8 || is_combo == 1">{{ current_order_product_group_passenger.summary | html }}</p>
            <!-- 普通商品 当日联系人-->
            <div class="passenger-section main-contact col-md-12" ms-if="(current_type != 8 && is_combo != 1 ) && current_order_passenger.has_lead == true">
                <div class="section-title">
                    <span class="mark"></span>
                    出行人1（当日联系人）
                </div>
                <div class="section-content with-bg">
                    <div class="info-block" ms-repeat="current_order_passenger_lead">
                        <p class="cell-title">{{ $key }}</p>
                        <p class="cell-content">{{ $val }}</p>
                    </div>
                </div>
            </div>
            <div ms-if="(current_type != 8 && is_combo != 1 ) && current_order_passenger_everyone.length > 0">
                <div class="passenger-section col-md-6" ms-repeat-person="current_order_passenger_everyone">
                    <div class="section-title">
                        <span class="mark"></span>
                        出行人{{ $index + passenger_index_revise }}
                    </div>
                    <div class="section-content with-bg">
                        <div class="info-block" ms-repeat="person">
                            <p class="cell-title">{{ $key }}</p>
                            <p class="cell-content">{{ $val }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 酒店套餐 -->
            <div class="passenger-section main-contact col-md-12" ms-if="(current_type == 8 || is_combo ==1 ) && current_order_product_group_passenger.has_lead">
                <div class="section-title">
                    <span class="mark"></span>
                    出行人1（当日联系人）
                </div>
                <div class="section-content with-bg">
                    <div class="info-block" ms-repeat="current_order_product_group_passenger.lead" ms-visible="$val">
                        <p class="cell-title">{{ $key }}</p>
                        <p class="cell-content">{{ $val }}</p>
                    </div>
                </div>
            </div>
            <div ms-if="(current_type == 8 || is_combo ==1 )  && current_order_product_group_everyone.length > 0">
                <div class="passenger-section col-md-6" ms-repeat-person="current_order_product_group_everyone">
                    <div class="section-title">
                        <span class="mark"></span>
                        出行人{{ $index + passenger_index_revise }}
                    </div>
                    <div class="section-content with-bg">
                        <div class="info-block" ms-repeat="person" ms-visible="$val">
                            <p class="cell-title">{{ $key }}</p>
                            <p class="cell-content">{{ $val }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="info-section insurance" ms-if="insurance_codes.length>0">
        <div class="section-title">
            <span class="mark"></span>
            保险信息
        </div>
        <div class="section-content">
            <p class="intro">
                本保险是由玩途和中国太平洋保险股份有限公司联合赠送一份为期10天，保额15万的境外旅游意外保险。<br />
                每位旅客的保单兑换码如下：
            </p>
            <div class="insurance-codes">
                <p class="item-pair with-bg" ms-repeat-el="insurance_codes">{{el.redeem_code}}</p>
            </div>
            <p class="item-pair">兑换电子保单的网站为：<a target="_blank" ms-attr-href="{{insurance_url}}">兑换网站</a></p>
            <p class="item-pair color-warned">
                <span class="label">兑换截止日期：</span>
                <span class="value">{{insurance_expires}}</span>
            </p>
        </div>
    </div>
</div>

<div class="delete-confirm-container" ms-visible="content_type == 'delete_contact'">
    <div class="delete-modal">
        <p>确定删除联系人？</p>
        <div class="delete-action">
            <button class="button-warned" ms-click="sendDelete('delete')">删除</button>
            <button class="button-confirm" ms-click="sendDelete('cancel')">取消</button>
        </div>
    </div>
</div>
<div class="cancel-confirm-container" ms-visible="content_type == 'cancel_order'">
    <div class="delete-modal">
        <p>确定取消该订单？</p>
        <div class="delete-action">
            <button class="button-warned" ms-click="sendCancel(true)">确定</button>
            <button class="button-confirm" ms-click="sendCancel(false)">取消</button>
        </div>
    </div>
</div>
<div class="refund-confirm-container" ms-visible="content_type == 'refund_order'">
    <div class="delete-modal">
        <p>确定退订此订单？</p>
        <div class="delete-action">
            <button class="button-warned" ms-click="sendRefund(true,true)">退订</button>
            <button class="button-confirm" ms-click="sendRefund(false,true)">取消</button>
        </div>
    </div>
</div>
<div class="process-result-container" ms-visible="content_type == 'process_result'">
    <div class="delete-modal">
        <p>{{process_result}}</p>
        <div class="delete-action">
            <button class="button-warned" ms-click="closeResult()">我知道了</button>
        </div>
    </div>
</div>