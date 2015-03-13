<h2 class="text-center pad-bottom pad-top">
    支付信息
</h2>
<p class="small-desc" ng-bind="local.payment.message" ng-hide="local.payment.has_info"></p>
<div class="states-section col-md-10 col-md-offset-4" ng-show="local.payment.has_info" id="payment_info">
    <div class="section-body view col-md-10">
        <table class="forms-table">
            <tr>
                <td class="view-title col-md-4">支付方式</td>
                <td class="view-body row-align-left" ng-bind="data.payment.payment_method"></td>
            </tr>
            <tr>
                <td class="view-title col-md-4">支付流水号</td>
                <td class="view-body row-align-left" ng-bind="data.payment.trade_id"></td>
            </tr>
            <tr>
                <td class="view-title col-md-4">订单金额</td>
                <td class="view-body-container">
                    <div class="view-row clearfix" data-ng-repeat="price in data.payment.ticket_price">
                        <div class="col-md-8 view-title"
                             ng-bind="price.ticket_name + ' x ' + price.quantity + ' x ' + price.ticket_price"></div>
                        <div class="view-body col-md-4" ng-bind="price.ticket_price_total"></div>
                    </div>
                    <div class="view-row border clearfix">
                        <div class="col-md-8 view-title">总金额</div>
                        <div class="view-body col-md-4" ng-bind="data.payment.product_price_total"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="view-title col-md-4">支付金额</td>
                <td class="view-body row-align-left" ng-bind="data.payment.pay_price"></td>
            </tr>
            <tr ng-show="data.payment.coupon_info.length">
                <td class="view-title col-md-4">优惠券</td>
                <td class="view-body-container">
                    <div class="view-row" data-ng-repeat="coupon in data.payment.coupon_info">
                        <div class="col-md-8 view-title">
                            <a ng-href="{{ local.coupon_edit_url }}{{ coupon.coupon_id }}" ng-bind="coupon.code"
                               target="_blank"></a>
                        </div>
                        <div class="view-body" ng-bind="'￥' + +(coupon.amount)"></div>
                    </div>
                </td>
            </tr>
        </table>

        <div ng-show="data.payment.refund_info.refund_history.length" class="pad-top grid-bottom">
            <h2 class="text-center">退款信息</h2>

            <p class="small-desc" ng-bind="'此订单' + data.payment.refund_info.refund_type"></p>

            <table class="forms-table">
                <tr>
                    <td class="view-title col-md-4">退款金额</td>
                    <td class="view-body row-align-left"
                        ng-bind="data.payment.refund_info.refund_history[0].trade_total"></td>
                </tr>
                <tr>
                    <td class="view-title col-md-4">备注信息</td>
                    <td class="view-body row-align-left"
                        ng-bind="data.payment.refund_info.refund_history[0].comment"></td>
                </tr>
                <tr>
                    <td class="view-title col-md-4">退款原因</td>
                    <td class="view-body row-align-left"
                        ng-bind="data.payment.refund_info.refund_history[0].refund_reason_desc"></td>
                </tr>
                <tr>
                    <td class="view-title col-md-4">退款时间</td>
                    <td class="view-body row-align-left"
                        ng-bind="data.payment.refund_info.refund_history[0].trade_time"></td>
                </tr>
            </table>
        </div>
    </div>
</div>