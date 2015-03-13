<table class="forms-table">
    <tr class="customer-limit">
        <td class="view-title">优惠券类型</td>
        <td class="view-body-container pad-bottom">
            <div class="view-row customer_limit">
                <div class="view-title">是否限制多人使用</div>
                <div class="view-body"
                     ng-bind="data.coupon.user_limit == 0 ? '不限制' : '限制一个用户。用户ID为' + data.coupon.customer_id + '，邮箱为' + data.coupon.customer_email"></div>
            </div>
            <div class="view-row" ng-show="data.coupon.user_limit == 0">
                <div class="view-title">优惠券最多使用次数</div>
                <div class="view-body"
                     ng-bind="data.coupon.uses_total == 0 ? '不限制' : data.coupon.uses_total + '次'"></div>
            </div>
            <div class="view-row">
                <div class="view-title">单用户最多使用次数</div>
                <div class="view-body"
                     ng-bind="data.coupon.uses_customer == 0 ? '不限制' : data.coupon.uses_customer + '次'"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="view-title">订单最少金额</td>
        <td class="view-body-container pad-bottom">
            <div class="view-body" ng-bind="data.coupon.total == 0 ? '不限制' : data.coupon.total + ' RMB'"></div>
        </td>
    </tr>
    <tr>
        <td class="view-title">下单人数限制</td>
        <td class="view-body-container pad-bottom">
            <div class="view-row">
                <div class="view-title">订单包含最少人数</div>
                <div class="view-body"
                     ng-bind="data.coupon.product_min == 0 ? '不限制' : data.coupon.product_min + ' 人'"></div>
            </div>
            <div class="view-row">
                <div class="view-title">订单包含最多人数</div>
                <div class="view-body"
                     ng-bind="data.coupon.product_max == 0 ? '不限制' : data.coupon.product_max + ' 人'"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="view-title">使用商品限制</td>
        <td class="view-body-container pad-bottom">
            <div class="view-row">
                <div class="view-body" ng-if="data.coupon.limit_ids.length == 0">
                    不限制使用商品（全球券）
                </div>
                <div class="view-title" ng-if="data.coupon.valid_type == 1">
                    <em ng-if="data.coupon.limit_type == 1">可以</em>
                    <em ng-if="data.coupon.limit_type == 0">不可以</em>
                    使用的商品
                </div>
                <div class="view-title" ng-if="data.coupon.valid_type == 2">
                    <em ng-if="data.coupon.limit_type == 1">可以</em>
                    <em ng-if="data.coupon.limit_type == 0">不可以</em>
                    使用的城市
                </div>
                <div class="view-title" ng-if="data.coupon.valid_type == 3">
                    <em ng-if="data.coupon.limit_type == 1">可以</em>
                    <em ng-if="data.coupon.limit_type == 0">不可以</em>
                    使用的国家
                </div>
                <ol ng-hide="data.coupon.limit_ids.length == 0" class="grid-bottom">
                    <li ng-repeat="product in data.coupon.limit_ids" ng-bind="product.name"></li>
                </ol>
            </div>
        </td>
    </tr>
    <tr>
        <td class="view-title">登录限制</td>
        <td class="view-body-container pad-bottom">
            <div class="view-body" ng-bind="data.coupon.logged == 0 ? '不限制' : '限制'"></div>
        </td>
    </tr>
</table>